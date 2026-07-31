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
        return view('analyzer.index');
    }

    /**
     * Memproses URL: hanya cek VirusTotal di server (cepat), lalu halaman
     * "processing" yang menjalankan Google PageSpeed langsung dari browser.
     */
    public function analyze(Request $request, WebAnalyzerService $analyzerService)
    {
        $request->validate([
            'url' => 'required|url',
            'strategy' => 'required|in:DESKTOP,MOBILE'
        ]);

        $url = $request->input('url');
        $strategy = $request->input('strategy');

        $virusTotalData = $analyzerService->analyzeVirusTotal($url);

        $report = AnalysisReport::create([
            'url' => $url,
            'status' => 'pending',
            'malicious_votes' => $virusTotalData ? $virusTotalData['malicious_votes'] : null,
            'security_status' => $virusTotalData ? $virusTotalData['security_status'] : null,
            'raw_api_data' => json_encode([
                'strategy' => $strategy,
                'pagespeed' => null,
                'virustotal' => $virusTotalData ? $virusTotalData['raw_data'] : null,
            ]),
        ]);

        return view('analyzer.processing', compact('report'));
    }

    /**
     * Menampilkan hasil laporan (hanya bila sudah selesai diproses)
     */
    public function show($id)
    {
        $report = AnalysisReport::findOrFail($id);

        if ($report->status !== 'completed') {
            return view('analyzer.processing', compact('report'));
        }

        return view('analyzer.result', compact('report'));
    }

    /**
     * Endpoint status laporan untuk polling AJAX.
     */
    public function status($id)
    {
        $report = AnalysisReport::findOrFail($id);

        return response()->json([
            'id' => $report->id,
            'status' => $report->status,
        ]);
    }

    /**
     * Menyimpan hasil Google PageSpeed yang dikirim oleh browser.
     */
    public function storePageSpeed($id, Request $request, WebAnalyzerService $analyzerService)
    {
        $report = AnalysisReport::findOrFail($id);

        if ($report->status === 'completed') {
            return response()->json(['ok' => true]);
        }

        $request->validate([
            'pagespeed' => 'required|array',
        ]);

        $data = $request->input('pagespeed');

        $scores = $analyzerService->parsePageSpeedData($data);

        $raw = json_decode($report->raw_api_data, true) ?? [];

        $report->update([
            'performance_score' => $scores['performance_score'],
            'seo_score' => $scores['seo_score'],
            'raw_api_data' => json_encode([
                'strategy' => $raw['strategy'] ?? 'DESKTOP',
                'pagespeed' => $data,
                'virustotal' => $raw['virustotal'] ?? null,
            ]),
            'status' => 'completed',
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Mengekspor data laporan ke format PDF
     */
    public function exportPdf($id)
    {
        $report = AnalysisReport::findOrFail($id);

        if ($report->status !== 'completed') {
            abort(409, 'Laporan belum selesai diproses.');
        }

        $pdf = Pdf::loadView('analyzer.pdf', compact('report'));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Laporan-Analisis-' . $report->id . '.pdf');
    }
}
