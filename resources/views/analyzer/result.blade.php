<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Analyzer - {{ $report->url }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.05); padding: 30px; border-radius: 8px; }
        
        .ps-scores { display: flex; justify-content: center; align-items: flex-start; gap: 40px; margin-bottom: 40px; flex-wrap: wrap; }
        .ps-score-item { text-align: center; font-family: 'Roboto', sans-serif; display: flex; flex-direction: column; align-items: center; }
        
        .circular-chart { display: block; margin: 0 auto; width: 80px; height: 80px; }
        .circle-bg { fill: none; stroke: #eee; stroke-width: 2.5; }
        .circle { fill: none; stroke-width: 2.5; stroke-linecap: round; }
        .percentage { fill: #333; font-family: 'Roboto', sans-serif; font-size: 10px; text-anchor: middle; font-weight: 500; }
        .score-title { margin-top: 10px; font-size: 14px; font-weight: 500; color: #202124; }
        
        .color-red { stroke: #ff4e42; } .text-red { fill: #ff4e42; color: #ff4e42; }
        .color-orange { stroke: #ffa400; } .text-orange { fill: #ffa400; color: #ffa400; }
        .color-green { stroke: #0cce6b; } .text-green { fill: #0cce6b; color: #0cce6b; }

        .perf-donut-container { position: relative; width: 200px; height: 200px; margin: 20px auto; cursor: pointer; }
        .perf-donut-container .circular-chart { width: 140px; height: 140px; position: absolute; top: 30px; left: 30px; }
        .perf-donut-container .percentage { font-size: 8px; }
        .metric-label { position: absolute; font-size: 11px; font-family: 'Roboto', sans-serif; color: #5f6368; font-weight: 500; }
        .ml-fcp { top: 10px; right: 30px; } 
        .ml-si { top: 10px; left: 40px; }
        .ml-lcp { top: 80px; right: 0px; }
        .ml-tbt { bottom: 0px; left: 85px; }
        .ml-cls { top: 80px; left: -5px; }

        .gt-header-row { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 8px; justify-content: space-around;}
        .gt-box-title { font-size: 18px; color: #0073b6; margin-bottom: 10px; font-weight: 400; }
        .gt-grade-card, .gt-vitals-card { background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; display: flex; align-items: center; }
        .gt-grade-letter { font-size: 65px; font-weight: 700; line-height: 1; padding-right: 20px; border-right: 1px solid #eee; margin-right: 20px; }
        .gt-score-item { text-align: center; padding: 0 15px; border-right: 1px solid #eee; }
        .gt-score-item:last-child { border-right: none; padding-right: 0; }
        .gt-score-label { font-size: 13px; color: #777; margin-bottom: 5px; }
        .gt-score-val { font-size: 26px; font-weight: 400; }

        .two-col-layout { display: flex; flex-wrap: wrap; gap: 30px; margin-bottom: 40px; }
        .col-left { flex: 7; min-width: 300px;} .col-right { flex: 5; min-width: 300px;}
        .section-heading { font-size: 22px; color: #444; font-weight: 400; margin-bottom: 15px; }
        
        .issue-row { display: flex; margin-bottom: 10px; background: #fff; border: 1px solid #eee; border-radius: 3px; align-items: flex-start; cursor: pointer; flex-direction: column;}
        .issue-header { display: flex; width: 100%; align-items: center;}
        .issue-impact { width: 80px; text-align: center; padding: 12px 10px; color: #fff; font-weight: 600; font-size: 13px; border-radius: 3px 0 0 3px; align-self: stretch; display: flex; align-items: center; justify-content: center;}
        .impact-high { background-color: #e74c3c; } .impact-med { background-color: #f39c12; }
        .issue-title { padding: 10px 15px; font-size: 14px; color: #2980b9; font-weight: 600; flex-grow: 1; }
        .issue-desc { display: none; padding: 15px; font-size: 13px; color: #555; border-top: 1px solid #eee; width: 100%; background: #fafafa;}
        .issue-row.expanded .issue-desc { display: block; }

        .url-header { font-size: 22px; color: #333; margin-bottom: 20px; font-weight: 400; border-bottom: 1px solid #eee; padding-bottom: 15px; display: flex; justify-content: space-between; align-items: center;}
        .badge-strategy { font-size: 14px; background: #3498db; color: #fff; padding: 4px 12px; border-radius: 20px; text-transform: uppercase;}
    </style>
</head>
<body>

    @php
        $rawData = json_decode($report->raw_api_data, true);
        $strategy = $rawData['strategy'] ?? 'DESKTOP';
        $data = $rawData['pagespeed'] ?? null;

        $getColorClass = function($score) {
            return $score >= 90 ? 'green' : ($score >= 50 ? 'orange' : 'red');
        };
        $getHexColor = function($colorClass) {
            return $colorClass == 'green' ? '#0cce6b' : ($colorClass == 'orange' ? '#ffa400' : '#ff4e42');
        };
    @endphp

    <div class="container">
        
        <div class="url-header">
            <div>Laporan Analisis: <a href="{{ $report->url }}" target="_blank">{{ $report->url }}</a></div>
            <span class="badge-strategy">{{ $strategy }}</span>
        </div>

        @if(!$data)
            <div style="padding:40px; text-align:center; color:#e74c3c; border:1px solid #f2dede; background:#fde8e9; border-radius:4px;">
                Gagal memuat data dari Google PageSpeed API. Kemungkinan besar API Key terkena batasan kuota sementara. Silakan coba beberapa saat lagi.
            </div>
        @else
            @php
                $lh = $data['lighthouseResult'];
                $audits = $lh['audits'];
                $cats = $lh['categories'];

                $perf = isset($cats['performance']) ? round($cats['performance']['score'] * 100) : 0;
                $acc = isset($cats['accessibility']) ? round($cats['accessibility']['score'] * 100) : 0;
                $bp = isset($cats['best-practices']) ? round($cats['best-practices']['score'] * 100) : 0;
                $seo = isset($cats['seo']) ? round($cats['seo']['score'] * 100) : 0;

                $structure = round(($acc + $bp + $seo) / 3);
                $grade = $perf >= 90 ? 'A' : ($perf >= 80 ? 'B' : ($perf >= 70 ? 'C' : ($perf >= 50 ? 'D' : 'F')));
                
                $fcpRaw = $audits['first-contentful-paint']['numericValue'] ?? 0;
                $lcpRaw = $audits['largest-contentful-paint']['numericValue'] ?? 0;
                $tbtRaw = $audits['total-blocking-time']['numericValue'] ?? 0;
                $clsRaw = $audits['cumulative-layout-shift']['numericValue'] ?? 0;
                $siRaw  = $audits['speed-index']['numericValue'] ?? 0;
            @endphp

            <!-- PAGESPEED DONUTS -->
            <div class="ps-scores">
                <div class="ps-score-item">
                    <div class="perf-donut-container" id="perfDonutBox">
                        @php $perfColor = $getColorClass($perf); @endphp
                        <span class="metric-label ml-fcp">FCP</span>
                        <span class="metric-label ml-si">SI</span>
                        <span class="metric-label ml-lcp">LCP</span>
                        <span class="metric-label ml-tbt">TBT</span>
                        <span class="metric-label ml-cls">CLS</span>
                        
                        <svg viewBox="0 0 36 36" class="circular-chart color-{{ $perfColor }}">
                            <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="circle" stroke-dasharray="{{ $perf }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <text x="18" y="20.35" class="percentage text-{{ $perfColor }}">{{ $perf }}</text>
                        </svg>
                    </div>
                    <div class="score-title">Performance</div>
                </div>

                @foreach(['Accessibility' => $acc, 'Best Practices' => $bp, 'SEO' => $seo] as $title => $score)
                    @php $cColor = $getColorClass($score); @endphp
                    <div class="ps-score-item" style="margin-top: 50px;">
                        <svg viewBox="0 0 36 36" class="circular-chart color-{{ $cColor }}">
                            <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="circle" stroke-dasharray="{{ $score }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <text x="18" y="20.35" class="percentage text-{{ $cColor }}">{{ $score }}</text>
                        </svg>
                        <div class="score-title">{{ $title }}</div>
                    </div>
                @endforeach
            </div>

            <!-- GTMETRIX HEADER -->
            <div class="gt-header-row">
                <div>
                    <div class="gt-box-title">GTmetrix Grade</div>
                    <div class="gt-grade-card">
                        <div class="gt-grade-letter" style="color: {{ $getHexColor($getColorClass($perf)) }}">{{ $grade }}</div>
                        <div class="gt-score-item">
                            <div class="gt-score-label">Performance</div>
                            <div class="gt-score-val" style="color: {{ $getHexColor($getColorClass($perf)) }}">{{ $perf }}%</div>
                        </div>
                        <div class="gt-score-item">
                            <div class="gt-score-label">Structure</div>
                            <div class="gt-score-val" style="color: {{ $getHexColor($getColorClass($structure)) }}">{{ $structure }}%</div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="gt-box-title">Web Vitals</div>
                    <div class="gt-vitals-card">
                        <div class="gt-score-item">
                            <div class="gt-score-label">LCP</div>
                            <div class="gt-score-val">{{ round($lcpRaw/1000, 1) }}s</div>
                        </div>
                        <div class="gt-score-item">
                            <div class="gt-score-label">TBT</div>
                            <div class="gt-score-val">{{ round($tbtRaw) }}ms</div>
                        </div>
                        <div class="gt-score-item">
                            <div class="gt-score-label">CLS</div>
                            <div class="gt-score-val">{{ number_format($clsRaw, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOP ISSUES -->
            <div class="section-heading">Top Issues & Diagnostics</div>
            <div style="margin-bottom: 40px;">
                @foreach($audits as $aId => $a)
                    @if(isset($a['score']) && $a['score'] < 0.9 && isset($a['details']['type']) && $a['details']['type'] !== 'opportunity')
                        @php 
                            $isHigh = $a['score'] < 0.5;
                            $impCls = $isHigh ? 'impact-high' : 'impact-med';
                            $impTxt = $isHigh ? 'High' : 'Med';
                        @endphp
                        <div class="issue-row" onclick="this.classList.toggle('expanded')">
                            <div class="issue-header">
                                <div class="issue-impact {{ $impCls }}">{{ $impTxt }}</div>
                                <div class="issue-title">{{ $a['title'] }}</div>
                                <div style="padding: 10px; color: #aaa; font-size: 12px;">▼</div>
                            </div>
                            <div class="issue-desc">
                                {!! strip_tags($a['description'], '<a><code>') !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('analyzer.index') }}" class="btn btn-primary" style="padding: 10px 20px; background: #0073b6; color: #fff; text-decoration: none; border-radius: 4px;">← Analisis Website Lain</a>
        </div>
    </div>

    <!-- SKRIP HOVER DONAT PERFORMANCE -->
    <!-- SKRIP HOVER DONAT PERFORMANCE DENGAN STATUS DETAIL -->
    <!-- SKRIP HOVER DONAT PERFORMANCE DENGAN FORMAT POIN KONTRIBUSI -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const perfBox = document.getElementById('perfDonutBox');
            
            if (perfBox) {
                // Mengambil data skor metrik atau bobot estimasi dari Lighthouse API
                @php
                    $audits = $data['lighthouseResult']['audits'] ?? [];
                    // Mengambil skor tiap audit (skor berkisar 0 sampai 1)
                    $fcpScore = isset($audits['first-contentful-paint']['score']) ? round($audits['first-contentful-paint']['score'] * 10) : 0;
                    $lcpScore = isset($audits['largest-contentful-paint']['score']) ? round($audits['largest-contentful-paint']['score'] * 25) : 0;
                    $tbtScore = isset($audits['total-blocking-time']['score']) ? round($audits['total-blocking-time']['score'] * 30) : 0;
                    $clsScore = isset($audits['cumulative-layout-shift']['score']) ? round($audits['cumulative-layout-shift']['score'] * 15) : 0;
                    $siScore  = isset($audits['speed-index']['score']) ? round($audits['speed-index']['score'] * 20) : 0;
                @endphp

                const fcpPlus = "+{{ $fcpScore }}";
                const lcpPlus = "+{{ $lcpScore }}";
                const tbtPlus = "+{{ $tbtScore }}";
                const clsPlus = "+{{ $clsScore }}";
                const siPlus  = "+{{ $siScore }}";

                const tip = document.createElement('div');
                tip.style.position = 'absolute';
                tip.style.background = 'rgba(33, 33, 33, 0.95)';
                tip.style.color = '#ffffff';
                tip.style.padding = '12px 16px';
                tip.style.borderRadius = '6px';
                tip.style.fontSize = '12px';
                tip.style.lineHeight = '1.6';
                tip.style.zIndex = '9999';
                tip.style.display = 'none';
                tip.style.pointerEvents = 'none';
                tip.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
                document.body.appendChild(tip);

                perfBox.addEventListener('mousemove', function(e) {
                    tip.style.display = 'block';
                    tip.style.left = (e.pageX + 15) + 'px';
                    tip.style.top = (e.pageY + 15) + 'px';
                    tip.innerHTML = `
                        <strong style="color: #ffa400; border-bottom: 1px solid #555; display: block; margin-bottom: 6px; padding-bottom: 3px; font-size: 13px;">Kontribusi Skor Performa:</strong>
                        • <strong>LCP:</strong> ${lcpPlus}<br>
                        • <strong>TBT:</strong> ${tbtPlus}<br>
                        • <strong>CLS:</strong> ${clsPlus}<br>
                        • <strong>SI:</strong> ${siPlus}<br>
                        • <strong>FCP:</strong> ${fcpPlus}
                    `;
                });

                perfBox.addEventListener('mouseleave', function() {
                    tip.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>