<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Web - {{ $report->url }}</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2c3e50; line-height: 1.5; font-size: 11px; }
        
        /* General Layout */
        .header-box { background: #2c3e50; color: #fff; padding: 12px; border-radius: 6px; margin-bottom: 15px; text-align: center; }
        .header-title { font-size: 20px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .header-subtitle { font-size: 11px; color: #bdc3c7; margin: 3px 0 8px 0; }
        .header-meta { font-size: 10px; background: rgba(255,255,255,0.1); display: inline-block; padding: 5px 12px; border-radius: 4px; }
        
        .section-title { font-size: 13px; font-weight: bold; color: #2980b9; border-bottom: 2px solid #ecf0f1; padding-bottom: 3px; margin-top: 15px; margin-bottom: 8px; text-transform: uppercase; }
        
        /* Two Columns for Grades */
        .row { width: 100%; display: table; margin-bottom: 10px; }
        .col-half { width: 48%; display: table-cell; vertical-align: top; }
        .spacer { width: 4%; display: table-cell; }
        
        .grade-card { border: 1px solid #e0e6ed; border-radius: 6px; padding: 10px; background: #fdfdfd; }
        .grade-card-title { font-size: 12px; font-weight: bold; color: #34495e; margin-bottom: 8px; text-align: center; border-bottom: 1px dashed #e0e6ed; padding-bottom: 5px; }
        
        .grade-box { text-align: center; margin-bottom: 10px; }
        .grade-score { font-size: 32px; font-weight: bold; line-height: 1; }
        
        .text-success { color: #27ae60; }
        .text-warning { color: #f39c12; }
        .text-danger { color: #c0392b; }
        
        .grade-reason { font-size: 10px; color: #555; text-align: justify; padding: 8px; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #bdc3c7; line-height: 1.4; }
        
        /* Mini Scores Box */
        .mini-scores { display: flex; text-align: center; margin-top: 10px; padding-top: 10px; border-top: 1px solid #ecf0f1; width: 100%; }
        .mini-score-item { display: inline-block; width: 32%; }
        .mini-score-val { font-size: 14px; font-weight: bold; color: #2c3e50; }
        .mini-score-label { font-size: 9px; color: #7f8c8d; text-transform: uppercase; }
        
        /* Tables for Details */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        th, td { border: 1px solid #e0e6ed; padding: 6px 8px; text-align: left; }
        th { background-color: #f4f6f7; color: #2c3e50; font-weight: bold; }
        
        /* Recommendations */
        .rec-item { margin-bottom: 8px; padding: 8px 12px; background: #fffcf5; border: 1px solid #fdebd0; border-left: 4px solid #f39c12; border-radius: 4px; }
        .rec-title { font-weight: bold; font-size: 11px; color: #d35400; margin-bottom: 3px; }
        .rec-desc { font-size: 10px; color: #555; }
        .rec-savings { font-size: 9px; color: #c0392b; font-weight: bold; background: #fadbd8; padding: 2px 5px; border-radius: 3px; display: inline-block; margin-top: 3px; }
        
        .footer { text-align: center; font-size: 9px; color: #95a5a6; margin-top: 25px; border-top: 1px solid #ecf0f1; padding-top: 8px; }
        
        .tech-box { font-size: 10px; background: #f4f6f7; padding: 8px; border-radius: 4px; margin-top: 10px; border: 1px solid #e0e6ed; }
        .tech-box strong { color: #34495e; }
    </style>
</head>
<body>

    <!-- Header / Cover -->
    <div class="header-box">
        <h1 class="header-title">LAPORAN ANALISIS WEB</h1>
        <p class="header-subtitle">Audit Performa & Keamanan Komprehensif</p>
        <div class="header-meta">
            <strong>Target:</strong> {{ $report->url }} &nbsp; | &nbsp; 
            <strong>Tanggal:</strong> {{ $report->created_at->format('d F Y, H:i') }} WIB
        </div>
    </div>

    @php
        $gradeLetter = 'N/A';
        $gradeColor = '#7f8c8d';
        $gradeClass = '';
        if ($report->performance_score !== null) {
            if ($report->performance_score >= 90) { $gradeLetter = 'A'; $gradeColor = '#27ae60'; $gradeClass = 'text-success'; }
            elseif ($report->performance_score >= 50) { $gradeLetter = 'B'; $gradeColor = '#f39c12'; $gradeClass = 'text-warning'; }
            else { $gradeLetter = 'C'; $gradeColor = '#c0392b'; $gradeClass = 'text-danger'; }
        }
    @endphp

    <!-- Ringkasan Eksekutif (Grades & Reasons) -->
    <div class="section-title">Ringkasan Eksekutif & Analisis Hasil Audit</div>
    <div class="row">
        <!-- Kolom Performa -->
        <div class="col-half">
            <div class="grade-card">
                <div class="grade-card-title">Skor Performa Web</div>
                <div class="grade-box">
                    <div class="grade-score {{ $gradeClass }}">
                        {{ $report->performance_score ?? 'N/A' }} 
                        <span style="font-size: 18px; color: #7f8c8d;">/ 100</span>
                        <span style="font-size: 24px; margin-left: 5px;">(Grade {{ $gradeLetter }})</span>
                    </div>
                </div>
                
                <div style="text-align:center; width:100%; margin-bottom:10px;">
                    <div class="mini-score-item">
                        <div class="mini-score-val">{{ $scores['accessibility'] ?? 'N/A' }}</div>
                        <div class="mini-score-label">Accessibility</div>
                    </div>
                    <div class="mini-score-item">
                        <div class="mini-score-val">{{ $scores['best_practices'] ?? 'N/A' }}</div>
                        <div class="mini-score-label">Best Practices</div>
                    </div>
                    <div class="mini-score-item">
                        <div class="mini-score-val">{{ $scores['seo'] ?? 'N/A' }}</div>
                        <div class="mini-score-label">SEO</div>
                    </div>
                </div>

                <div class="grade-reason" style="border-left-color: {{ $gradeColor }};">
                    {{ $performance_reason }}
                </div>
            </div>
        </div>
        
        <div class="spacer"></div>
        
        <!-- Kolom Keamanan -->
        <div class="col-half">
            <div class="grade-card">
                <div class="grade-card-title">Status Keamanan Jaringan</div>
                <div class="grade-box">
                    @if($report->malicious_votes > 0)
                        <div class="grade-score text-danger" style="font-size: 24px; margin-top: 8px;">BERBAHAYA</div>
                        <div style="font-size: 11px; color: #c0392b; margin-top: 5px;">{{ $report->malicious_votes }} Deteksi Ancaman</div>
                    @else
                        <div class="grade-score text-success" style="font-size: 24px; margin-top: 8px;">AMAN (CLEAN)</div>
                        <div style="font-size: 11px; color: #27ae60; margin-top: 5px;">0 Deteksi Ancaman</div>
                    @endif
                </div>
                
                <div class="tech-box">
                    <div style="margin-bottom:3px;"><strong>IP Target:</strong> {{ $security_details['ip'] ?? 'N/A' }}</div>
                    <div style="margin-bottom:3px;"><strong>HTTP Status:</strong> {{ $security_details['status_code'] ?? 'N/A' }}</div>
                    @if(!empty($security_details['categories']))
                    <div><strong>Kategori:</strong> 
                        @php $cats = is_array($security_details['categories']) ? array_slice($security_details['categories'], 0, 3) : []; @endphp
                        {{ implode(', ', $cats) }}
                    </div>
                    @endif
                </div>

                <div class="grade-reason" style="border-left-color: {{ $report->malicious_votes > 0 ? '#c0392b' : '#27ae60' }}; margin-top: 10px;">
                    {{ $security_reason }}
                </div>
                
                @if(!empty($security_vendors))
                <div style="font-size:9px; color:#c0392b; margin-top:5px;">
                    <strong>Terdeteksi oleh:</strong> {{ implode(', ', $security_vendors) }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Detail Audit -->
    <div class="section-title">Detail Metrik Kunci (Core Web Vitals)</div>
    <table>
        <tr>
            <th width="35%">Nama Metrik</th>
            <th width="45%">Deskripsi Pengukuran</th>
            <th width="20%">Nilai (Hasil)</th>
        </tr>
        <tr>
            <td><strong>Largest Contentful Paint (LCP)</strong></td>
            <td>Waktu yang dibutuhkan untuk merender konten visual terbesar di layar.</td>
            <td><strong>{{ $metrics['lcp'] ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td><strong>First Contentful Paint (FCP)</strong></td>
            <td>Waktu hingga elemen pertama (teks/gambar) muncul di layar.</td>
            <td><strong>{{ $metrics['fcp'] ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Cumulative Layout Shift (CLS)</strong></td>
            <td>Mengukur pergeseran tata letak (layout) mendadak yang tidak diharapkan.</td>
            <td><strong>{{ $metrics['cls'] ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Speed Index (SI)</strong></td>
            <td>Seberapa cepat keseluruhan konten halaman terisi penuh secara visual.</td>
            <td><strong>{{ $metrics['si'] ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Total Blocking Time (TBT)</strong></td>
            <td>Total durasi antarmuka "membeku" akibat eksekusi tugas yang berat.</td>
            <td><strong>{{ $metrics['tbt'] ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Time to Interactive (TTI)</strong></td>
            <td>Lama waktu hingga halaman web sepenuhnya siap untuk interaksi pengguna.</td>
            <td><strong>{{ $metrics['tti'] ?? 'N/A' }}</strong></td>
        </tr>
    </table>
    <div style="font-size: 9px; color: #7f8c8d; font-style: italic; margin-top: -10px; margin-bottom: 15px;">
        *Catatan: Jika ada nilai berupa "N/A" atau "Data tidak tersedia", ini berarti alat tidak bisa mengekstrak data dari target karena diblokir oleh target atau koneksi target terlalu lambat (timeout).
    </div>

    <!-- Security Headers -->
    <div class="section-title">Analisis Security Headers</div>
    @if($security_headers_score !== null)
        @php
            $shScore = $security_headers_score;
            $shColor = $shScore >= 80 ? '#27ae60' : ($shScore >= 50 ? '#f39c12' : '#c0392b');
            $shGrade = $shScore >= 90 ? 'A' : ($shScore >= 70 ? 'B' : ($shScore >= 50 ? 'C' : ($shScore >= 30 ? 'D' : 'E')));
        @endphp
        <div class="row">
            <div class="col-half">
                <div class="grade-card">
                    <div class="grade-card-title">Skor Security Headers</div>
                    <div class="grade-box">
                        <div class="grade-score" style="color: {{ $shColor }};">
                            {{ $shScore }} <span style="font-size: 18px; color: #7f8c8d;">/ 100</span>
                            <span style="font-size: 24px; margin-left: 5px;">(Grade {{ $shGrade }})</span>
                        </div>
                    </div>
                    <div class="grade-reason" style="border-left-color: {{ $shColor }};">
                        Analisis header keamanan (HTTPS, HSTS, CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy) terhadap respons server.
                    </div>
                </div>
            </div>
            <div class="spacer"></div>
            <div class="col-half">
                <table>
                    <tr>
                        <th width="55%">Header</th>
                        <th width="25%">Skor</th>
                        <th width="20%">Status</th>
                    </tr>
                    @foreach($security_headers_items as $shItem)
                        <tr>
                            <td><strong>{{ $shItem['label'] ?? '-' }}</strong></td>
                            <td>{{ $shItem['points'] ?? 0 }}/{{ $shItem['max'] ?? 0 }}</td>
                            <td>
                                @if(($shItem['status'] ?? '') === 'good')
                                    <span class="text-success">Baik</span>
                                @elseif(($shItem['status'] ?? '') === 'partial')
                                    <span class="text-warning">Sebagian</span>
                                @else
                                    <span class="text-danger">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        @if(!empty($security_headers_recs))
            <p style="margin: 5px 0 8px 0; font-size: 10px;"><strong>Rekomendasi Security Headers:</strong></p>
            @foreach($security_headers_recs as $shRec)
                <div class="rec-item">
                    <div class="rec-desc">{{ $shRec }}</div>
                </div>
            @endforeach
        @else
            <div style="background: #eafaf1; border: 1px solid #d5f5e3; padding: 10px; border-radius: 4px; color: #1e8449; font-weight: bold; font-size: 10px;">
                Semua security header utama sudah terpasang dengan baik.
            </div>
        @endif
    @else
        <div style="background: #fef9e7; border: 1px solid #fdebd0; padding: 10px; border-radius: 4px; color: #7d6608; font-size: 10px;">
            Data security headers tidak tersedia untuk laporan ini.
        </div>
    @endif

    <!-- Rekomendasi -->
    <div class="section-title">Top Issues & Rekomendasi Perbaikan</div>
    @if(empty($recommendations))
        <div style="background: #eafaf1; border: 1px solid #d5f5e3; padding: 12px; border-radius: 4px; color: #1e8449; text-align: center; font-weight: bold; font-size: 11px;">
            Luar biasa! Web ini sudah sangat optimal. Tidak ada masalah teknis prioritas yang mendesak untuk diperbaiki.
        </div>
    @else
        <p style="margin-bottom: 10px; font-size: 10px;">Berdasarkan kegagalan metrik audit di atas, berikut adalah temuan arsitektural utama serta langkah teknis untuk menanganinya:</p>
        @foreach($recommendations as $rec)
            <div class="rec-item">
                <div class="rec-title">{{ $rec['title'] }}</div>
                <div class="rec-desc">{{ $rec['description'] }}</div>
                @if(!empty($rec['savings']))
                    <div class="rec-savings">Potensi Peningkatan (Savings): {{ $rec['savings'] }}</div>
                @endif
            </div>
        @endforeach
    @endif

    <div class="footer">
        Dihasilkan secara otomatis oleh Web Analyzer System pada {{ date('d F Y, H:i') }} WIB
    </div>

</body>
</html>