<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Analyzer</title>
    <!-- Memanggil CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Web Analyzer (Performa & Keamanan)</h4>
                    </div>
                    <div class="card-body">
                        <!-- Menampilkan Error Jika Validasi Controller Gagal -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('analyzer.process') }}" method="POST" id="analyzeForm">
                            @csrf <!-- Wajib ada di Laravel untuk keamanan form (Mencegah serangan CSRF) -->
                            
                            <div class="mb-3">
                                <label for="url" class="form-label">Masukkan URL Website:</label>
                                <input type="url" class="form-control form-control-lg" id="url" name="url" placeholder="https://contoh.com" required>
                                <div class="form-text">Pastikan menggunakan http:// atau https://</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">Analisis Sekarang</button>
                        </form>

                        <!-- Indikator Loading (Karena API butuh waktu beberapa detik) -->
                        <div class="text-center mt-4 d-none" id="loadingDiv">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted fw-bold">Sedang menganalisis (mungkin memakan waktu hingga 1 menit)...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script sederhana untuk menampilkan loading saat tombol ditekan -->
    <script>
        document.getElementById('analyzeForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').classList.add('d-none'); // Sembunyikan tombol
            document.getElementById('loadingDiv').classList.remove('d-none'); // Tampilkan loading
        });
    </script>
</body>
</html>