<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Analyzer - {{ $report->url }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* ================= GLOBAL & LAYOUT ================= */
        body { font-family: 'Open Sans', sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.05); padding: 30px; border-radius: 8px; }
        .device-view { display: none; animation: fadeIn 0.4s; }
        .device-view.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        /* ================= PAGESPEED TABS ================= */
        .ps-tabs { display: flex; justify-content: center; gap: 40px; border-bottom: 1px solid #e0e0e0; margin-bottom: 30px; }
        .ps-tab { padding: 15px 20px; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; color: #5f6368; cursor: pointer; border-bottom: 3px solid transparent; display: flex; align-items: center; gap: 8px; transition: 0.3s;}
        .ps-tab.active { color: #1a73e8; border-bottom: 3px solid #1a73e8; }
        .ps-tab svg { width: 20px; height: 20px; fill: currentColor; }

        /* ================= PAGESPEED DONUTS ================= */
        .ps-scores { display: flex; justify-content: center; align-items: flex-start; gap: 50px; margin-bottom: 40px; }
        .ps-score-item { text-align: center; font-family: 'Roboto', sans-serif; display: flex; flex-direction: column; align-items: center; }
        
        .circular-chart { display: block; margin: 0 auto; width: 80px; height: 80px; }
        .circle-bg { fill: none; stroke: #eee; stroke-width: 2.5; }
        .circle { fill: none; stroke-width: 2.5; stroke-linecap: round; transition: stroke-dasharray 1.5s ease-out; }
        .percentage { fill: #333; font-family: 'Roboto', sans-serif; font-size: 10px; text-anchor: middle; font-weight: 500; }
        .score-title { margin-top: 10px; font-size: 14px; font-weight: 500; color: #202124; }
        
        .color-red { stroke: #ff4e42; } .text-red { fill: #ff4e42; color: #ff4e42; }
        .color-orange { stroke: #ffa400; } .text-orange { fill: #ffa400; color: #ffa400; }
        .color-green { stroke: #0cce6b; } .text-green { fill: #0cce6b; color: #0cce6b; }

        .triangle-wrapper { width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; }
        .triangle { width: 0; height: 0; border-left: 20px solid transparent; border-right: 20px solid transparent; border-bottom: 35px solid #ff4e42; }
        .agentic-badge { background: #ffebee; color: #ff4e42; font-size: 12px; padding: 2px 8px; border-radius: 10px; margin-top: 5px; font-weight: 500; }

        .perf-donut-container { position: relative; width: 200px; height: 200px; margin: 20px auto; }
        .perf-donut-container .circular-chart { width: 140px; height: 140px; position: absolute; top: 30px; left: 30px; }
        .perf-donut-container .percentage { font-size: 8px; }
        .metric-label { position: absolute; font-size: 11px; font-family: 'Roboto', sans-serif; color: #5f6368; font-weight: 500; }
        .ml-fcp { top: 10px; right: 30px; } .ml-fcp::after { content:''; position:absolute; width:6px; height:6px; border-radius:50%; top:4px; right:-10px; }
        .ml-si { top: 10px; left: 40px; }
        .ml-lcp { top: 80px; right: 0px; }
        .ml-tbt { bottom: 0px; left: 85px; }
        .ml-cls { top: 80px; left: -5px; }

        /* ================= GTMETRIX HEADER ================= */
        .gt-header-row { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; background: url('https://gtmetrix.com/static/images/global/bg-pattern.png') #f9f9f9; padding: 20px; border-radius: 8px;}
        .gt-box-title { font-size: 18px; color: #0073b6; margin-bottom: 10px; font-weight: 400; }
        .gt-grade-card, .gt-vitals-card { background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; display: flex; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .gt-grade-letter { font-size: 65px; font-weight: 700; line-height: 1; padding-right: 20px; border-right: 1px solid #eee; margin-right: 20px; }
        .gt-score-item { text-align: center; padding: 0 15px; border-right: 1px solid #eee; }
        .gt-score-item:last-child { border-right: none; padding-right: 0; }
        .gt-score-label { font-size: 13px; color: #777; margin-bottom: 5px; }
        .gt-score-val { font-size: 26px; font-weight: 400; }
        
        /* ================= GTMETRIX CrUX ================= */
        .crux-banner { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-radius: 4px; margin-bottom: 20px; font-weight: 600; font-size: 15px; }
        .crux-failed { background-color: #fde8e9; color: #c9302c; border: 1px solid #fac2c5; }
        .crux-passed { background-color: #e6f6e6; color: #3c763d; border: 1px solid #c3e6c3; }
        .crux-banner-icon { font-size: 20px; margin-right: 10px; vertical-align: middle; }
        .crux-toggle { display: flex; border: 1px solid #337ab7; border-radius: 4px; overflow: hidden; margin-left:15px;}
        .crux-toggle span { padding: 5px 15px; font-size: 13px; cursor: pointer; color: #337ab7; background: #fff; font-weight: 400;}
        .crux-toggle span.active { background: #337ab7; color: #fff; }

        .crux-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px; }
        .crux-card { border: 1px solid #ddd; border-radius: 4px; padding: 15px; background: #fff; }
        .crux-card-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .crux-card-title { font-size: 14px; color: #555; }
        .crux-badge { font-size: 11px; padding: 3px 8px; border-radius: 2px; color: #fff; font-weight: 600; text-transform: uppercase; }
        .bg-improve { background-color: #f0ad4e; } .bg-pass { background-color: #5cb85c; } .bg-poor { background-color: #d9534f; }
        .crux-val { font-size: 22px; font-weight: 600; text-align: right; }
        .crux-bar { display: flex; height: 8px; border-radius: 4px; overflow: hidden; margin-top: 15px; }
        .crux-bar-segment { height: 100%; }

        /* ================= TOP ISSUES & PAGE DETAILS ================= */
        .two-col-layout { display: flex; flex-wrap: wrap; gap: 30px; margin-bottom: 40px; }
        .col-left { flex: 7; min-width: 300px;} .col-right { flex: 5; min-width: 300px;}
        .section-heading { font-size: 22px; color: #444; font-weight: 400; margin-bottom: 15px; }
        
        .filter-row { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; font-size: 13px; flex-wrap: wrap;}
        .btn-filter { background: #eee; border: 1px solid #ddd; padding: 5px 12px; border-radius: 3px; cursor: pointer; color: #555; transition: 0.2s;}
        .btn-filter.active { background: #2c3e50; color: #fff; border-color: #2c3e50; }

        .issue-row { display: flex; margin-bottom: 10px; background: #fff; border: 1px solid #eee; border-radius: 3px; align-items: flex-start; cursor: pointer; transition: 0.2s; flex-direction: column;}
        .issue-row:hover { background: #f9f9f9; }
        .issue-header { display: flex; width: 100%; align-items: center;}
        .issue-impact { width: 80px; text-align: center; padding: 12px 10px; color: #fff; font-weight: 600; font-size: 13px; border-radius: 3px 0 0 3px; align-self: stretch; display: flex; align-items: center; justify-content: center;}
        .impact-high { background-color: #e74c3c; } .impact-med { background-color: #f39c12; }
        .impact-medlow { background-color: #a3cb38; } .impact-low { background-color: #27ae60; }
        .issue-title { padding: 10px 15px; font-size: 14px; color: #2980b9; font-weight: 600; flex-grow: 1; }
        .issue-badge { background: #eee; color: #777; font-size: 11px; padding: 2px 5px; border-radius: 3px; margin-left: 5px; border: 1px solid #ddd;}
        .issue-desc { display: none; padding: 15px; font-size: 13px; color: #555; border-top: 1px solid #eee; width: 100%; background: #fafafa;}
        .issue-row.expanded .issue-desc { display: block; }

        /* Page Details Stacked Bar */
        .pd-total { font-size: 15px; color: #555; margin-bottom: 5px; margin-top: 20px;}
        .pd-bar-wrapper { display: flex; height: 40px; border-radius: 3px; overflow: hidden; margin-bottom: 5px; }
        .pd-segment { display: flex; flex-direction: column; justify-content: center; align-items: center; color: #fff; font-size: 11px; border-right: 1px solid rgba(255,255,255,0.3); text-align: center; line-height: 1.2;}
        .pd-color-image { background-color: #4a779d; } 
        .pd-color-script { background-color: #6a6a8c; } 
        .pd-color-stylesheet { background-color: #8c6a8c; } 
        .pd-color-font { background-color: #d9534f; }
        .pd-color-document { background-color: #5cb85c; }
        .pd-color-other { background-color: #c5a3c5; }

        /* ================= PERFORMANCE METRICS & TIMINGS ================= */
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-bottom: 40px; }
        .metric-card-border { background: #fff; border: 1px solid #ddd; border-radius: 4px; display: flex; overflow: hidden; position: relative;}
        .metric-card-color { width: 6px; }
        .metric-card-content { padding: 15px; flex-grow: 1; position: relative; }
        .mc-title { font-size: 18px; color: #444; font-weight: 400; margin-bottom: 10px; }
        .mc-desc { font-size: 12px; color: #777; line-height: 1.4; padding-right: 100px; } 
        .mc-val { position: absolute; right: 15px; bottom: 15px; font-size: 28px; font-weight: 400; }
        .mc-badge { position: absolute; right: 15px; top: 15px; font-size: 11px; padding: 4px 8px; color: #fff; font-weight: 600; border-radius: 2px; }
        
        .br-blue { background: #3498db; } .br-purple { background: #9b59b6; } .br-pink { background: #e84393; }
        .br-teal { background: #1abc9c; } .br-green { background: #2ecc71; } .br-dark { background: #34495e; }

        .timings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 40px; }
        .timing-card { background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; border-left: 4px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .timing-card .t-label { font-size: 14px; color: #555; }
        .timing-card .t-val { font-size: 18px; color: #444; }

        .toggle-details-wrap { float: right; font-size: 12px; color: #555; display: flex; align-items: center; gap: 5px; }
        .switch { position: relative; display: inline-block; width: 34px; height: 18px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #3498db; }
        input:checked + .slider:before { transform: translateX(16px); }

        /* Header Info */
        .url-header { font-size: 24px; color: #333; margin-bottom: 20px; font-weight: 400; border-bottom: 1px solid #eee; padding-bottom: 15px;}
        .url-header a { color: #337ab7; text-decoration: none; font-weight: 600;}
    </style>
</head>
<body>

    @php
        // Mengambil JSON Mentah dari Database
        $rawData = json_decode($report->raw_api_data, true);
        
        $devices = [
            'DESKTOP' => $rawData['pagespeed_desktop'] ?? null,
            'MOBILE' => $rawData['pagespeed_mobile'] ?? null
        ];

        // Fungsi Helper untuk warna dan format
        function getColorClass($score) {
            return $score >= 90 ? 'green' : ($score >= 50 ? 'orange' : 'red');
        }
        function getHexColor($colorClass) {
            return $colorClass == 'green' ? '#0cce6b' : ($colorClass == 'orange' ? '#ffa400' : '#ff4e42');
        }
        function getMetricStatus($val, $th1, $th2) {
            if ($val <= $th1) return ['badge' => 'Good - Nothing to do here', 'bg' => '#27ae60', 'color' => '#27ae60'];
            if ($val <= $th2) return ['badge' => 'OK, but consider improvement', 'bg' => '#a3cb38', 'color' => '#a3cb38'];
            return ['badge' => 'Longer than recommended', 'bg' => '#e74c3c', 'color' => '#e74c3c'];
        }
        function formatBytes($bytes) {
            if ($bytes >= 1048576) return round($bytes / 1048576, 1) . 'MB';
            if ($bytes >= 1024) return round($bytes / 1024, 1) . 'KB';
            return $bytes . 'B';
        }
    @endphp

    <div class="container">
        
        <div class="url-header">
            Latest Performance Report for: <a href="{{ $report->url }}" target="_blank">{{ $report->url }}</a>
        </div>

        <!-- TABS BUTTONS -->
        <div class="ps-tabs">
            <div class="ps-tab active" data-target="desktop-view">
                <svg viewBox="0 0 24 24"><path d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/></svg> Desktop
            </div>
            <div class="ps-tab" data-target="mobile-view">
                <svg viewBox="0 0 24 24"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg> Mobile
            </div>
        </div>

        <!-- KONTEN LOOP: DESKTOP & MOBILE -->
        @foreach($devices as $deviceType => $data)
            @php $viewId = strtolower($deviceType) . '-view'; @endphp
            
            <div id="{{ $viewId }}" class="device-view {{ $loop->first ? 'active' : '' }}">
                @if(!$data)
                    <div style="padding:40px; text-align:center; color:#e74c3c; border:1px solid #f2dede; background:#fde8e9; border-radius:4px;">
                        Data performa untuk {{ $deviceType }} gagal diambil dari Google API.
                    </div>
                    @continue
                @endif

                @php
                    $lh = $data['lighthouseResult'];
                    $audits = $lh['audits'];
                    $cats = $lh['categories'];

                    // Skor Utama PageSpeed (0-100)
                    $perf = isset($cats['performance']) ? round($cats['performance']['score'] * 100) : 0;
                    $acc = isset($cats['accessibility']) ? round($cats['accessibility']['score'] * 100) : 0;
                    $bp = isset($cats['best-practices']) ? round($cats['best-practices']['score'] * 100) : 0;
                    $seo = isset($cats['seo']) ? round($cats['seo']['score'] * 100) : 0;

                    // GTmetrix Logic
                    $structure = round(($acc + $bp + $seo) / 3);
                    $grade = $perf >= 90 ? 'A' : ($perf >= 80 ? 'B' : ($perf >= 70 ? 'C' : ($perf >= 50 ? 'D' : 'F')));
                    $gradeColor = $grade == 'A' ? '#8cc152' : ($grade == 'B' ? '#337ab7' : ($grade == 'C' ? '#f39c12' : '#e74c3c'));

                    // Raw Metrics
                    $fcpRaw = $audits['first-contentful-paint']['numericValue'] ?? 0;
                    $lcpRaw = $audits['largest-contentful-paint']['numericValue'] ?? 0;
                    $tbtRaw = $audits['total-blocking-time']['numericValue'] ?? 0;
                    $clsRaw = $audits['cumulative-layout-shift']['numericValue'] ?? 0;
                    $siRaw = $audits['speed-index']['numericValue'] ?? 0;
                    $ttiRaw = $audits['interactive']['numericValue'] ?? 0;

                    // CrUX Data
                    $cruxUrl = $data['loadingExperience']['metrics'] ?? null;
                    $cruxOrigin = $data['originLoadingExperience']['metrics'] ?? null;
                @endphp

                <!-- 1. PAGESPEED DONUTS -->
                <div class="ps-scores">
                    <!-- Performance Master -->
                    <div class="ps-score-item">
                        <div class="perf-donut-container">
                            @php $perfColor = getColorClass($perf); @endphp
                            <span class="metric-label ml-fcp">FCP</span>
                            <span class="metric-label ml-si">SI</span>
                            <span class="metric-label ml-lcp">LCP</span>
                            <span class="metric-label ml-tbt">TBT</span>
                            <span class="metric-label ml-cls">CLS</span>
                            
                            <svg viewBox="0 0 36 36" class="circular-chart color-{{ $perfColor }}">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="circle svg-anim" data-score="{{ $perf }}" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <text x="18" y="20.35" class="percentage text-{{ $perfColor }}">{{ $perf }}</text>
                            </svg>
                        </div>
                        <div class="score-title">Performance</div>
                        <div style="font-size: 11px; color: #5f6368; margin-top: 5px;">Values are estimated and may vary.</div>
                    </div>

                    <!-- Other Categories -->
                    @foreach(['Accessibility' => $acc, 'Best Practices' => $bp, 'SEO' => $seo] as $title => $score)
                        @php $cColor = getColorClass($score); @endphp
                        <div class="ps-score-item" style="margin-top: 50px;">
                            <svg viewBox="0 0 36 36" class="circular-chart color-{{ $cColor }}">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="circle svg-anim" data-score="{{ $score }}" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <text x="18" y="20.35" class="percentage text-{{ $cColor }}">{{ $score }}</text>
                            </svg>
                            <div class="score-title">{{ $title }}</div>
                        </div>
                    @endforeach

                    <!-- Agentic Browsing (Mock) -->
                    <div class="ps-score-item" style="margin-top: 50px;">
                        <div class="triangle-wrapper"><div class="triangle"></div></div>
                        <div class="score-title">Agentic Browsing</div>
                        <div class="agentic-badge">▲ 0/2</div>
                    </div>
                </div>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 40px 0;">

                <!-- 2. GTMETRIX HEADER -->
                <div class="gt-header-row">
                    <div>
                        <div class="gt-box-title">GTmetrix Grade</div>
                        <div class="gt-grade-card">
                            <div class="gt-grade-letter" style="color: {{ $gradeColor }};">{{ $grade }}</div>
                            <div class="gt-score-item">
                                <div class="gt-score-label">Performance</div>
                                <div class="gt-score-val" style="color: {{ getHexColor(getColorClass($perf)) }}">{{ $perf }}%</div>
                            </div>
                            <div class="gt-score-item">
                                <div class="gt-score-label">Structure</div>
                                <div class="gt-score-val" style="color: {{ getHexColor(getColorClass($structure)) }}">{{ $structure }}%</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="gt-box-title">Web Vitals</div>
                        <div class="gt-vitals-card">
                            <div class="gt-score-item">
                                <div class="gt-score-label">LCP</div>
                                <div class="gt-score-val" style="color: {{ getMetricStatus($lcpRaw/1000, 2.5, 4.0)['color'] }}">{{ round($lcpRaw/1000, 1) }}s</div>
                            </div>
                            <div class="gt-score-item">
                                <div class="gt-score-label">TBT</div>
                                <div class="gt-score-val" style="color: {{ getMetricStatus($tbtRaw, 200, 600)['color'] }}">{{ round($tbtRaw) }}ms</div>
                            </div>
                            <div class="gt-score-item">
                                <div class="gt-score-label">CLS</div>
                                <div class="gt-score-val" style="color: {{ getMetricStatus($clsRaw, 0.1, 0.25)['color'] }}">{{ number_format($clsRaw, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. CrUX SECTION -->
                @php
                    $isCruxPassed = false;
                    if($cruxUrl && isset($cruxUrl['LARGEST_CONTENTFUL_PAINT_MS']) && isset($cruxUrl['CUMULATIVE_LAYOUT_SHIFT_SCORE'])) {
                        $isCruxPassed = ($cruxUrl['LARGEST_CONTENTFUL_PAINT_MS']['percentile'] <= 2500) && ($cruxUrl['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'] <= 10);
                    }
                @endphp

                <div class="crux-banner {{ $isCruxPassed ? 'crux-passed' : 'crux-failed' }}">
                    <div>
                        <span class="crux-banner-icon">{{ $isCruxPassed ? '✔' : '✖' }}</span> 
                        Core Web Vitals Assessment: {{ $isCruxPassed ? 'Passed' : 'Failed' }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; font-weight: 400; font-size: 13px; color: #555;">
                        Results for: <a href="{{ $report->url }}" style="color: #337ab7; text-decoration: underline;" target="_blank">{{ $report->url }}</a> 🔀
                        <div class="crux-toggle" data-view="{{ $deviceType }}">
                            <span class="btn-crux active" data-target="url-{{ $deviceType }}">This URL</span>
                            <span class="btn-crux" data-target="ori-{{ $deviceType }}">Origin</span>
                        </div>
                    </div>
                </div>

                <!-- Tab Konten CrUX: URL & Origin -->
                @foreach(['url' => $cruxUrl, 'ori' => $cruxOrigin] as $cruxType => $cruxData)
                    <div id="{{ $cruxType }}-{{ $deviceType }}" class="crux-wrapper-{{ $deviceType }}" style="display: {{ $cruxType == 'url' ? 'block' : 'none' }}">
                        @if($cruxData)
                            <div class="crux-grid">
                                @foreach([
                                    ['LARGEST_CONTENTFUL_PAINT_MS', 'Largest Contentful Paint (LCP)', 's', 1000, 2500, 4000, '#f0ad4e', $lcpRaw/1000, 'LCP'],
                                    ['INTERACTION_TO_NEXT_PAINT', 'Interaction to Next Paint (INP)', 'ms', 1, 200, 500, '#9b59b6', $tbtRaw, 'TBT'], // Lighthouse INP approx TBT
                                    ['CUMULATIVE_LAYOUT_SHIFT_SCORE', 'Cumulative Layout Shift (CLS)', '', 100, 0.1, 0.25, '#1abc9c', $clsRaw, 'CLS'],
                                    ['FIRST_CONTENTFUL_PAINT_MS', 'First Contentful Paint (FCP)', 's', 1000, 1800, 3000, '#3498db', $fcpRaw/1000, 'FCP'],
                                    ['EXPERIMENTAL_TIME_TO_FIRST_BYTE', 'Time to First Byte (TTFB)', 's', 1000, 800, 1800, '#34495e', ($audits['server-response-time']['numericValue'] ?? 0)/1000, 'TTFB']
                                ] as $cfg)
                                    @if(isset($cruxData[$cfg[0]]))
                                        @php
                                            $val = $cruxData[$cfg[0]]['percentile'] / $cfg[3];
                                            $good = $cruxData[$cfg[0]]['distributions'][0]['proportion'] * 100;
                                            $needs = $cruxData[$cfg[0]]['distributions'][1]['proportion'] * 100;
                                            $poor = $cruxData[$cfg[0]]['distributions'][2]['proportion'] * 100;
                                            
                                            $cBadge = $val <= $cfg[4] ? ['bg-pass', 'Pass'] : ($val <= $cfg[5] ? ['bg-improve', 'Improve'] : ['bg-poor', 'Poor']);
                                            $cValColor = $val <= $cfg[4] ? '#5cb85c' : ($val <= $cfg[5] ? '#f0ad4e' : '#d9534f');
                                        @endphp
                                        <div class="crux-card" style="border-top: 3px solid {{ $cfg[6] }};">
                                            <div class="crux-card-header">
                                                <span class="crux-card-title">{{ $cfg[1] }}</span>
                                                <span class="crux-badge {{ $cBadge[0] }}">{{ $cBadge[1] }}</span>
                                            </div>
                                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                                <div style="font-size:12px; color:#777;">GTmetrix {{ $cfg[8] }} - 
                                                    <span style="color:#777; font-weight:600;">
                                                        {{ number_format($cfg[7], $cfg[2] == 's' || $cfg[2] == '' ? 2 : 0) }}{{ $cfg[2] }}
                                                    </span>
                                                </div>
                                                <div class="crux-val" style="color: {{ $cValColor }}">{{ number_format($val, 2) }}{{ $cfg[2] }}</div>
                                            </div>
                                            <div class="crux-bar">
                                                <div class="crux-bar-segment" style="width: {{ $good }}%; background-color:#5cb85c;"></div>
                                                <div class="crux-bar-segment" style="width: {{ $needs }}%; background-color:#f0ad4e;"></div>
                                                <div class="crux-bar-segment" style="width: {{ $poor }}%; background-color:#d9534f;"></div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div style="padding: 30px; text-align: center; color: #777; background: #fff; border: 1px solid #ddd; margin-bottom: 30px;">
                                Data pengalaman nyata (CrUX) untuk metrik {{ strtoupper($cruxType) }} ini belum tersedia dari Google.
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- 4. TOP ISSUES & PAGE DETAILS -->
                <div class="two-col-layout">
                    <!-- TOP ISSUES -->
                    <div class="col-left">
                        <div class="section-heading">Top Issues</div>
                        <p style="font-size: 14px; color: #555; margin-bottom: 15px;">These audits are identified as the top issues impacting <strong>your performance</strong>.</p>
                        
                        <div class="filter-row" id="filter-wrap-{{ $deviceType }}">
                            <div class="btn-filter active" data-filter="all">All</div>
                            <div class="btn-filter" data-filter="fcp">FCP</div>
                            <div class="btn-filter" data-filter="lcp">LCP</div>
                            <div class="btn-filter" data-filter="tbt">TBT</div>
                            <div class="btn-filter" data-filter="cls">CLS</div>
                        </div>

                        <div id="issues-list-{{ $deviceType }}">
                            @foreach($audits as $aId => $a)
                                @if(isset($a['score']) && $a['score'] < 0.9 && isset($a['details']['type']) && $a['details']['type'] !== 'opportunity')
                                    @php 
                                        $isHigh = $a['score'] < 0.5;
                                        $impCls = $isHigh ? 'impact-high' : 'impact-med';
                                        $impTxt = $isHigh ? 'High' : 'Med';
                                        
                                        $rel = 'all ';
                                        if(str_contains($aId, 'render-blocking') || str_contains($aId, 'server-response')) $rel .= 'fcp lcp ';
                                        if(str_contains($aId, 'mainthread') || str_contains($aId, 'javascript') || str_contains($aId, 'bootup')) $rel .= 'tbt ';
                                        if(str_contains($aId, 'layout-shift') || str_contains($aId, 'image-size')) $rel .= 'cls ';
                                        if(str_contains($aId, 'network-payloads')) $rel .= 'lcp ';
                                    @endphp
                                    <div class="issue-row filterable-issue" data-rel="{{ $rel }}" onclick="this.classList.toggle('expanded')">
                                        <div class="issue-header">
                                            <div class="issue-impact {{ $impCls }}">{{ $impTxt }}</div>
                                            <div class="issue-title">
                                                {{ $a['title'] }}
                                                @if(str_contains($rel, 'fcp')) <span class="issue-badge">FCP</span> @endif
                                                @if(str_contains($rel, 'lcp')) <span class="issue-badge">LCP</span> @endif
                                                @if(str_contains($rel, 'tbt')) <span class="issue-badge">TBT</span> @endif
                                                @if(str_contains($rel, 'cls')) <span class="issue-badge">CLS</span> @endif
                                            </div>
                                            <div style="padding: 10px; color: #aaa; font-size: 12px;">▼</div>
                                        </div>
                                        <div class="issue-desc">
                                            {!! strip_tags($a['description'], '<a><code>') !!}
                                            @if(isset($a['displayValue'])) <br><br><strong>Value:</strong> {{ $a['displayValue'] }} @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- PAGE DETAILS -->
                    <div class="col-right">
                        @php
                            $resSum = $audits['resource-summary']['details']['items'] ?? [];
                            $totSize = 0; $totReq = 0;
                            $resMap = [];
                            foreach($resSum as $rs) {
                                if($rs['resourceType'] == 'total') {
                                    $totSize = $rs['transferSize']; $totReq = $rs['requestCount'];
                                } else {
                                    $resMap[$rs['resourceType']] = ['size' => $rs['transferSize'], 'req' => $rs['requestCount']];
                                }
                            }
                        @endphp
                        
                        <div class="section-heading">Page Details</div>
                        <p style="font-size: 13px; color: #777; margin-bottom: 30px;">Pages with smaller total sizes and fewer requests tend to load faster.</p>
                        
                        <div style="border-top: 2px solid #eee; position: relative; margin-bottom: 40px; margin-top: 40px;">
                            <div style="position:absolute; left: 50%; top: -10px; background: #fff; padding: 0 15px; transform: translateX(-50%); text-align: center;">
                                <div style="font-size: 18px; color: #444;">{{ round($ttiRaw/1000, 1) }}s</div>
                                <div style="font-size: 11px; color: #777;">Fully Loaded Time</div>
                            </div>
                        </div>

                        <div class="pd-total">Total Page Size - {{ formatBytes($totSize) }}</div>
                        <div class="pd-bar-wrapper">
                            @foreach($resMap as $type => $dt)
                                @if($dt['size'] > 0)
                                    <div class="pd-segment pd-color-{{ $type }}" style="width: {{ ($dt['size']/$totSize)*100 }}%;" title="{{ $type }}: {{ formatBytes($dt['size']) }}">
                                        @if(($dt['size']/$totSize)*100 > 10) {{ strtoupper(substr($type,0,3)) }}<br>{{ formatBytes($dt['size']) }} @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="pd-total">Total Page Requests - {{ $totReq }}</div>
                        <div class="pd-bar-wrapper">
                            @foreach($resMap as $type => $dt)
                                @if($dt['req'] > 0)
                                    <div class="pd-segment pd-color-{{ $type }}" style="width: {{ ($dt['req']/$totReq)*100 }}%;" title="{{ $type }}: {{ $dt['req'] }}">
                                        @if(($dt['req']/$totReq)*100 > 10) {{ strtoupper(substr($type,0,3)) }}<br>{{ round(($dt['req']/$totReq)*100) }}% @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 5. PERFORMANCE METRICS -->
                <div class="section-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    Performance Metrics
                    <div class="toggle-details-wrap">
                        Metric details
                        <label class="switch">
                            <input type="checkbox" class="toggle-metrics-chk" data-target="metrics-desc-{{ $deviceType }}" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                
                <div class="metrics-grid">
                    @foreach([
                        ['First Contentful Paint', $fcpRaw/1000, 's', 1.8, 3.0, 'br-blue', 'How quickly content like text or images are painted onto your page.'],
                        ['Time to Interactive', $ttiRaw/1000, 's', 3.8, 7.3, 'br-purple', 'How long it takes for your page to become fully interactive.'],
                        ['Speed Index', $siRaw/1000, 's', 3.4, 5.8, 'br-pink', 'How quickly the contents of your page are visibly populated.'],
                        ['Total Blocking Time', $tbtRaw, 'ms', 200, 600, 'br-dark', 'How much time is blocked by scripts during your page loading process.'],
                        ['Largest Contentful Paint', $lcpRaw/1000, 's', 2.5, 4.0, 'br-blue', 'How long it takes for the largest element of content to be painted.'],
                        ['Cumulative Layout Shift', $clsRaw, '', 0.1, 0.25, 'br-teal', 'How much your page\'s layout shifts as it loads.']
                    ] as $m)
                        @php $st = getMetricStatus($m[1], $m[3], $m[4]); @endphp
                        <div class="metric-card-border">
                            <div class="metric-card-color {{ $m[5] }}"></div>
                            <div class="metric-card-content">
                                <div class="mc-badge" style="background: {{ $st['bg'] }}">{{ $st['badge'] }}</div>
                                <div class="mc-title">{{ $m[0] }}</div>
                                <div class="mc-desc metrics-desc-{{ $deviceType }}">{{ $m[6] }}</div>
                                <div class="mc-val" style="color: {{ $st['color'] }}">{{ number_format($m[1], $m[2]=='' ? 2 : 1) }}{{ $m[2] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 6. BROWSER TIMINGS -->
                <div class="section-heading">Browser Timings</div>
                <div class="timings-grid">
                    @php $srt = $audits['server-response-time']['numericValue'] ?? 0; @endphp
                    <div class="timing-card"><div class="t-label">Redirect Duration</div><div class="t-val">N/A</div></div>
                    <div class="timing-card"><div class="t-label">Connection Duration</div><div class="t-val">N/A</div></div>
                    <div class="timing-card" style="border-left-color:#ccc;"><div class="t-label">Backend Duration</div><div class="t-val">{{ round($srt) }}ms</div></div>
                    <div class="timing-card" style="border-left-color:#7f8c8d;"><div class="t-label">Time to First Byte</div><div class="t-val">{{ round($srt) }}ms</div></div>
                    <div class="timing-card" style="border-left-color:#34495e;"><div class="t-label">First Paint</div><div class="t-val">{{ round($fcpRaw/1000, 1) }}s</div></div>
                    <div class="timing-card" style="border-left-color:#5f9ea0;"><div class="t-label">DOM Interactive</div><div class="t-val">{{ round($ttiRaw/1000, 1) }}s</div></div>
                    <div class="timing-card" style="border-left-color:#c0392b;"><div class="t-label">Fully Loaded Time</div><div class="t-val">{{ round($ttiRaw/1000, 1) }}s</div></div>
                </div>

            </div>
        @endforeach

    </div>

    <!-- SCRIPT LOGIC JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Tab Switching (Mobile / Desktop)
            const mainTabs = document.querySelectorAll('.ps-tab[data-target]');
            const deviceViews = document.querySelectorAll('.device-view');
            
            mainTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    mainTabs.forEach(t => t.classList.remove('active'));
                    deviceViews.forEach(v => v.classList.remove('active'));
                    
                    tab.classList.add('active');
                    document.getElementById(tab.getAttribute('data-target')).classList.add('active');
                    
                    // Trigger animasi SVG ulang saat tab diganti
                    triggerSvgAnimations();
                });
            });

            // 2. SVG Donut Animation Logic
            function triggerSvgAnimations() {
                // Reset to 0 first
                document.querySelectorAll('.svg-anim').forEach(el => {
                    el.style.strokeDasharray = '0, 100';
                });
                
                // Add tiny delay to allow reflow, then animate to actual score
                setTimeout(() => {
                    document.querySelectorAll('.device-view.active .svg-anim').forEach(el => {
                        const score = el.getAttribute('data-score');
                        el.style.strokeDasharray = `${score}, 100`;
                    });
                }, 100);
            }
            // Trigger pada load pertama
            triggerSvgAnimations();

            // 3. CrUX Toggle (URL vs Origin)
            const cruxToggles = document.querySelectorAll('.btn-crux');
            cruxToggles.forEach(btn => {
                btn.addEventListener('click', function() {
                    const parentView = this.closest('.crux-toggle').getAttribute('data-view');
                    const targetId = this.getAttribute('data-target');
                    
                    // Hapus active state dari tombol toggle di view ini saja
                    this.parentElement.querySelectorAll('.btn-crux').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Sembunyikan semua wrapper CrUX di view ini, tampilkan yg dipilih
                    document.querySelectorAll(`.crux-wrapper-${parentView}`).forEach(w => w.style.display = 'none');
                    document.getElementById(targetId).style.display = 'block';
                });
            });

            // 4. Issues Filter Logic
            document.querySelectorAll('.filter-row').forEach(row => {
                const btns = row.querySelectorAll('.btn-filter');
                btns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Ganti status aktif
                        btns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Eksekusi filter
                        const filterVal = this.getAttribute('data-filter');
                        const issueListId = this.closest('.col-left').querySelector('[id^="issues-list"]').id;
                        const issueRows = document.querySelectorAll(`#${issueListId} .filterable-issue`);
                        
                        issueRows.forEach(ir => {
                            if (ir.getAttribute('data-rel').includes(filterVal)) {
                                ir.style.display = 'flex';
                            } else {
                                ir.style.display = 'none';
                            }
                        });
                    });
                });
            });

            // 5. Toggle Performance Metrics Description
            const metricToggles = document.querySelectorAll('.toggle-metrics-chk');
            metricToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const targetClass = this.getAttribute('data-target');
                    const descs = document.querySelectorAll(`.${targetClass}`);
                    descs.forEach(desc => {
                        desc.style.display = this.checked ? 'block' : 'none';
                    });
                });
                // Initialize default state
                toggle.dispatchEvent(new Event('change'));
            });

        });
    </script>
</body>
</html>