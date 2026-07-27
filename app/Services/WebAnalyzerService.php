<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebAnalyzerService
{
    /**
     * Menganalisis Performa dan SEO menggunakan Google PageSpeed API
     */
    public function analyzePageSpeed(string $url)
    {
        $apiKey = env('GOOGLE_PAGESPEED_API_KEY');
        $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed";

        try {
            // Memanggil API Google
            $response = Http::timeout(60)->get($apiUrl, [
                'url' => $url,
                'key' => $apiKey,
                'category' => ['PERFORMANCE', 'SEO'],
                'strategy' => 'DESKTOP' // Bisa diubah ke 'MOBILE'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Mengambil nilai skor (Google mengembalikan nilai 0.0 - 1.0, jadi kita kali 100)
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
            Log::error("Google API Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Menganalisis Keamanan menggunakan VirusTotal API v3
     */
    public function analyzeVirusTotal(string $url)
    {
        $apiKey = env('VIRUSTOTAL_API_KEY');
        
        // VirusTotal v3 membutuhkan URL yang di-encode menggunakan base64url format
        $urlId = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');
        
        $apiUrl = "https://www.virustotal.com/api/v3/urls/{$urlId}";

        try {
            // Memanggil API VirusTotal dengan Header Auth
            $response = Http::withHeaders([
                'x-apikey' => $apiKey
            ])->timeout(60)->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                // Mengambil statistik deteksi malware/phising
                $maliciousVotes = $data['data']['attributes']['last_analysis_stats']['malicious'] ?? 0;
                
                // Menentukan status aman atau tidak
                $securityStatus = $maliciousVotes > 0 ? 'Malicious/Bahaya' : 'Safe/Aman';

                return [
                    'malicious_votes' => $maliciousVotes,
                    'security_status' => $securityStatus,
                    'raw_data' => $data
                ];
            }
            return null;
        } catch (\Exception $e) {
            Log::error("VirusTotal API Error: " . $e->getMessage());
            return null;
        }
    }
}