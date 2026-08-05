<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebAnalyzerService
{
    /**
     * Menganalisis menggunakan Google PageSpeed API (Mendukung Mobile & Desktop)
     */
    public function analyzePageSpeed(string $url, string $strategy = 'DESKTOP')
    {
        $apiKey = config('services.google_pagespeed.key');
        $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed";

        try {
            $queryString = http_build_query([
                'url' => $url,
                'key' => $apiKey,
                'strategy' => $strategy
            ]) . '&category=PERFORMANCE&category=SEO&category=ACCESSIBILITY&category=BEST_PRACTICES';

            // Memanggil API Google dengan penambahan timeout 120 detik
            $response = Http::timeout(120)->get($apiUrl . '?' . $queryString);

            if ($response->successful()) {
                $data = $response->json();
                
                $performanceScore = isset($data['lighthouseResult']['categories']['performance']['score']) 
                                    ? $data['lighthouseResult']['categories']['performance']['score'] * 100 : 0;
                
                $seoScore = isset($data['lighthouseResult']['categories']['seo']['score']) 
                            ? $data['lighthouseResult']['categories']['seo']['score'] * 100 : 0;

                return [
                    'performance_score' => $performanceScore,
                    'seo_score' => $seoScore,
                    'raw_data' => $data
                ];
            }
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Google API Error ({$strategy}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Menganalisis Keamanan menggunakan VirusTotal API v3
     */
    public function analyzeVirusTotal(string $url)
    {
        $apiKey = config('services.virustotal.key');

        // VirusTotal v3 membutuhkan URL yang di-encode menggunakan base64url format
        $urlId = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');

        try {
            $response = Http::withHeaders([
                'x-apikey' => $apiKey,
            ])->timeout(20)->get("https://www.virustotal.com/api/v3/urls/{$urlId}");

            // Jika URL belum pernah dipindai (404), submit dulu lalu ambil hasilnya.
            if ($response->status() === 404) {
                return $this->submitAndWaitForUrl($url, $apiKey);
            }

            if ($response->successful()) {
                $data = $response->json();

                $maliciousVotes = $data['data']['attributes']['last_analysis_stats']['malicious'] ?? 0;
                $securityStatus = $maliciousVotes > 0 ? 'Malicious/Bahaya' : 'Safe/Aman';

                return [
                    'malicious_votes' => $maliciousVotes,
                    'security_status' => $securityStatus,
                    'raw_data' => $data,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("VirusTotal API Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Men-submit URL baru ke VirusTotal lalu menunggu hasil analisisnya.
     * Setelah analisis selesai, data yang disimpan diambil dari endpoint URL
     * report agar strukturnya sama dengan jalur normal (last_analysis_stats).
     */
    private function submitAndWaitForUrl(string $url, string $apiKey)
    {
        $submit = Http::asForm()->withHeaders([
            'x-apikey' => $apiKey,
        ])->timeout(20)->post("https://www.virustotal.com/api/v3/urls", [
            'url' => $url,
        ]);

        if (!$submit->successful()) {
            return null;
        }

        $analysisId = $submit->json('data.id');

        if (!$analysisId) {
            return null;
        }

        // Tunggu analisis selesai (maksimal ~4x2 detik agar tetap dalam batas waktu request)
        $done = false;
        for ($i = 0; $i < 4; $i++) {
            sleep(2);

            $report = Http::withHeaders([
                'x-apikey' => $apiKey,
            ])->timeout(20)->get("https://www.virustotal.com/api/v3/analyses/{$analysisId}");

            if ($report->successful() && $report->json('data.attributes.status') === 'completed') {
                $done = true;
                break;
            }
        }

        if (!$done) {
            return null;
        }

        // Ambil URL report (format sama seperti jalur normal)
        $urlId = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');

        $response = Http::withHeaders([
            'x-apikey' => $apiKey,
        ])->timeout(20)->get("https://www.virustotal.com/api/v3/urls/{$urlId}");

        if ($response->successful()) {
            $data = $response->json();

            $maliciousVotes = $data['data']['attributes']['last_analysis_stats']['malicious'] ?? 0;
            $securityStatus = $maliciousVotes > 0 ? 'Malicious/Bahaya' : 'Safe/Aman';

            return [
                'malicious_votes' => $maliciousVotes,
                'security_status' => $securityStatus,
                'raw_data' => $data,
            ];
        }

        return null;
    }

    /**
     * Mendeteksi teknologi website (mirip Wappalyzer) dari header respons
     * dan HTML halaman target.
     */
    public function detectTechnologies(string $url): array
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36';

        try {
            $response = Http::timeout(25)
                ->withHeaders(['User-Agent' => $userAgent])
                ->withOptions(['stream' => true])
                ->get($url);
        } catch (\Exception $e) {
            Log::error("Tech Detect Error: " . $e->getMessage());
            return [];
        }

        // Baca maksimal 3MB HTML agar aman untuk halaman besar
        // (loop per-chunk karena respons chunked belum tentu penuh saat read pertama)
        $html = '';
        try {
            $stream = $response->toPsrResponse()->getBody();
            while (!$stream->eof() && strlen($html) < 3000000) {
                $chunk = $stream->read(65536);
                if ($chunk === '' || $chunk === false) {
                    break;
                }
                $html .= $chunk;
            }
            $stream->close();
        } catch (\Throwable $e) {
            $html = (string) $response->body();
        }

        $lower = strtolower($html);
        $setCookie = (string) $response->header('Set-Cookie');
        $serverRaw = (string) $response->header('Server');
        $server = strtolower($serverRaw);
        $poweredBy = strtolower((string) $response->header('X-Powered-By'));
        $via = strtolower((string) $response->header('Via'));

        $detected = [];

        $add = function (string $name, string $version = '-', string $category = '') use (&$detected) {
            foreach ($detected as $i => $d) {
                if (strtolower($d['name']) === strtolower($name)) {
                    if ($version !== '-' && $d['version'] === '-') {
                        $detected[$i]['version'] = $version;
                    }
                    return;
                }
            }
            $detected[] = ['name' => $name, 'version' => $version, 'category' => $category];
        };

        $ver = function (string $pattern, string $subject) {
            if (preg_match($pattern, $subject, $m) && preg_match('/\d/', $m[1])) {
                return $m[1];
            }
            return '-';
        };

        // --- Web Server / Infra (dari header) ---
        if ($response->header('CF-Ray') || str_contains($server, 'cloudflare')) {
            $add('Cloudflare', '-', 'CDN');
        }
        if (str_contains($server, 'cloudfront') || $response->header('X-Amz-Cf-Id') || str_contains($via, 'cloudfront')) {
            $add('Amazon CloudFront', '-', 'CDN');
        }
        if (str_contains($server, 'nginx')) {
            $add('Nginx', $ver('/nginx\/([\d.]+)/i', $serverRaw), 'Web Server');
        }
        if (str_contains($server, 'apache')) {
            $add('Apache', $ver('/apache\/([\d.]+)/i', $serverRaw), 'Web Server');
        }
        if (str_contains($server, 'litespeed')) {
            $add('LiteSpeed', '-', 'Web Server');
        }
        if (str_contains($server, 'microsoft-iis')) {
            $add('IIS', $ver('/microsoft-iis\/([\d.]+)/i', $serverRaw), 'Web Server');
        }
        if (str_contains($poweredBy, 'php')) {
            $add('PHP', $ver('/php\/([\d.]+)/i', (string) $response->header('X-Powered-By')), 'Programming Language');
        }
        if (str_contains($poweredBy, 'express')) {
            $add('Express', '-', 'Web Framework');
        }
        if (str_contains($poweredBy, 'node.js')) {
            $add('Node.js', '-', 'Programming Language');
        }
        if (str_contains($poweredBy, 'next.js')) {
            $add('Next.js', '-', 'Web Framework');
        }
        if (str_contains($poweredBy, 'asp.net')) {
            $add('ASP.NET', '-', 'Web Framework');
        }

        // --- PHP framework via cookie ---
        if (str_contains($setCookie, 'ci_session') || str_contains($setCookie, 'csrf_cookie_name')) {
            $add('CodeIgniter', '-', 'PHP Framework');
            $add('PHP', '-', 'Programming Language');
        }
        if (str_contains($setCookie, 'laravel_session') || str_contains($setCookie, 'XSRF-TOKEN') || preg_match('/csrf-token/i', $html)) {
            $add('Laravel', '-', 'PHP Framework');
        }
        if (str_contains($setCookie, 'CAKEPHP')) {
            $add('CakePHP', '-', 'PHP Framework');
        }
        if (str_contains($setCookie, 'PHPSESSID')) {
            $add('PHP', '-', 'Programming Language');
        }

        // --- CMS ---
        if (preg_match('/wp-content|wp-includes/i', $html) || preg_match('/<meta[^>]+name=["\']generator["\'][^>]*content=["\'][^"\']*WordPress/i', $html)) {
            $add('WordPress', $ver('/content=["\'][^"\']*WordPress\s*([\d.]+)/i', $html), 'CMS');
        }
        if (str_contains($lower, 'elementor')) {
            $add('Elementor', '-', 'Page Builder');
        }
        if (preg_match('/woocommerce/i', $html)) {
            $add('WooCommerce', '-', 'E-Commerce');
        }
        if (preg_match('/yoast/i', $html)) {
            $add('Yoast SEO', '-', 'SEO');
        }
        if (preg_match('/\/media\/system\/js\/|Joomla/i', $html)) {
            $add('Joomla', $ver('/content=["\'][^"\']*Joomla!\s*([\d.]+)/i', $html), 'CMS');
        }
        if (preg_match('/drupal/i', $html) || $response->header('X-Drupal-Cache')) {
            $add('Drupal', '-', 'CMS');
        }
        if (str_contains($lower, 'cdn.shopify.com')) {
            $add('Shopify', '-', 'E-Commerce');
        }
        if (preg_match('/mage\.cookies|Mage\.js/i', $html)) {
            $add('Magento', '-', 'E-Commerce');
        }
        if (str_contains($lower, 'prestashop')) {
            $add('PrestaShop', '-', 'E-Commerce');
        }
        if (str_contains($lower, 'static.parastorage.com') || str_contains($lower, 'x-wix')) {
            $add('Wix', '-', 'Website Builder');
        }

        // --- JavaScript libraries ---
        if (preg_match('/jquery/i', $html)) {
            $add('jQuery', $ver('/jquery(?:[\/@]|(?:\.min)?\.js)[^\'" ]*?([\d.]+)/i', $html), 'JavaScript Library');
        }
        if (preg_match('/bootstrap/i', $html)) {
            $add('Bootstrap', $ver('/bootstrap(?:[\/@]|\.bundle)[^\'" ]*?([\d.]+)/i', $html), 'CSS Framework');
        }
        if (preg_match('/data-reactroot|__react|react(?:[\/@.]|\b)/i', $html)) {
            $add('React', $ver('/react(?:[\/@]|(?:\.min)?\.js)[^\'" ]*?([\d.]+)/i', $html), 'JavaScript Framework');
        }
        if (preg_match('/data-v-[0-9a-f]+|__vue__|vue(?:[\/@.]|\b)/i', $html)) {
            $add('Vue.js', $ver('/vue(?:[\/@]|(?:\.min)?\.js)[^\'" ]*?([\d.]+)/i', $html), 'JavaScript Framework');
        }
        if (preg_match('/ng-app|angular\.js|angular(?:[\/@]|\b)/i', $html)) {
            $add('Angular', '-', 'JavaScript Framework');
        }
        if (preg_match('/alpine(?:\.min)?\.js|@alpinejs/i', $html)) {
            $add('Alpine.js', '-', 'JavaScript Library');
        }
        if (preg_match('/htmx/i', $html)) {
            $add('htmx', '-', 'JavaScript Library');
        }
        if (preg_match('/gsap/i', $html)) {
            $add('GSAP', '-', 'Animation Library');
        }
        if (preg_match('/chart\.js|chartjs/i', $html)) {
            $add('Chart.js', $ver('/chart\.js[^\'" ]*?([\d.]+)/i', $html), 'Charts');
        }
        if (preg_match('/select2/i', $html)) {
            $add('Select2', $ver('/select2[^\'" ]*?([\d.]+-?rc?\.?\d*)/i', $html), 'JavaScript Library');
        }
        if (preg_match('/sweetalert2/i', $html)) {
            $add('SweetAlert2', $ver('/sweetalert2(?:[\/@]|\.min\.js)[^\'" ]*?(\d+)/i', $html), 'JavaScript Library');
        }
        if (preg_match('/tinymce/i', $html)) {
            $add('TinyMCE', $ver('/tinymce\/(\d+)(?:\.\d+)*\//i', $html), 'Rich Text Editor');
        }
        if (preg_match('/ckeditor/i', $html)) {
            $add('CKEditor', '-', 'Rich Text Editor');
        }
        if (preg_match('/axios/i', $html)) {
            $add('Axios', '-', 'HTTP Client');
        }
        if (preg_match('/moment(?:\.min)?\.js/i', $html)) {
            $add('Moment.js', $ver('/moment(?:\.min)?\.js[^\'" ]*?([\d.]+)/i', $html), 'JavaScript Library');
        }
        if (preg_match('/lodash/i', $html)) {
            $add('Lodash', '-', 'JavaScript Library');
        }
        if (preg_match('/slick(?:\.min)?\.(?:js|css)/i', $html)) {
            $add('Slick Carousel', '-', 'JavaScript Library');
        }
        if (preg_match('/owl\.carousel/i', $html)) {
            $add('Owl Carousel', '-', 'JavaScript Library');
        }

        // --- CSS & fonts ---
        if (preg_match('/font-?awesome|fontawesome/i', $html)) {
            $add('Font Awesome', $ver('/font-?awesome[^\'" ]*?([\d.]+)/i', $html), 'Icon Font');
        }
        if (preg_match('/bootstrap-icons/i', $html)) {
            $add('Bootstrap Icons', '-', 'Icon Font');
        }
        if (preg_match('/material-icons/i', $html)) {
            $add('Material Icons', '-', 'Icon Font');
        }
        if (preg_match('/fonts\.googleapis/i', $html)) {
            $add('Google Fonts', '-', 'Font Script');
        }
        if (preg_match('/tailwind(?:css)?/i', $html)) {
            $add('Tailwind CSS', $ver('/tailwindcss[^\'" ]*?([\d.]+)/i', $html), 'CSS Framework');
        }

        // --- Analytics ---
        if (preg_match('/gtag\/js\?id=|google-analytics\.com\/analytics|ga\.js/i', $html)) {
            $add('Google Analytics', $ver('/id=(G-[A-Z0-9]+)/i', $html), 'Analytics');
        }
        if (preg_match('/googletagmanager\.com\/gtm/i', $html)) {
            $add('Google Tag Manager', '-', 'Analytics');
        }
        if (preg_match('/facebook\.com\/tr|fbevents/i', $html)) {
            $add('Facebook Pixel', '-', 'Analytics');
        }
        if (preg_match('/static\.hotjar\.com|hotjar/i', $html)) {
            $add('Hotjar', '-', 'Analytics');
        }
        if (preg_match('/matomo|piwik/i', $html)) {
            $add('Matomo', '-', 'Analytics');
        }
        if (preg_match('/mc\.yandex|yandex_metrika/i', $html)) {
            $add('Yandex Metrika', '-', 'Analytics');
        }

        // --- CDN ---
        if (str_contains($lower, 'cdn.jsdelivr')) {
            $add('jsDelivr', '-', 'CDN');
        }
        if (str_contains($lower, 'cdnjs.cloudflare')) {
            $add('cdnjs', '-', 'CDN');
        }
        if (str_contains($lower, 'unpkg.com')) {
            $add('unpkg', '-', 'CDN');
        }
        if (preg_match('/__FIREBASE__|firebase/i', $html)) {
            $add('Firebase', '-', 'Cloud');
        }
        if (preg_match('/vercel|_vercel/i', $html)) {
            $add('Vercel', '-', 'Cloud');
        }
        if (str_contains($lower, 'netlify')) {
            $add('Netlify', '-', 'Cloud');
        }

        // Map icon (slug Simple Icons) untuk tiap teknologi
        $icons = [
            'Apache' => 'apache',
            'Nginx' => 'nginx',
            'LiteSpeed' => 'litespeed',
            'IIS' => 'microsoft-iis',
            'Cloudflare' => 'cloudflare',
            'Amazon CloudFront' => 'amazonwebservices',
            'PHP' => 'php',
            'Express' => 'express',
            'Node.js' => 'nodedotjs',
            'Next.js' => 'nextdotjs',
            'ASP.NET' => 'dotnet',
            'CodeIgniter' => 'codeigniter',
            'Laravel' => 'laravel',
            'CakePHP' => 'cakephp',
            'WordPress' => 'wordpress',
            'Elementor' => 'elementor',
            'WooCommerce' => 'woocommerce',
            'Yoast SEO' => 'yoast',
            'Joomla' => 'joomla',
            'Drupal' => 'drupal',
            'Shopify' => 'shopify',
            'Magento' => 'magento',
            'PrestaShop' => 'prestashop',
            'Wix' => 'wix',
            'jQuery' => 'jquery',
            'Bootstrap' => 'bootstrap',
            'React' => 'react',
            'Vue.js' => 'vuedotjs',
            'Angular' => 'angular',
            'Alpine.js' => 'alpinejs',
            'htmx' => 'htmx',
            'GSAP' => 'gsap',
            'Chart.js' => 'chartdotjs',
            'Select2' => 'select2',
            'SweetAlert2' => 'sweetalert2',
            'TinyMCE' => 'tinymce',
            'CKEditor' => 'ckeditor',
            'Axios' => 'axios',
            'Moment.js' => 'moment',
            'Lodash' => 'lodash',
            'Slick Carousel' => 'slick',
            'Owl Carousel' => 'owlcarousel',
            'Font Awesome' => 'fontawesome',
            'Bootstrap Icons' => 'bootstrap',
            'Material Icons' => 'materialdesign',
            'Google Fonts' => 'googlefonts',
            'Tailwind CSS' => 'tailwindcss',
            'Google Analytics' => 'googleanalytics',
            'Google Tag Manager' => 'googletagmanager',
            'Facebook Pixel' => 'facebook',
            'Hotjar' => 'hotjar',
            'Matomo' => 'matomo',
            'Yandex Metrika' => 'yandex',
            'jsDelivr' => 'jsdelivr',
            'cdnjs' => 'cloudflare',
            'unpkg' => 'unpkg',
            'Firebase' => 'firebase',
            'Vercel' => 'vercel',
            'Netlify' => 'netlify',
        ];

        foreach ($detected as &$d) {
            $d['icon'] = $icons[$d['name']] ?? '';
        }

        return $detected;
    }

    /**
     * Mengambil status code, content-type, seluruh header respons, dan URL
     * akhir (setelah redirect) dari website target sebagai pelengkap data
     * VirusTotal (yang kadang belum terisi untuk URL baru).
     * Prioritas GET (authoritative) karena banyak server memblokir/menyalahi HEAD.
     */
    public function fetchHttpInfo(string $url): ?array
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36';

        try {
            // Streaming GET: hanya baca header, tanpa men-download body penuh.
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => $userAgent])
                ->withOptions(['stream' => true])
                ->get($url);

            $status = $response->status();
            $contentType = $response->header('Content-Type') ?: 'N/A';
            $headers = $this->normalizeHeaders($response);
            $finalUrl = $response->effectiveUri() ? (string) $response->effectiveUri() : $url;

            // Tutup body stream agar koneksi tidak menggantung
            try {
                $response->toPsrResponse()->getBody()->close();
            } catch (\Throwable $e) {
                // abaikan
            }

            return [
                'status_code' => $status,
                'content_type' => $contentType,
                'headers' => $headers,
                'final_url' => $finalUrl,
            ];
        } catch (\Exception $e) {
            Log::error("HTTP Info GET Error: " . $e->getMessage());
        }

        // Fallback: HEAD bila GET gagal di level transport
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => $userAgent])
                ->head($url);

            return [
                'status_code' => $response->status(),
                'content_type' => $response->header('Content-Type') ?: 'N/A',
                'headers' => $this->normalizeHeaders($response),
                'final_url' => $url,
            ];
        } catch (\Exception $e) {
            Log::error("HTTP Info Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Menganalisis Security Headers (HSTS, CSP, X-Frame-Options,
     * X-Content-Type-Options, Referrer-Policy) dari header respons yang sudah
     * dinormalisasi, lalu menghitung skor keamanan (0-100).
     *
     * @param  array<string, mixed>  $headers
     * @param  string  $finalUrl  URL akhir setelah redirect (untuk deteksi HTTPS)
     */
    public function analyzeSecurityHeaders(array $headers, string $finalUrl): array
    {
        $get = function (string $name) use ($headers): ?string {
            $value = $headers[$name] ?? null;
            return ($value !== null && trim((string) $value) !== '') ? trim((string) $value) : null;
        };

        $https = str_starts_with(strtolower($finalUrl), 'https://');

        $items = [];
        $total = 0;

        // --- HTTPS ---
        $pts = $https ? 25 : 0;
        $total += $pts;
        $items['https'] = [
            'label' => 'HTTPS / SSL',
            'present' => $https,
            'points' => $pts,
            'max' => 25,
            'value' => $https ? 'Koneksi menggunakan HTTPS' : 'URL tidak menggunakan HTTPS',
            'status' => $https ? 'good' : 'missing',
            'note' => $https
                ? 'Trafik dienkripsi (TLS) dan menjadi fondasi untuk HSTS.'
                : 'Aktifkan HTTPS untuk mengenkripsi komunikasi pengguna.',
        ];

        // --- HSTS (Strict-Transport-Security) ---
        $hstsRaw = $get('strict-transport-security');
        $hstsPoints = 0;
        $hstsStatus = 'missing';
        $hstsNote = 'Tambahkan Strict-Transport-Security dengan max-age minimal 15552000 (180 hari).';
        $hstsValue = 'Tidak tersedia';

        if ($hstsRaw !== null) {
            $hstsValue = $hstsRaw;

            $maxAge = 0;
            if (preg_match('/max-age\s*=\s*(\d+)/i', $hstsRaw, $m)) {
                $maxAge = (int) $m[1];
            }
            $includeSub = (bool) preg_match('/includeSubDomains/i', $hstsRaw);
            $preload = (bool) preg_match('/preload/i', $hstsRaw);

            if ($maxAge >= 15552000) {
                $hstsPoints = 15;
                $hstsStatus = 'good';
                $hstsNote = 'Penerapan HSTS baik: max-age=' . $maxAge . 's'
                    . ($includeSub ? ', includeSubDomains' : '')
                    . ($preload ? ', preload' : '') . '.';
            } elseif ($maxAge > 0) {
                $hstsPoints = 8;
                $hstsStatus = 'partial';
                $hstsNote = 'HSTS aktif namun max-age=' . $maxAge . 's lebih pendek dari 180 hari (15552000s). Tingkatkan max-age.';
            } else {
                $hstsPoints = 5;
                $hstsStatus = 'partial';
                $hstsNote = 'HSTS terpasang tanpa max-age yang valid. Tambahkan max-age minimal 15552000.';
            }
        }
        $total += $hstsPoints;
        $items['hsts'] = [
            'label' => 'Strict-Transport-Security (HSTS)',
            'present' => $hstsRaw !== null,
            'points' => $hstsPoints,
            'max' => 15,
            'value' => $hstsValue,
            'status' => $hstsStatus,
            'note' => $hstsNote,
        ];

        // --- CSP (Content-Security-Policy) ---
        $cspRaw = $get('content-security-policy');
        $cspPoints = 0;
        $cspStatus = 'missing';
        $cspNote = 'Tambahkan Content-Security-Policy untuk mencegah XSS dan pemuatan sumber tidak tepercaya.';
        $cspValue = 'Tidak tersedia';

        if ($cspRaw !== null) {
            $cspValue = $cspRaw;
            $hardened = (bool) preg_match('/(?:^|;)\s*(?:default-src|object-src|frame-ancestors|base-uri)\s+/i', $cspRaw);
            if ($hardened) {
                $cspPoints = 20;
                $cspStatus = 'good';
                $cspNote = 'CSP terpasang dan diperkuat dengan direktif kunci (default-src/object-src/frame-ancestors/base-uri).';
            } else {
                $cspPoints = 12;
                $cspStatus = 'partial';
                $cspNote = 'CSP terpasang namun belum diperkuat. Tambahkan direktif seperti default-src, object-src \'none\', base-uri \'none\', frame-ancestors.';
            }
        } else {
            $cspRo = $get('content-security-policy-report-only');
            if ($cspRo !== null) {
                $cspPoints = 6;
                $cspStatus = 'partial';
                $cspNote = 'Hanya ada Content-Security-Policy-Report-Only (mode laporan). Terapkan sebagai policy penuh agar benar-benar aktif.';
                $cspValue = $cspRo;
            }
        }
        $total += $cspPoints;
        $items['csp'] = [
            'label' => 'Content-Security-Policy (CSP)',
            'present' => $cspRaw !== null,
            'points' => $cspPoints,
            'max' => 20,
            'value' => $cspValue,
            'status' => $cspStatus,
            'note' => $cspNote,
        ];

        // --- X-Frame-Options ---
        $xfoRaw = $get('x-frame-options');
        $xfoPoints = 0;
        $xfoStatus = 'missing';
        $xfoNote = 'Tambahkan X-Frame-Options dengan nilai DENY atau SAMEORIGIN untuk mencegah clickjacking.';
        $xfoValue = 'Tidak tersedia';

        if ($xfoRaw !== null) {
            $xfoValue = $xfoRaw;
            $xfoUpper = strtoupper($xfoRaw);
            if (in_array($xfoUpper, ['DENY', 'SAMEORIGIN'], true)) {
                $xfoPoints = 10;
                $xfoStatus = 'good';
                $xfoNote = 'X-Frame-Options = ' . $xfoUpper . ' (mencegah pemuatan halaman di frame pihak lain).';
            } else {
                $xfoPoints = 5;
                $xfoStatus = 'partial';
                $xfoNote = 'Nilai "' . $xfoRaw . '" sudah usang (ALLOW-FROM tidak didukung banyak browser). Gunakan DENY/SAMEORIGIN atau frame-ancestors pada CSP.';
            }
        }
        $total += $xfoPoints;
        $items['xfo'] = [
            'label' => 'X-Frame-Options',
            'present' => $xfoRaw !== null,
            'points' => $xfoPoints,
            'max' => 10,
            'value' => $xfoValue,
            'status' => $xfoStatus,
            'note' => $xfoNote,
        ];

        // --- X-Content-Type-Options ---
        $xctoRaw = $get('x-content-type-options');
        $xctoPoints = 0;
        $xctoStatus = 'missing';
        $xctoNote = 'Tambahkan X-Content-Type-Options: nosniff untuk mencegah MIME sniffing.';
        $xctoValue = 'Tidak tersedia';

        if ($xctoRaw !== null) {
            $xctoValue = $xctoRaw;
            if (strtolower($xctoRaw) === 'nosniff') {
                $xctoPoints = 10;
                $xctoStatus = 'good';
                $xctoNote = 'X-Content-Type-Options: nosniff aktif (browser tidak menebak MIME type).';
            } else {
                $xctoPoints = 5;
                $xctoStatus = 'partial';
                $xctoNote = 'Nilai harus "nosniff" agar browser tidak menebak MIME type.';
            }
        }
        $total += $xctoPoints;
        $items['xcto'] = [
            'label' => 'X-Content-Type-Options',
            'present' => $xctoRaw !== null,
            'points' => $xctoPoints,
            'max' => 10,
            'value' => $xctoValue,
            'status' => $xctoStatus,
            'note' => $xctoNote,
        ];

        // --- Referrer-Policy ---
        $rpRaw = $get('referrer-policy');
        $rpPoints = 0;
        $rpStatus = 'missing';
        $rpNote = 'Tambahkan Referrer-Policy, misal strict-origin-when-cross-origin, untuk mengontrol data referer yang dikirim browser.';
        $rpValue = 'Tidak tersedia';

        if ($rpRaw !== null) {
            $rpValue = $rpRaw;
            $safe = [
                'no-referrer',
                'same-origin',
                'strict-origin',
                'strict-origin-when-cross-origin',
                'no-referrer-when-downgrade',
            ];
            $rpLower = strtolower(explode(',', $rpRaw)[0]);
            if (in_array($rpLower, $safe, true)) {
                $rpPoints = 10;
                $rpStatus = 'good';
                $rpNote = 'Referrer-Policy = ' . $rpRaw . ' (nilai aman).';
            } else {
                $rpPoints = 5;
                $rpStatus = 'partial';
                $rpNote = 'Nilai "' . $rpRaw . '" berisiko membocorkan referer. Gunakan strict-origin-when-cross-origin atau yang lebih ketat.';
            }
        }
        $total += $rpPoints;
        $items['rp'] = [
            'label' => 'Referrer-Policy',
            'present' => $rpRaw !== null,
            'points' => $rpPoints,
            'max' => 10,
            'value' => $rpValue,
            'status' => $rpStatus,
            'note' => $rpNote,
        ];

        // --- Permissions-Policy ---
        $ppRaw = $get('permissions-policy');
        $ppPoints = 0;
        $ppStatus = 'missing';
        $ppNote = 'Tambahkan Permissions-Policy untuk membatasi penggunaan fitur browser sensitif (kamera, mikrofon, geolokasi, USB, dll.) oleh halaman dan iframe.';
        $ppValue = 'Tidak tersedia';

        if ($ppRaw !== null) {
            $ppValue = $ppRaw;
            // Fitur sensitif yang sebaiknya di-nonaktifkan/dibatasi
            $sensitive = [
                'geolocation',
                'camera',
                'microphone',
                'usb',
                'payment',
                'magnetometer',
                'gyroscope',
                'ambient-light-sensor',
                'battery',
                'fullscreen',
            ];
            $lowerPp = strtolower($ppRaw);
            $restricted = 0;
            foreach ($sensitive as $feature) {
                if (preg_match('/\b' . preg_quote($feature, '/') . '\s*=\s*(?:\(\)|(?:\(|\b)self\b)/', $lowerPp)) {
                    $restricted++;
                }
            }

            if ($restricted >= 3) {
                $ppPoints = 10;
                $ppStatus = 'good';
                $ppNote = 'Permissions-Policy terpasang dan membatasi fitur sensitif (' . $restricted . ' fitur: geolocation, camera, microphone, dll.).';
            } else {
                $ppPoints = 6;
                $ppStatus = 'partial';
                $ppNote = 'Permissions-Policy terpasang namun belum banyak membatasi. Nonaktifkan fitur sensitif seperti geolocation, camera, dan microphone (mis. geolocation=(), camera=(), microphone=()).';
            }
        }
        $total += $ppPoints;
        $items['pp'] = [
            'label' => 'Permissions-Policy',
            'present' => $ppRaw !== null,
            'points' => $ppPoints,
            'max' => 10,
            'value' => $ppValue,
            'status' => $ppStatus,
            'note' => $ppNote,
        ];

        $score = max(0, min(100, $total));

        $recommendations = [];
        foreach ($items as $item) {
            if ($item['status'] !== 'good') {
                $recommendations[] = $item['note'];
            }
        }

        return [
            'score' => $score,
            'https' => $https,
            'final_url' => $finalUrl,
            'headers' => $headers,
            'items' => $items,
            'recommendations' => array_values(array_unique($recommendations)),
        ];
    }

    /**
     * Menormalkan nama header respons menjadi lowercase agar mudah dicari.
     *
     * @return array<string, mixed>
     */
    private function normalizeHeaders($response): array
    {
        $normalized = [];

        foreach ($response->headers() as $name => $values) {
            $normalized[strtolower($name)] = is_array($values)
                ? implode(', ', $values)
                : (string) $values;
        }

        return $normalized;
    }

    /**
     * Mengambil skor performance & seo dari data mentah Google PageSpeed
     * yang dikirim oleh browser.
     */
    public function parsePageSpeedData(array $data): array
    {
        $performanceScore = isset($data['lighthouseResult']['categories']['performance']['score'])
            ? $data['lighthouseResult']['categories']['performance']['score'] * 100 : 0;

        $seoScore = isset($data['lighthouseResult']['categories']['seo']['score'])
            ? $data['lighthouseResult']['categories']['seo']['score'] * 100 : 0;

        return [
            'performance_score' => $performanceScore,
            'seo_score' => $seoScore,
        ];
    }
}
