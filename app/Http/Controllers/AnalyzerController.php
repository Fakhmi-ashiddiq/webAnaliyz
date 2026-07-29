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
            'url' => 'required|url',
            'strategy' => 'required|in:DESKTOP,MOBILE'
        ]);

        $url = $request->input('url');
        $strategy = $request->input('strategy');

        set_time_limit(60); 

        // Panggil API Google HANYA SEKALI sesuai pilihan user
        $pageSpeedData = $analyzerService->analyzePageSpeed($url, $strategy);
        $virusTotalData = $analyzerService->analyzeVirusTotal($url);

        $report = AnalysisReport::create([
            'url' => $url,
            'performance_score' => $pageSpeedData ? $pageSpeedData['performance_score'] : null,
            'seo_score' => $pageSpeedData ? $pageSpeedData['seo_score'] : null,
            'malicious_votes' => $virusTotalData ? $virusTotalData['malicious_votes'] : null,
            'security_status' => $virusTotalData ? $virusTotalData['security_status'] : null,
            
            // Simpan data mentahnya sesuai strategi yang dipilih
            'raw_api_data' => json_encode([
                'strategy' => $strategy,
                'pagespeed' => $pageSpeedData ? $pageSpeedData['raw_data'] : null,
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