<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Laporan Hasil Analisis</h5>
                        <!-- Tombol Export PDF (Rutenya akan kita buat di Tahap 6, saat ini kita isi '#' dulu) -->
                        <a href="{{ route('analyzer.pdf', $report->id) }}" class="btn btn-light btn-sm fw-bold text-success">Export ke PDF</a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-1">Target Website:</p>
                        <h5 class="card-title text-primary"><a href="{{ $report->url }}" target="_blank">{{ $report->url }}</a></h5>
                        <hr>

                        <div class="row text-center mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="p-4 border rounded shadow-sm">
                                    <h6 class="text-muted">Performa (PageSpeed)</h6>
                                    <h1 class="{{ $report->performance_score >= 80 ? 'text-success' : ($report->performance_score >= 50 ? 'text-warning' : 'text-danger') }}">
                                        {{ $report->performance_score ?? 'N/A' }} <small class="fs-6 text-muted">/ 100</small>
                                    </h1>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-4 border rounded shadow-sm">
                                    <h6 class="text-muted">SEO (PageSpeed)</h6>
                                    <h1 class="{{ $report->seo_score >= 80 ? 'text-success' : ($report->seo_score >= 50 ? 'text-warning' : 'text-danger') }}">
                                        {{ $report->seo_score ?? 'N/A' }} <small class="fs-6 text-muted">/ 100</small>
                                    </h1>
                                </div>
                            </div>
                        </div>

                        <div class="row text-center mb-4">
                            <div class="col-md-12">
                                <div class="p-4 border rounded shadow-sm {{ $report->security_status == 'Safe/Aman' ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                                    <h6 class="text-muted">Status Keamanan (VirusTotal)</h6>
                                    <h2 class="{{ $report->security_status == 'Safe/Aman' ? 'text-success' : 'text-danger' }}">
                                        {{ $report->security_status ?? 'Gagal Dianalisis' }}
                                    </h2>
                                    @if($report->malicious_votes > 0)
                                        <p class="text-danger mb-0 fw-bold">Peringatan: Terdeteksi {{ $report->malicious_votes }} vendor keamanan menandai URL ini berbahaya!</p>
                                    @else
                                        <p class="text-success mb-0">Situs ini bersih dan tidak ditemukan indikasi malware atau phising.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('analyzer.index') }}" class="btn btn-outline-secondary">Cek Website Lainnya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>