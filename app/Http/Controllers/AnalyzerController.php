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
        // Kita akan membuat view 'analyzer.index' di Tahap 5 nanti
        return view('analyzer.index'); 
    }

    /**
     * Memproses URL, memanggil API, dan menyimpan ke Database
     */
    public function analyze(Request $request, WebAnalyzerService $analyzerService)
    {
        $request->validate([
            'url' => 'required|url'
        ], [
            'url.required' => 'URL website wajib diisi.',
            'url.url' => 'Format URL tidak valid (harus menggunakan http:// atau https://).'
        ]);

        $url = $request->input('url');

        // Menambah batas waktu eksekusi PHP agar tidak timeout saat menunggu 2 API Google
        set_time_limit(120); 

        // 2. Panggil API Google 2 kali (Desktop dan Mobile)
        $desktopData = $analyzerService->analyzePageSpeed($url, 'DESKTOP');
        $mobileData = $analyzerService->analyzePageSpeed($url, 'MOBILE');
        
        // Panggil VirusTotal
        $virusTotalData = $analyzerService->analyzeVirusTotal($url);

        // 3. Simpan hasil analisis ke Database
        $report = AnalysisReport::create([
            'url' => $url,
            // Untuk kolom utama, kita ambil dari data Desktop sebagai acuan standar
            'performance_score' => $desktopData ? $desktopData['performance_score'] : null,
            'seo_score' => $desktopData ? $desktopData['seo_score'] : null,
            
            'malicious_votes' => $virusTotalData ? $virusTotalData['malicious_votes'] : null,
            'security_status' => $virusTotalData ? $virusTotalData['security_status'] : null,
            
            // Simpan KEDUA data mentah ke JSON agar bisa kita ekstrak di View nanti
            'raw_api_data' => json_encode([
                'pagespeed_desktop' => $desktopData ? $desktopData['raw_data'] : null,
                'pagespeed_mobile' => $mobileData ? $mobileData['raw_data'] : null,
                'virustotal' => $virusTotalData ? $virusTotalData['raw_data'] : null,
            ])
        ]);

        return view('analyzer.result', compact('report'));
    }

    /**
     * Mengekspor data laporan ke format PDF
     */
    public function exportPdf($id)
    {
        // Cari data laporan berdasarkan ID, jika tidak ada (404), batalkan.
        $report = AnalysisReport::findOrFail($id);

        // Load tampilan khusus PDF (kita buat di langkah 4) dan kirim datanya
        $pdf = Pdf::loadView('analyzer.pdf', compact('report'));

        // Atur ukuran kertas dan orientasi (opsional, defaultnya A4 portrait)
        $pdf->setPaper('A4', 'portrait');

        // Kembalikan sebagai file yang langsung ter-download
        return $pdf->download('Laporan-Analisis-' . $report->id . '.pdf');
    }
}