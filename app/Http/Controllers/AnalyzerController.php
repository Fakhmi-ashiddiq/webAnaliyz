<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use App\Services\WebAnalyzerService;
use App\Models\AnalysisReport;

class AnalyzerController extends Controller
{
    /**
     * Menampilkan halaman awal (form input)
     */
    public function index()
    {
        $reports = AnalysisReport::where('status', 'completed')
            ->orderByDesc('id')
            ->get();

        return view('analyzer.index', compact('reports'));
    }

    /**
     * Memproses URL: simpan laporan (status pending) lalu kembalikan id-nya.
     * Google PageSpeed dipanggil dari browser (tanpa beban timeout server),
     * lalu hasilnya dikirim kembali ke server lewat metode store().
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'strategy' => 'required|in:DESKTOP,MOBILE'
        ]);

        $url = $request->input('url');
        $strategy = $request->input('strategy');

        $report = AnalysisReport::create([
            'url' => $url,
            'status' => 'pending',
            'raw_api_data' => json_encode(['strategy' => $strategy]),
        ]);

        return response()->json(['report_id' => $report->id]);
    }

    /**
     * Menerima hasil Google PageSpeed dari browser, memanggil VirusTotal
     * (cepat), lalu menyimpan laporan final.
     */
    public function store(Request $request, $id, WebAnalyzerService $analyzerService)
    {
        $report = AnalysisReport::findOrFail($id);

        $request->validate([
            'pagespeed' => 'required|array',
        ]);

        $pageSpeedData = $request->input('pagespeed');
        $strategy = json_decode($report->raw_api_data, true)['strategy'] ?? 'DESKTOP';

        $performanceScore = isset($pageSpeedData['lighthouseResult']['categories']['performance']['score'])
                            ? round($pageSpeedData['lighthouseResult']['categories']['performance']['score'] * 100) : null;

        $seoScore = isset($pageSpeedData['lighthouseResult']['categories']['seo']['score'])
                    ? round($pageSpeedData['lighthouseResult']['categories']['seo']['score'] * 100) : null;

        $virusTotalData = null;
        try {
            $virusTotalData = $analyzerService->analyzeVirusTotal($report->url);
        } catch (\Exception $e) {
            $virusTotalData = null;
        }

        $httpInfo = null;
        try {
            $httpInfo = $analyzerService->fetchHttpInfo($report->url);
        } catch (\Exception $e) {
            $httpInfo = null;
        }

        $securityHeaders = [];
        try {
            $securityHeaders = $analyzerService->analyzeSecurityHeaders(
                $httpInfo['headers'] ?? [],
                $httpInfo['final_url'] ?? $report->url
            );
        } catch (\Exception $e) {
            $securityHeaders = [];
        }

        $technologies = [];
        try {
            $technologies = $analyzerService->detectTechnologies($report->url);
        } catch (\Exception $e) {
            $technologies = [];
        }

        $report->performance_score = $performanceScore;
        $report->seo_score = $seoScore;
        $report->malicious_votes = $virusTotalData['malicious_votes'] ?? null;
        $report->security_status = $virusTotalData['security_status'] ?? null;

        $report->raw_api_data = json_encode([
            'strategy' => $strategy,
            'pagespeed' => $pageSpeedData,
            'virustotal' => $virusTotalData['raw_data'] ?? null,
            'http_info' => $httpInfo,
            'security_headers' => $securityHeaders,
            'technologies' => $technologies,
        ]);

        $report->status = 'completed';
        $report->save();

        return response()->json(['ok' => true, 'result_url' => route('analyzer.result', $report->id)]);
    }

    /**
     * Menampilkan hasil analisis berdasarkan ID (Mencegah error saat refresh)
     */
    public function result($id)
    {
        $report = AnalysisReport::findOrFail($id);

        if ($report->status !== 'completed') {
            return redirect()->route('analyzer.index');
        }
        
        // Resolve Target IP Address directly via DNS Ping for UI
        $parsed_url = parse_url($report->url);
        $host = $parsed_url['host'] ?? '';
        $resolved_ip = $host ? gethostbyname($host) : 'N/A';
        if ($resolved_ip === $host) $resolved_ip = 'N/A';
        
        return view('analyzer.result', compact('report', 'resolved_ip'));
    }

    private function prepareReportData($report)
    {
        $data = json_decode($report->raw_api_data, true);
        
        // --- Performance Data ---
        $metrics = [];
        $recommendations = [];
        
        // --- Additional Performance Scores ---
        $scores = [
            'accessibility' => 0,
            'best_practices' => 0,
            'seo' => 0
        ];

        if (isset($data['pagespeed']['lighthouseResult']['categories'])) {
            $cats = $data['pagespeed']['lighthouseResult']['categories'];
            $scores['accessibility'] = isset($cats['accessibility']['score']) ? round($cats['accessibility']['score'] * 100) : 'N/A';
            $scores['best_practices'] = isset($cats['best-practices']['score']) ? round($cats['best-practices']['score'] * 100) : 'N/A';
            $scores['seo'] = isset($cats['seo']['score']) ? round($cats['seo']['score'] * 100) : 'N/A';
        }

        if (isset($data['pagespeed']['lighthouseResult'])) {
            $lh = $data['pagespeed']['lighthouseResult'];
            
            // Metrics
            if (isset($lh['audits'])) {
                $audits = $lh['audits'];
                $tti_raw = $audits['interactive']['displayValue'] ?? 'N/A';
                $tti_numeric = $audits['interactive']['numericValue'] ?? null;
                $tti_clean = 'N/A';
                
                if ($tti_numeric) {
                    $tti_clean = $tti_numeric >= 1000 ? round($tti_numeric / 1000, 1) . ' s' : round($tti_numeric) . ' ms';
                } elseif (preg_match('/([\d\.]+\s*(?:ms|s))/i', $tti_raw, $matches)) {
                    $tti_clean = $matches[1];
                }

                $metrics = [
                    'lcp' => $audits['largest-contentful-paint']['displayValue'] ?? 'N/A',
                    'fcp' => $audits['first-contentful-paint']['displayValue'] ?? 'N/A',
                    'cls' => $audits['cumulative-layout-shift']['displayValue'] ?? 'N/A',
                    'si' => $audits['speed-index']['displayValue'] ?? 'N/A',
                    'tbt' => $audits['total-blocking-time']['displayValue'] ?? 'N/A',
                    'tti' => $tti_clean
                ];
                
                // Recommendations (Top Issues & Opportunities)
                foreach ($audits as $key => $audit) {
                    if (isset($audit['details']['type']) && $audit['details']['type'] === 'opportunity' && isset($audit['score']) && $audit['score'] !== 1 && $audit['score'] !== null) {
                        $recommendations[] = [
                            'title' => $audit['title'],
                            'description' => strip_tags(str_replace(['[', ']'], '', preg_replace('/\(http.*?\)/', '', $audit['description']))),
                            'savings' => $audit['displayValue'] ?? ''
                        ];
                    }
                    if (isset($audit['details']['type']) && $audit['details']['type'] === 'table' && isset($audit['score']) && $audit['score'] < 0.9 && $audit['score'] !== null) {
                        if (!in_array($audit['title'], array_column($recommendations, 'title'))) {
                           $recommendations[] = [
                                'title' => $audit['title'],
                                'description' => strip_tags(str_replace(['[', ']'], '', preg_replace('/\(http.*?\)/', '', $audit['description']))),
                                'savings' => ''
                            ];
                        }
                    }
                }
            }
        }
        
        // --- Security Data ---
        $security_vendors = [];
        
        // Resolve Target IP Address directly via DNS Ping
        $parsed_url = parse_url($report->url);
        $host = $parsed_url['host'] ?? '';
        $resolved_ip = $host ? gethostbyname($host) : 'N/A';
        // Fallback to N/A if gethostbyname fails (returns the hostname itself)
        if ($resolved_ip === $host) $resolved_ip = 'N/A';

        $security_details = [
            'ip' => $resolved_ip,
            'status_code' => 'N/A',
            'categories' => []
        ];

        $httpInfo = $data['http_info'] ?? null;

        if (isset($data['virustotal']['data']['attributes'])) {
            $attr = $data['virustotal']['data']['attributes'];
            
            // Vendors
            if (isset($attr['last_analysis_results'])) {
                foreach ($attr['last_analysis_results'] as $engine => $res) {
                    if ($res['category'] === 'malicious' || $res['category'] === 'suspicious') {
                        $security_vendors[] = $engine . ' (' . ucfirst($res['category']) . ')';
                    }
                }
            }
            
            // Status Code
            if (isset($attr['last_http_response_code'])) {
                $security_details['status_code'] = $attr['last_http_response_code'];
            }
            
            // Categories
            if (isset($attr['categories'])) {
                $security_details['categories'] = array_values($attr['categories']);
            }
        }

        // Fallback: status code asli dari fetch HTTP langsung (data real situs target)
        if ($security_details['status_code'] === 'N/A' && isset($httpInfo['status_code'])) {
            $security_details['status_code'] = $httpInfo['status_code'];
        }

        // --- Security Headers (HSTS, CSP, XFO, XCTO, Referrer-Policy) ---
        $security_headers_data = $data['security_headers'] ?? null;
        $security_headers_score = $security_headers_data['score'] ?? null;
        $security_headers_items = $security_headers_data['items'] ?? [];
        $security_headers_recs = $security_headers_data['recommendations'] ?? [];
        
        // --- Generate Reasons ---
        $performance_reason = '';
        if ($report->performance_score >= 90) {
            $performance_reason = "Grade A (Sangat Baik) ini didapatkan karena metrik utama tercapai dengan baik (LCP: " . ($metrics['lcp'] ?? 'N/A') . " dan TBT: " . ($metrics['tbt'] ?? 'N/A') . "). Stabilitas visual (CLS: " . ($metrics['cls'] ?? 'N/A') . ") sangat terjaga. Sistem mencatat situs merespons instruksi tanpa jeda berarti.";
        } elseif ($report->performance_score >= 50) {
            $performance_reason = "Grade B (Menengah / Perlu Perbaikan) ini diberikan karena sistem mencatat performa menengah dengan hambatan pada muat visual atau interaksi. Metrik pemblokiran (TBT) berada di " . ($metrics['tbt'] ?? 'N/A') . " dan LCP tercatat " . ($metrics['lcp'] ?? 'N/A') . ". Optimasi skrip dan kompresi media sangat disarankan.";
        } else {
            $performance_reason = "Grade C (Buruk / Kritis) ini menandakan terdapat masalah kritikal pada waktu respons atau render halaman (LCP: " . ($metrics['lcp'] ?? 'N/A') . "). Proses JavaScript menghambat interaksi utama (TBT: " . ($metrics['tbt'] ?? 'N/A') . "). Server/hosting lambat atau bobot web terlalu besar membebani klien.";
        }
        
        $security_reason = '';
        if ($report->malicious_votes > 0) {
            $security_reason = "Status Berbahaya ini diberikan karena analisis mendeteksi bahwa " . $report->malicious_votes . " dari total mesin keamanan global menggolongkan URL target sebagai berbahaya (Malware/Phishing). Koneksi tidak disarankan tanpa perlindungan khusus.";
        } else {
            $security_reason = "Status Aman (Clean) ini diberikan karena pemindaian dari puluhan mesin keamanan tidak menemukan perilaku mencurigakan atau indikasi phishing. Trafik web berada dalam batas standar dan aman untuk diakses.";
        }
        
        return [
            'report' => $report,
            'metrics' => $metrics,
            'scores' => $scores,
            'recommendations' => $recommendations,
            'security_vendors' => $security_vendors,
            'security_details' => $security_details,
            'security_headers_data' => $security_headers_data,
            'security_headers_score' => $security_headers_score,
            'security_headers_items' => $security_headers_items,
            'security_headers_recs' => $security_headers_recs,
            'performance_reason' => $performance_reason,
            'security_reason' => $security_reason
        ];
    }

    /**
     * Mengekspor data laporan ke format PDF
     */
    public function exportPdf($id)
    {
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

        $report = AnalysisReport::findOrFail($id);

        if ($report->status !== 'completed') {
            return redirect()->route('analyzer.index');
        }

        $data = $this->prepareReportData($report);

        $pdf = Pdf::loadView('analyzer.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('WebAnalyzer-Report-' . $report->id . '.pdf');
    }

    /**
     * Mengekspor data laporan ke format Word (.docx)
     */
    public function exportWord($id)
    {
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

        $report = AnalysisReport::findOrFail($id);

        if ($report->status !== 'completed') {
            return redirect()->route('analyzer.index');
        }

        $data = $this->prepareReportData($report);
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);
        
        $section = $phpWord->addSection();
        
        // Title
        $section->addText('LAPORAN ANALISIS WEB', ['bold' => true, 'size' => 24, 'color' => '2c3e50'], ['alignment' => 'center']);
        $section->addText('Audit Performa & Keamanan Komprehensif', ['size' => 14, 'color' => '7f8c8d'], ['alignment' => 'center']);
        $section->addTextBreak(1);
        
        // Meta
        $section->addText('Target URL: ' . $report->url, ['bold' => true]);
        $section->addText('Tanggal Analisis: ' . $report->created_at->format('d F Y, H:i') . ' WIB');
        $section->addTextBreak(2);
        
        // Performance
        $section->addText('1. Ringkasan Eksekutif & Analisis Hasil Audit', ['bold' => true, 'size' => 16, 'color' => '2980b9']);
        $section->addTextBreak(1);
        $scoreLabel = 'N/A';
        if ($report->performance_score !== null) {
            if ($report->performance_score >= 90) $scoreLabel = 'A (Sangat Baik)';
            elseif ($report->performance_score >= 50) $scoreLabel = 'B (Menengah)';
            else $scoreLabel = 'C (Buruk)';
        }
        $section->addText('Skor Performa Utama: ' . ($report->performance_score ?? 'N/A') . ' / 100 - Grade: ' . $scoreLabel, ['bold' => true, 'size' => 14]);
        
        // Reason for Performance
        $section->addText($data['performance_reason'], ['italic' => true]);
        $section->addTextBreak(1);
        
        // Additional Scores
        $section->addText('Skor Aksesibilitas: ' . ($data['scores']['accessibility'] ?? 'N/A') . ' / 100', ['bold' => true]);
        $section->addText('Skor Best Practices: ' . ($data['scores']['best_practices'] ?? 'N/A') . ' / 100', ['bold' => true]);
        $section->addText('Skor SEO: ' . ($data['scores']['seo'] ?? 'N/A') . ' / 100', ['bold' => true]);
        $section->addTextBreak(1);
        
        // Security
        $section->addText('Status Keamanan: ' . ($report->malicious_votes > 0 ? 'BERBAHAYA (' . $report->malicious_votes . ' Deteksi)' : 'AMAN / CLEAN (0 Deteksi)'), ['bold' => true, 'size' => 14, 'color' => $report->malicious_votes > 0 ? 'c0392b' : '27ae60']);
        
        // Reason for Security
        $section->addText($data['security_reason'], ['italic' => true]);
        
        if (!empty($data['security_vendors'])) {
            $section->addText('Vendor Pendeteksi: ' . implode(', ', $data['security_vendors']), ['color' => 'c0392b']);
        }
        
        // Security Details
        $section->addText('Alamat IP Target: ' . $data['security_details']['ip'], ['color' => '555555']);
        $section->addText('Status Kode HTTP: ' . $data['security_details']['status_code'], ['color' => '555555']);
        
        if (!empty($data['security_details']['categories'])) {
            $catsArray = is_array($data['security_details']['categories']) ? array_slice($data['security_details']['categories'], 0, 3) : [];
            $section->addText('Kategori Web: ' . implode(', ', $catsArray), ['color' => '555555']);
        }
        $section->addTextBreak(2);

        // Metrics Table
        $section->addText('2. Detail Metrik Kunci (Core Web Vitals)', ['bold' => true, 'size' => 16, 'color' => '2980b9']);
        if (!empty($data['metrics'])) {
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
            $table->addRow();
            $table->addCell(4000, ['bgColor' => 'f4f6f7'])->addText('Nama Metrik', ['bold' => true]);
            $table->addCell(4000, ['bgColor' => 'f4f6f7'])->addText('Nilai (Hasil)', ['bold' => true]);
            
            $labels = [
                'lcp' => 'Largest Contentful Paint (LCP)',
                'fcp' => 'First Contentful Paint (FCP)',
                'cls' => 'Cumulative Layout Shift (CLS)',
                'si' => 'Speed Index',
                'tbt' => 'Total Blocking Time (TBT)',
                'tti' => 'Time to Interactive (TTI)'
            ];
            
            foreach ($data['metrics'] as $key => $val) {
                $table->addRow();
                $table->addCell(4000)->addText($labels[$key] ?? $key);
                $table->addCell(4000)->addText($val ?: 'Data tidak tersedia');
            }
        }
        $section->addTextBreak(2);
        
        // Recommendations
        $section->addText('3. Top Issues & Rekomendasi Perbaikan', ['bold' => true, 'size' => 16, 'color' => '2980b9']);
        if (empty($data['recommendations'])) {
            $section->addText('Luar biasa! Web ini sudah sangat optimal. Tidak ada masalah kritis yang mendesak untuk diperbaiki.', ['bold' => true, 'color' => '27ae60']);
        } else {
            $section->addText('Berdasarkan kegagalan audit Lighthouse, berikut adalah isu dan peluang optimasi prioritas:', ['size' => 10]);
            $section->addTextBreak(1);
            foreach ($data['recommendations'] as $rec) {
                $title = $rec['title'] . ($rec['savings'] ? ' (Potensi: ' . $rec['savings'] . ')' : '');
                $section->addText('• ' . $title, ['bold' => true, 'color' => 'd35400']);
                $section->addText('  ' . $rec['description'], ['color' => '555555']);
                $section->addTextBreak(1);
            }
        }
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'WebAnalyzer-Report-' . $report->id . '.docx';

        $tempPath = tempnam(sys_get_temp_dir(), 'webanalyzer') . '.docx';
        $objWriter->save($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }
}