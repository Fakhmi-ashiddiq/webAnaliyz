<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menganalisis...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mt-5">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Web Analyzer (Performa & Keamanan)</h4>
                    </div>
                    <div class="card-body text-center py-5">
                        <div id="loadingBox">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-4 fw-bold">Sedang menganalisis <span id="reportUrl" class="text-primary"></span></p>
                            <p class="text-muted">Google Lighthouse memeriksa website Anda, bisa memakan waktu 1–2 menit. Halaman ini akan otomatis menampilkan hasilnya.</p>
                        </div>

                        <div id="failedBox" class="d-none">
                            <h4 class="text-danger">Analisis Gagal</h4>
                            <p class="text-muted" id="failMsg">Mohon coba beberapa saat lagi.</p>
                            <button class="btn btn-primary" onclick="runAnalysis()">Coba Lagi</button>
                            <a href="{{ route('analyzer.index') }}" class="btn btn-secondary">← Kembali ke Form</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const reportId = {{ $report->id }};
        const reportUrl = @json($report->url);
        const statusUrl = @json(route('analyzer.status', $report->id));
        const resultUrl = @json(route('analyzer.show', $report->id));
        const saveUrl = @json(route('analyzer.store', $report->id));
        const apiKey = @json(config('services.google_pagespeed.key'));
        const strategy = @json(json_decode($report->raw_api_data, true)['strategy'] ?? 'DESKTOP');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.getElementById('reportUrl').textContent = reportUrl;

        function showFail(msg) {
            document.getElementById('failMsg').textContent = msg || 'Mohon coba beberapa saat lagi.';
            document.getElementById('loadingBox').classList.add('d-none');
            document.getElementById('failedBox').classList.remove('d-none');
        }

        async function runAnalysis() {
            document.getElementById('loadingBox').classList.remove('d-none');
            document.getElementById('failedBox').classList.add('d-none');

            try {
                const statusRes = await fetch(statusUrl);
                const statusData = await statusRes.json();

                if (statusData.status === 'completed') {
                    window.location.href = resultUrl;
                    return;
                }

                if (statusData.status === 'failed') {
                    showFail();
                    return;
                }

                const params = new URLSearchParams();
                params.set('url', reportUrl);
                params.set('key', apiKey);
                params.set('strategy', strategy);
                ['PERFORMANCE', 'SEO', 'ACCESSIBILITY', 'BEST_PRACTICES'].forEach(c => params.append('category', c));

                const googleRes = await fetch('https://www.googleapis.com/pagespeedonline/v5/runPagespeed?' + params.toString());

                if (!googleRes.ok) {
                    const err = await googleRes.json().catch(() => ({}));
                    const detail = (err.error && err.error.message) || ('HTTP ' + googleRes.status);
                    throw new Error(detail);
                }

                const data = await googleRes.json();

                const saveRes = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ pagespeed: data }),
                });

                if (!saveRes.ok) {
                    throw new Error('Gagal menyimpan hasil.');
                }

                window.location.href = resultUrl;
            } catch (e) {
                showFail(e.message || 'Terjadi kesalahan koneksi.');
            }
        }

        runAnalysis();
    </script>
</body>
</html>
