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

        // Tunggu hasil analisis (maksimal ~4x2 detik agar tetap dalam batas waktu request)
        for ($i = 0; $i < 4; $i++) {
            sleep(2);

            $report = Http::withHeaders([
                'x-apikey' => $apiKey,
            ])->timeout(20)->get("https://www.virustotal.com/api/v3/analyses/{$analysisId}");

            if ($report->successful()) {
                $data = $report->json();

                $maliciousVotes = $data['data']['attributes']['stats']['malicious'] ?? 0;
                $securityStatus = $maliciousVotes > 0 ? 'Malicious/Bahaya' : 'Safe/Aman';

                return [
                    'malicious_votes' => $maliciousVotes,
                    'security_status' => $securityStatus,
                    'raw_data' => $data,
                ];
            }
        }

        return null;
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
