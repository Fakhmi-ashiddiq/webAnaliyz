<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Analyzer - Result: {{ $report->url }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Light Theme (Default) */
            --bg-body: #f8f9fa;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --primary: #3b82f6;
            
            --green: #10b981;
            --orange: #f59e0b;
            --red: #ef4444;
            --green-bg: rgba(16, 185, 129, 0.15);
            --orange-bg: rgba(245, 158, 11, 0.15);
            --red-bg: rgba(239, 68, 68, 0.15);
        }

        [data-theme="dark"] {
            /* Dark Theme */
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --border-color: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --green-bg: rgba(16, 185, 129, 0.2);
            --orange-bg: rgba(245, 158, 11, 0.2);
            --red-bg: rgba(239, 68, 68, 0.2);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; transition: background-color 0.3s, border-color 0.3s; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            padding-bottom: 50px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            margin-bottom: 30px;
        }
        .url-title { display: flex; align-items: center; gap: 15px; font-size: 1.2rem; font-weight: 600; }
        .url-link { color: var(--primary); text-decoration: none; }
        .url-link:hover { text-decoration: underline; }
        .badge-strategy { background: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid rgba(59, 130, 246, 0.3); font-weight: 600; }
        
        .header-actions { display: flex; align-items: center; gap: 15px; }
        .btn-back { padding: 8px 16px; background: var(--bg-card); color: var(--text-main); border-radius: 8px; font-weight: 500; font-size: 0.9rem; text-decoration: none; border: 1px solid var(--border-color); }
        .btn-back:hover { background: var(--border-color); }

        /* Theme Toggle */
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .theme-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .3s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--primary); }
        input:checked + .slider:before { transform: translateX(20px); }

        /* Scores Section */
        .scores-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-bottom: 40px;
            padding: 40px 20px;
        }
        
        .score-circle { display: flex; flex-direction: column; align-items: center; }
        .score-label { margin-top: 15px; font-size: 15px; color: var(--text-main); font-weight: 500; }
        
        /* Basic SVG */
        .donut-wrapper { position: relative; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; margin-bottom: 5px; }
        .chart-svg { width: 100%; height: 100%; position: absolute; top: 0; left: 0; }
        .circle-bg { fill: none; stroke: var(--border-color); stroke-width: 2.5; }
        .circle-fill { fill: none; stroke-width: 2.5; stroke-linecap: round; transition: 1s ease-out; }
        
        .score-val-text { position: relative; font-family: 'Outfit', sans-serif; font-size: 38px; font-weight: 500; color: var(--text-main); z-index: 10; line-height: 1; margin-top: 5px; }
        
        .score-circle { display: flex; flex-direction: column; align-items: center; }

        /* Grade and Vitals */
        .grade-vitals-container { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 30px; margin-bottom: 20px; }
        .gv-card-wrapper { flex: 1; min-width: 300px; }
        .gv-title { font-size: 16px; font-weight: 400; color: var(--primary); margin-bottom: 8px; display: flex; align-items: center; gap: 5px; }
        .gv-tooltip { display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border-radius: 50%; background: var(--border-color); color: var(--text-muted); font-size: 10px; cursor: help; }
        .gv-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; display: flex; padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .gv-divider { width: 1px; background: var(--border-color); margin: 0 25px; }
        
        .gv-box { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .gv-grade-box { align-items: center; justify-content: center; flex: 0 0 auto; padding-right: 5px; }
        .gv-grade-letter { font-size: 72px; font-weight: 700; line-height: 1; font-family: 'Outfit', sans-serif; }
        
        .gv-box-title { font-size: 14px; color: var(--text-muted); margin-bottom: 5px; display: flex; align-items: center; gap: 4px; }
        .gv-box-value { font-size: 32px; font-weight: 300; color: var(--text-main); font-family: 'Outfit', sans-serif; }


        /* Agentic Browsing */
        .agentic-box { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 90px; height: 90px; border-radius: 50%; background: var(--red-bg); }
        .agentic-val { color: var(--red); font-weight: 800; font-size: 20px; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 5px; }
        
        /* CrUX Section */
        .crux-container { margin-top: 20px; background: var(--card-bg); border-radius: 4px; border: 1px solid var(--border-color); padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .crux-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .crux-status-area { display: flex; align-items: center; gap: 12px; }
        .crux-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        .crux-icon.pass { background: var(--green); }
        .crux-icon.fail { background: var(--red); }
        .crux-title { font-size: 18px; font-weight: 400; color: var(--text-main); margin: 0; }
        .crux-toggle-area { display: flex; align-items: center; gap: 10px; }
        .crux-toggle-label { font-size: 13px; color: var(--text-muted); }
        .crux-btn-group { display: flex; border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; }
        .crux-btn { background: transparent; border: none; padding: 6px 14px; font-size: 13px; color: var(--text-main); cursor: pointer; transition: 0.2s; outline: none; }
        .crux-btn.active { background: var(--primary); color: white; }
        
        .crux-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .crux-separator { border-top: 1px solid var(--border-color); margin: 30px 0; padding-top: 15px; font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .crux-card { display: flex; flex-direction: column; }
        .crux-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .crux-card-title { font-size: 13px; font-weight: 500; color: var(--text-main); text-decoration: underline; text-decoration-color: var(--border-color); text-underline-offset: 3px; line-height: 1.3; }
        .crux-badge { font-size: 11px; font-weight: 500; padding: 2px 10px; border-radius: 3px; color: white; text-transform: capitalize; }
        .crux-badge.pass { background: var(--green); }
        .crux-badge.improve { background: var(--orange); }
        .crux-badge.fail { background: var(--red); }
        .crux-card-val { font-size: 20px; font-weight: 400; color: var(--text-main); text-align: right; margin-bottom: 5px; }
        
        .crux-bar-container { position: relative; height: 6px; display: flex; gap: 2px; margin-bottom: 15px; margin-top: 5px; }
        .crux-bar-seg { height: 100%; border-radius: 1px; }
        .bg-green { background: var(--green); }
        .bg-orange { background: var(--orange); }
        .bg-red { background: var(--red); }
        .crux-needle { position: absolute; top: -4px; bottom: -4px; width: 2px; background: var(--text-main); border-radius: 1px; box-shadow: 0 0 0 2px var(--card-bg); transition: left 0.5s ease-out; z-index: 2; left: 0%; }
        .crux-needle::after { content: ''; position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); border: 3px solid transparent; border-top-color: var(--text-main); }
        
        .crux-table-wrap { font-size: 11px; color: var(--text-muted); }
        .crux-table-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-color); }
        .crux-table-row:last-child { border-bottom: none; }
        .crux-table-row small { opacity: 0.7; }

        #error-box { display: none; background: var(--red-bg); border: 1px solid var(--red); color: var(--red); padding: 20px; text-align: center; border-radius: 8px; margin-bottom: 30px; font-weight: 500; }
        #app-content { display: none; }

        /* Performance Metrics Section */
        .pm-container { margin-top: 40px; }
        .pm-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        .pm-toggle-wrap { display: flex; align-items: center; gap: 10px; }
        .pm-toggle-label { font-size: 13px; color: var(--text-muted); }
        
        .pm-switch { position: relative; display: inline-block; width: 60px; height: 26px; }
        .pm-switch input { opacity: 0; width: 0; height: 0; }
        .pm-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #3498db; transition: .4s; border-radius: 26px; display: flex; align-items: center; justify-content: space-between; padding: 0 8px; }
        .pm-slider::before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; z-index: 2; }
        .pm-switch input:checked + .pm-slider { background-color: #3498db; }
        .pm-switch input:not(:checked) + .pm-slider { background-color: #ccc; }
        .pm-switch input:checked + .pm-slider::before { transform: translateX(34px); }
        .pm-slider-text { color: white; font-size: 10px; font-weight: bold; z-index: 1; }
        .pm-slider-text.off { display: none; margin-left: 15px; }
        .pm-switch input:not(:checked) + .pm-slider .pm-slider-text.on { display: none; }
        .pm-switch input:not(:checked) + .pm-slider .pm-slider-text.off { display: block; }

        .pm-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .pm-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; border-left: 4px solid var(--card-border, #ccc); display: flex; justify-content: space-between; min-height: 100px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .pm-card-left { padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .pm-card-title { font-size: 18px; font-weight: 300; color: var(--text-main); margin: 0; }
        .pm-card-desc { font-size: 12px; color: var(--text-muted); margin-top: 8px; line-height: 1.4; transition: opacity 0.3s, max-height 0.3s, margin-top 0.3s; max-height: 100px; overflow: hidden; opacity: 1; }
        .pm-card-desc.hidden { max-height: 0; opacity: 0; margin-top: 0; }
        
        .pm-card-right { width: 140px; display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; border-left: 1px solid var(--border-color); background: #fdfdfd; }
        [data-theme="dark"] .pm-card-right { background: #1a1a1a; }
        .pm-badge { width: 100%; text-align: center; padding: 6px 5px; font-size: 10px; color: white; font-weight: 500; }
        .pm-val { font-size: 32px; font-family: 'Outfit', sans-serif; font-weight: 300; padding: 15px 20px; display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; }
        .pm-learn-more { color: #3498db; text-decoration: none; font-weight: 500; }
        .pm-learn-more:hover { text-decoration: underline; }

        /* Browser Timings Section */
        .bt-container { margin-top: 40px; }
        .bt-header { margin-bottom: 20px; }
        .bt-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .bt-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; border-left: 4px solid var(--bt-border, #ccc); display: flex; justify-content: space-between; align-items: center; padding: 25px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .bt-card-title { font-size: 15px; font-weight: 400; color: var(--text-muted); }
        .bt-card-val { font-size: 20px; font-weight: 400; color: var(--text-main); font-family: 'Outfit', sans-serif; }

        /* Issues & Details Section */
        .issues-details-container { display: flex; flex-wrap: wrap; gap: 30px; margin-top: 30px; }
        .issues-col { flex: 3; min-width: 350px; }
        .details-col { flex: 2; min-width: 300px; background: var(--card-bg); border-radius: 4px; border: 1px solid var(--border-color); padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        
        .section-title { font-size: 22px; font-weight: 300; color: var(--text-main); margin: 0 0 5px 0; }
        .section-desc { font-size: 13px; color: var(--text-muted); margin: 0; line-height: 1.4; }
        
        .issues-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }
        .issues-filters { display: flex; gap: 4px; }
        .filter-btn { background: var(--card-bg); border: 1px solid var(--border-color); padding: 6px 14px; border-radius: 4px; font-size: 13px; color: var(--text-muted); cursor: pointer; transition: 0.2s; }
        .filter-btn:hover { background: var(--border-color); color: var(--text-main); }
        .filter-btn.active { background: #22679c; color: white; border-color: #22679c; font-weight: 500; } 
        
        /* Accordion List */
        .issues-list { display: flex; flex-direction: column; gap: 8px; }
        .issue-item { border: 1px solid var(--border-color); border-radius: 4px; background: var(--card-bg); overflow: hidden; }
        .issue-summary { display: flex; align-items: stretch; cursor: pointer; }
        .issue-summary:hover { background: rgba(0,0,0,0.02); }
        [data-theme="dark"] .issue-summary:hover { background: rgba(255,255,255,0.02); }
        
        .issue-severity { width: 85px; padding: 12px 10px; text-align: center; color: white; font-weight: 500; font-size: 14px; display: flex; flex-direction: column; justify-content: center; }
        .sev-high { background: #e06d6b; } /* Soft red */
        .sev-med { background: #f3b34c; } /* Soft orange */
        .sev-low { background: #98c65f; } /* Soft green */
        
        .issue-title-wrap { flex: 1; padding: 10px 15px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px; border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color); }
        .issue-title { font-size: 14px; font-weight: 600; color: #22679c; margin: 0; } 
        .issue-tags { display: flex; gap: 4px; }
        .issue-tag { background: #e9ecef; color: #6c757d; font-size: 10px; padding: 2px 6px; border-radius: 3px; border: 1px solid #dee2e6; text-transform: uppercase; }
        [data-theme="dark"] .issue-tag { background: #333; border-color: #444; color: #ccc; }
        [data-theme="dark"] .issue-title { color: #6ba7d4; }
        
        .issue-icon { width: 40px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); transition: transform 0.3s; }
        .issue-item.open .issue-icon { transform: rotate(180deg); }
        
        .issue-details { display: none; padding: 15px; border-top: 1px solid var(--border-color); font-size: 13px; color: var(--text-main); background: #fcfcfc; }
        [data-theme="dark"] .issue-details { background: #1a1a1a; }
        .issue-item.open .issue-details { display: block; }
        
        /* Details Column */
        .details-header { margin-bottom: 30px; }
        
        .detail-timeline { position: relative; margin: 30px 0 40px 0; padding-top: 10px; }
        .timeline-track { height: 4px; background: #e9ecef; width: 100%; position: relative; border-radius: 2px; }
        [data-theme="dark"] .timeline-track { background: #444; }
        .timeline-track::before, .timeline-track::after { content: ''; position: absolute; width: 3px; height: 16px; background: #dee2e6; top: -6px; }
        [data-theme="dark"] .timeline-track::before, [data-theme="dark"] .timeline-track::after { background: #555; }
        .timeline-track::before { left: 0; }
        .timeline-track::after { right: 0; }
        .timeline-marker { position: absolute; top: -35px; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; }
        .marker-val { font-size: 24px; color: var(--text-main); font-family: 'Outfit', sans-serif; font-weight: 400; line-height: 1; }
        .marker-label { font-size: 11px; color: var(--text-muted); font-weight: 600; white-space: nowrap; margin-top: 15px; text-transform: uppercase; }

        .detail-block { margin-bottom: 25px; }
        .detail-title { font-size: 18px; color: var(--text-muted); margin-bottom: 12px; font-weight: 300; }
        .detail-title span { color: var(--text-main); font-weight: 400; }
        
        .stacked-bar { display: flex; height: 50px; border-radius: 4px; overflow: hidden; width: 100%; }
        .bar-seg { display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; font-size: 11px; font-weight: 500; line-height: 1.2; border-right: 1px solid rgba(255,255,255,0.3); transition: width 0.5s ease; white-space: nowrap; overflow: hidden; text-overflow: clip; padding: 0 4px; }
        .bar-seg:last-child { border-right: none; }
        
        .c-img { background: #5683a6; } /* Blue */
        .c-js { background: #71889c; } /* Blue-gray */
        .c-css { background: #967c9c; } /* Purple */
        .c-font { background: #b07e99; } /* Pink */
        .c-html { background: #507fb0; } /* Light Blue */
        .c-other { background: #a6a6a6; } /* Gray */
        .c-media { background: #6b5c77; } /* Dark Purple */
        
        /* Utilities */
        .c-green { color: var(--green); } .s-green { stroke: var(--green); } .bg-green { stroke: var(--green-bg); }
        .c-orange { color: var(--orange); } .s-orange { stroke: var(--orange); } .bg-orange { stroke: var(--orange-bg); }
        .c-red { color: var(--red); } .s-red { stroke: var(--red); } .bg-red { stroke: var(--red-bg); }
    </style>
</head>
<body>

    <div class="container">
        @include('analyzer.partials.header')

        <div id="error-box">Failed to load or parse API data. Please try again.</div>

        <div id="app-content">
            <!-- Main Dashboard -->
            <div class="dashboard-container">
                
                <!-- Tab: Performance -->
                <div id="tab-performance" class="tab-pane active">
                    <div class="dashboard-card">
                        <h2 style="font-size: 18px; margin-bottom: 25px; color: var(--text-main);">Performance Metrics</h2>
                        @include('analyzer.partials.scores')
                        
                        <!-- NEW: Grade & Vitals Section -->
                        @include('analyzer.partials.grade-vitals')

                        <!-- NEW: Performance Metrics (6 Cards) Section -->
                        @include('analyzer.partials.perf-metrics')

                        <!-- NEW: Core Web Vitals (CrUX) Section -->
                        @include('analyzer.partials.crux-vitals')

                        <!-- NEW: Top Issues & Page Details Section -->
                        @include('analyzer.partials.issues-details')

                        <!-- NEW: Browser Timings Section -->
                        @include('analyzer.partials.browser-timings')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const rawJson = @json($report->raw_api_data);
        let data = null;

        // Theme Toggle Logic
        const themeToggle = document.getElementById('theme-toggle');
        const root = document.documentElement;
        
        themeToggle.addEventListener('change', (e) => {
            if(e.target.checked) {
                root.setAttribute('data-theme', 'dark');
            } else {
                root.setAttribute('data-theme', 'light');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            try {
                data = JSON.parse(rawJson);
                if(data && data.pagespeed && data.pagespeed.lighthouseResult) {
                    document.getElementById('app-content').style.display = 'block';
                    initDashboard(data);
                } else {
                    document.getElementById('error-box').style.display = 'block';
                }
            } catch (e) {
                console.error("Data parse error:", e);
                document.getElementById('error-box').style.display = 'block';
            }
        });

        function getScoreStatus(scoreVal) {
            if (scoreVal >= 90) return 'green';
            if (scoreVal >= 50) return 'orange';
            return 'red';
        }

        function initDashboard(data) {
            // Header Strategy Badge
            const strategy = data.strategy || 'DESKTOP';
            const badge = document.getElementById('strategy-badge');
            if(badge) badge.textContent = strategy;

            // Lighthouse Data
            const lh = data.pagespeed.lighthouseResult;
            const cats = lh.categories || {};
            const audits = lh.audits || {};

            // STANDARD SCORES (Performance, Accessibility, Best Practices, SEO)
            const perfScore = cats.performance ? Math.round(cats.performance.score * 100) : 0;
            const scores = {
                'Performance': perfScore,
                'Accessibility': cats.accessibility ? Math.round(cats.accessibility.score * 100) : 0,
                'BestPractices': cats['best-practices'] ? Math.round(cats['best-practices'].score * 100) : 0,
                'SEO': cats.seo ? Math.round(cats.seo.score * 100) : 0
            };
            
            for(const [key, val] of Object.entries(scores)) {
                const svg = document.getElementById(`svg-${key}`);
                const text = document.getElementById(`val-${key}`);
                const colorTheme = getScoreStatus(val);
                
                if(svg && text) {
                    text.textContent = val;
                    text.style.color = `var(--${colorTheme})`;
                    svg.style.stroke = `var(--${colorTheme})`;
                    setTimeout(() => { svg.style.strokeDasharray = `${val}, 100`; }, 100);
                }
            }

            // 2. GRADE & WEB VITALS
            const structScore = scores['BestPractices']; // Menggunakan Best Practices sebagai Structure
            
            // Hitung Grade berdasarkan Performance
            let gradeLetter = 'F';
            let gradeColor = 'red';
            if(perfScore >= 90) { gradeLetter = 'A'; gradeColor = 'green'; }
            else if(perfScore >= 80) { gradeLetter = 'B'; gradeColor = 'green'; }
            else if(perfScore >= 70) { gradeLetter = 'C'; gradeColor = 'orange'; }
            else if(perfScore >= 60) { gradeLetter = 'D'; gradeColor = 'orange'; }
            else if(perfScore >= 50) { gradeLetter = 'E'; gradeColor = 'red'; }

            // Set Grade UI
            const elGradeLetter = document.getElementById('val-GradeLetter');
            const elGradePerf = document.getElementById('val-GradePerf');
            const elGradeStruct = document.getElementById('val-GradeStruct');
            
            if(elGradeLetter) {
                elGradeLetter.textContent = gradeLetter;
                elGradeLetter.style.color = `var(--${gradeColor})`;
                
                elGradePerf.textContent = `${perfScore}%`;
                elGradePerf.style.color = `var(--${getScoreStatus(perfScore)})`;
                
                elGradeStruct.textContent = `${structScore}%`;
                elGradeStruct.style.color = `var(--${getScoreStatus(structScore)})`;
            }

            // Set Vitals UI
            const elLCP = document.getElementById('val-VitalsLCP');
            const elTBT = document.getElementById('val-VitalsTBT');
            const elCLS = document.getElementById('val-VitalsCLS');

            if(elLCP && audits['largest-contentful-paint']) {
                const lcpMs = audits['largest-contentful-paint'].numericValue || 0;
                const lcpSec = (lcpMs / 1000).toFixed(1);
                elLCP.textContent = `${lcpSec}s`;
                elLCP.style.color = `var(--${getScoreStatus(audits['largest-contentful-paint'].score * 100)})`;
            }

            if(elTBT && audits['total-blocking-time']) {
                const tbtMs = Math.round(audits['total-blocking-time'].numericValue || 0);
                elTBT.textContent = `${tbtMs}ms`;
                elTBT.style.color = `var(--${getScoreStatus(audits['total-blocking-time'].score * 100)})`;
            }

            if(elCLS && audits['cumulative-layout-shift']) {
                const clsVal = (audits['cumulative-layout-shift'].numericValue || 0).toFixed(2);
                elCLS.textContent = clsVal;
                elCLS.style.color = `var(--${getScoreStatus(audits['cumulative-layout-shift'].score * 100)})`;
            }

            // 3. AGENTIC BROWSING SCORE (Custom)
            const agenticVal = document.getElementById('agentic-val');
            if(agenticVal) {
                // Not requested anymore but kept element reference check to prevent error
            }

            // 4. CORE WEB VITALS (CrUX)
            const rawPagespeed = data.pagespeed || {};
            const cruxThisUrl = rawPagespeed.loadingExperience || null;
            const cruxOrigin = rawPagespeed.originLoadingExperience || null;
            
            const btnThisUrl = document.getElementById('btn-this-url');
            const btnOrigin = document.getElementById('btn-origin');
            
            function renderCrux(cruxData) {
                if(!cruxData || !cruxData.metrics || Object.keys(cruxData.metrics).length === 0) {
                    document.getElementById('crux-empty-state').style.display = 'block';
                    document.getElementById('crux-metrics-content').style.display = 'none';
                    document.getElementById('crux-status-text').textContent = 'Failed (No Data)';
                    document.getElementById('crux-status-text').style.color = 'var(--red)';
                    const iconEl = document.getElementById('crux-status-icon');
                    if(iconEl) {
                        iconEl.className = 'crux-icon fail';
                        iconEl.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                    }
                    return;
                }
                
                document.getElementById('crux-empty-state').style.display = 'none';
                document.getElementById('crux-metrics-content').style.display = 'block';
                
                // Overall Assessment
                const overall = cruxData.overall_category || 'AVERAGE';
                const isPassed = overall === 'FAST' || overall === 'AVERAGE';
                document.getElementById('crux-status-text').textContent = isPassed ? 'Passed' : 'Failed';
                document.getElementById('crux-status-text').style.color = isPassed ? 'var(--green)' : 'var(--red)';
                
                const iconClass = isPassed ? 'pass' : 'fail';
                const iconSvg = isPassed 
                    ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>'
                    : '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                
                const iconEl = document.getElementById('crux-status-icon');
                if(iconEl) {
                    iconEl.className = `crux-icon ${iconClass}`;
                    iconEl.innerHTML = iconSvg;
                }
                
                // Helper to populate card
                const populateMetric = (cardId, metricData, unit, divider) => {
                    const badge = document.getElementById(`badge-${cardId}`);
                    const needle = document.getElementById(`needle-${cardId}`);
                    if(!metricData) {
                        document.getElementById(`val-${cardId}`).textContent = 'N/A';
                        if(badge) {
                            badge.textContent = 'N/A';
                            badge.className = 'crux-badge';
                            badge.style.background = 'var(--border-color)';
                            badge.style.color = 'var(--text-muted)';
                        }
                        document.getElementById(`dist-${cardId}-good`).textContent = '-';
                        document.getElementById(`dist-${cardId}-ni`).textContent = '-';
                        document.getElementById(`dist-${cardId}-poor`).textContent = '-';
                        if(needle) needle.style.left = '0%';
                        return;
                    }
                    
                    const p = metricData.percentile;
                    let val = divider ? (p / divider).toFixed(divider > 10 ? 1 : 2) : p;
                    // Fix CLS to 2 decimals
                    if(cardId === 'cls') val = (p / divider).toFixed(2);
                    
                    document.getElementById(`val-${cardId}`).textContent = val + (unit ? ' ' + unit : '');
                    
                    const cat = metricData.category || 'AVERAGE';
                    let badgeClass = 'improve';
                    let badgeText = 'Improve';
                    if(cat === 'FAST') { badgeClass = 'pass'; badgeText = 'Pass'; }
                    else if(cat === 'SLOW') { badgeClass = 'fail'; badgeText = 'Fail'; }
                    
                    if(badge) {
                        badge.className = `crux-badge ${badgeClass}`;
                        badge.textContent = badgeText;
                        badge.style.background = ''; // reset inline style
                        badge.style.color = '';
                    }
                    
                    // Distributions
                    const dists = metricData.distributions || [];
                    const goodP = dists[0] ? Math.round(dists[0].proportion * 100) : 0;
                    const niP = dists[1] ? Math.round(dists[1].proportion * 100) : 0;
                    const poorP = dists[2] ? Math.round(dists[2].proportion * 100) : 0;
                    
                    document.getElementById(`dist-${cardId}-good`).textContent = goodP + '%';
                    document.getElementById(`dist-${cardId}-ni`).textContent = niP + '%';
                    document.getElementById(`dist-${cardId}-poor`).textContent = poorP + '%';
                    
                    // Calculate needle position
                    const maxGood = dists[0] && dists[0].max ? dists[0].max : 0;
                    const maxNi = dists[1] && dists[1].max ? dists[1].max : 0;
                    
                    let pos = 0;
                    if(p <= maxGood && maxGood > 0) {
                        pos = (p / maxGood) * 33.33;
                    } else if(p <= maxNi && maxNi > 0) {
                        pos = 33.33 + ((p - maxGood) / (maxNi - maxGood)) * 33.33;
                    } else {
                        const poorMax = maxNi * 1.5; // Cap visualization to prevent needle disappearing
                        let poorRatio = (p - maxNi) / (poorMax - maxNi);
                        if(poorRatio > 0.95) poorRatio = 0.95;
                        pos = 66.66 + (poorRatio * 33.33);
                    }
                    
                    if(needle) {
                        setTimeout(() => { needle.style.left = pos + '%'; }, 200);
                    }
                };

                const metrics = cruxData.metrics || {};
                populateMetric('lcp', metrics.LARGEST_CONTENTFUL_PAINT_MS, 's', 1000);
                populateMetric('inp', metrics.INTERACTION_TO_NEXT_PAINT, 'ms', 1);
                populateMetric('cls', metrics.CUMULATIVE_LAYOUT_SHIFT_SCORE, '', 100);
                populateMetric('fcp', metrics.FIRST_CONTENTFUL_PAINT_MS, 's', 1000);
                populateMetric('ttfb', metrics.EXPERIMENTAL_TIME_TO_FIRST_BYTE, 's', 1000);
            }
            
            // Initial render
            renderCrux(cruxThisUrl);
            
            if(btnThisUrl && btnOrigin) {
                btnThisUrl.addEventListener('click', () => {
                    btnThisUrl.classList.add('active');
                    btnOrigin.classList.remove('active');
                    renderCrux(cruxThisUrl);
                });
                
                btnOrigin.addEventListener('click', () => {
                    btnOrigin.classList.add('active');
                    btnThisUrl.classList.remove('active');
                    renderCrux(cruxOrigin);
                });
            }

            // 5. TOP ISSUES & PAGE DETAILS
            
            // A. Page Details (Resource Summary)
            const rsAudit = audits['resource-summary'];
            const ttiAudit = audits['interactive'];
            
            if(ttiAudit) {
                const ttiMs = ttiAudit.numericValue || 0;
                const ttiS = (ttiMs / 1000).toFixed(1);
                document.getElementById('val-fully-loaded').textContent = `${ttiS}s`;
            }
            
            if(rsAudit && rsAudit.details && rsAudit.details.items) {
                const items = rsAudit.details.items;
                let totalSize = 0;
                let totalReq = 0;
                
                const typeMap = {
                    'image': { cls: 'c-img', label: 'IMG' },
                    'script': { cls: 'c-js', label: 'JS' },
                    'stylesheet': { cls: 'c-css', label: 'CSS' },
                    'font': { cls: 'c-font', label: 'FONT' },
                    'document': { cls: 'c-html', label: 'HTML' },
                    'media': { cls: 'c-media', label: 'MEDIA' },
                    'other': { cls: 'c-other', label: 'OTHER' },
                    'third-party': { cls: 'c-other', label: '3RD-PARTY' }
                };
                
                let sizeData = [];
                let reqData = [];
                
                items.forEach(item => {
                    if(item.resourceType === 'total') {
                        totalSize = item.transferSize || 0;
                        totalReq = item.requestCount || 0;
                    } else {
                        const typeInfo = typeMap[item.resourceType] || { cls: 'c-other', label: item.resourceType.toUpperCase() };
                        sizeData.push({ ...typeInfo, val: item.transferSize || 0 });
                        reqData.push({ ...typeInfo, val: item.requestCount || 0 });
                    }
                });
                
                const mbTotal = (totalSize / 1024 / 1024).toFixed(1);
                document.getElementById('val-total-size').textContent = `${mbTotal}MB`;
                document.getElementById('val-total-req').textContent = totalReq;
                
                sizeData.sort((a, b) => b.val - a.val);
                reqData.sort((a, b) => b.val - a.val);
                
                const sizeBar = document.getElementById('bar-page-size');
                if(sizeBar) {
                    sizeBar.innerHTML = '';
                    sizeData.forEach(d => {
                        if(d.val > 0) {
                            const pct = (d.val / totalSize) * 100;
                            let text = '';
                            if(pct > 5) {
                                const mb = (d.val / 1024 / 1024).toFixed(1);
                                text = `<div>${d.label}</div><div>${mb}MB</div>`;
                            }
                            sizeBar.innerHTML += `<div class="bar-seg ${d.cls}" style="width: ${pct}%" title="${d.label}: ${(d.val/1024/1024).toFixed(1)}MB">${text}</div>`;
                        }
                    });
                }
                
                const reqBar = document.getElementById('bar-page-req');
                if(reqBar) {
                    reqBar.innerHTML = '';
                    reqData.forEach(d => {
                        if(d.val > 0) {
                            const pct = (d.val / totalReq) * 100;
                            let text = '';
                            if(pct > 5) {
                                text = `<div>${d.label}</div><div>${pct.toFixed(1)}%</div>`;
                            }
                            reqBar.innerHTML += `<div class="bar-seg ${d.cls}" style="width: ${pct}%" title="${d.label}: ${pct.toFixed(1)}%">${text}</div>`;
                        }
                    });
                }
            }

            // 6. PERFORMANCE METRICS (6 CARDS)
            const populatePerfCard = (id, auditKey, unit, divider) => {
                const audit = audits[auditKey];
                const badgeEl = document.getElementById(`pm-badge-${id}`);
                const valEl = document.getElementById(`pm-val-${id}`);
                
                if(!audit || audit.numericValue === undefined) {
                    if(badgeEl) badgeEl.style.background = '#ccc';
                    return;
                }
                
                let val = audit.numericValue;
                if(divider > 1) {
                    val = (val / divider).toFixed(1);
                } else {
                    val = Math.round(val);
                }
                
                // Fix CLS decimal format
                if(id === 'cls') val = Number(audit.numericValue).toFixed(2);
                
                if(valEl) valEl.textContent = val + (unit ? unit : '');
                
                const score = audit.score; // 0.0 to 1.0
                let statusColor = '#e06d6b'; // red
                let statusText = 'Longer than recommended';
                
                if(score >= 0.9) {
                    statusColor = '#98c65f'; // green
                    statusText = 'Good - Nothing to do here';
                } else if (score >= 0.5) {
                    statusColor = '#f3b34c'; // orange
                    statusText = 'OK, but consider improvement';
                }
                
                if(badgeEl) {
                    badgeEl.style.background = statusColor;
                    badgeEl.textContent = statusText;
                }
                if(valEl) {
                    valEl.style.color = statusColor;
                }
            };
            
            populatePerfCard('fcp', 'first-contentful-paint', 's', 1000);
            populatePerfCard('tti', 'interactive', 's', 1000);
            populatePerfCard('si', 'speed-index', 's', 1000);
            populatePerfCard('tbt', 'total-blocking-time', 'ms', 1);
            populatePerfCard('lcp', 'largest-contentful-paint', 's', 1000);
            populatePerfCard('cls', 'cumulative-layout-shift', '', 1);
            
            // Toggle Details
            const pmToggle = document.getElementById('pm-details-toggle');
            if(pmToggle) {
                pmToggle.addEventListener('change', (e) => {
                    const descs = document.querySelectorAll('.pm-card-desc');
                    descs.forEach(desc => {
                        if(e.target.checked) {
                            desc.classList.remove('hidden');
                        } else {
                            desc.classList.add('hidden');
                        }
                    });
                });
            }

            // B. Top Issues
            const issuesList = document.getElementById('issues-list');
            let allIssues = [];
            
            for(const [key, audit] of Object.entries(audits)) {
                if(audit.score !== null && audit.score < 1 && audit.details && audit.details.type === 'opportunity') {
                    let severity = 'low';
                    let sevClass = 'sev-low';
                    let sevText = 'Med-Low';
                    
                    if(audit.score < 0.5) { severity = 'high'; sevClass = 'sev-high'; sevText = 'High'; }
                    else if(audit.score < 0.8) { severity = 'med'; sevClass = 'sev-med'; sevText = 'Med'; }
                    
                    let tags = [];
                    if(key.includes('lcp') || key.includes('render-blocking') || key.includes('server-response') || key.includes('payload')) tags.push('LCP');
                    if(key.includes('fcp') || key.includes('render-blocking') || key.includes('server-response')) tags.push('FCP');
                    if(key.includes('tbt') || key.includes('mainthread') || key.includes('bootup') || key.includes('javascript')) tags.push('TBT');
                    if(key.includes('cls') || key.includes('image-aspect-ratio') || key.includes('unsized-images')) tags.push('CLS');
                    
                    if(tags.length === 0) tags.push('PERF');
                    tags = [...new Set(tags)];
                    
                    allIssues.push({
                        id: key,
                        title: audit.title,
                        description: audit.description,
                        severityScore: audit.score,
                        sevClass: sevClass,
                        sevText: sevText,
                        tags: tags
                    });
                }
            }
            
            allIssues.sort((a, b) => a.severityScore - b.severityScore);
            
            function renderIssues(filter) {
                if(!issuesList) return;
                issuesList.innerHTML = '';
                
                const filtered = allIssues.filter(issue => {
                    if(filter === 'all') return true;
                    return issue.tags.includes(filter.toUpperCase());
                });
                
                if(filtered.length === 0) {
                    issuesList.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted); background: var(--card-bg); border-radius:4px; border:1px solid var(--border-color);">Bagus! Tidak ada isu performa signifikan pada kategori ini.</div>';
                    return;
                }
                
                filtered.forEach(issue => {
                    const tagsHtml = issue.tags.map(t => `<span class="issue-tag">${t}</span>`).join('');
                    const descHtml = issue.description.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" style="color:#22679c;">$1</a>');
                    
                    const item = document.createElement('div');
                    item.className = 'issue-item';
                    item.innerHTML = `
                        <div class="issue-summary" onclick="this.parentElement.classList.toggle('open')">
                            <div class="issue-severity ${issue.sevClass}">${issue.sevText}</div>
                            <div class="issue-title-wrap">
                                <h4 class="issue-title">${issue.title}</h4>
                                <div class="issue-tags">${tagsHtml}</div>
                            </div>
                            <div class="issue-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                        <div class="issue-details">
                            ${descHtml}
                        </div>
                    `;
                    issuesList.appendChild(item);
                });
            }
            
            renderIssues('all');
            
            const filterBtns = document.querySelectorAll('.filter-btn');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    renderIssues(e.target.dataset.filter);
                });
            });

            // 7. BROWSER TIMINGS
            const btMetrics = audits['metrics'] && audits['metrics'].details && audits['metrics'].details.items ? audits['metrics'].details.items[0] : null;
            const srtAudit = audits['server-response-time'];
            
            if(btMetrics) {
                const setBtVal = (id, valMs) => {
                    const el = document.getElementById(`bt-val-${id}`);
                    if(!el || valMs === undefined || valMs === null) return;
                    if(valMs >= 1000) {
                        el.textContent = (valMs / 1000).toFixed(1) + 's';
                    } else {
                        el.textContent = Math.round(valMs) + 'ms';
                    }
                };
                
                let ttfb = srtAudit ? srtAudit.numericValue : null;
                setBtVal('ttfb', ttfb);
                setBtVal('fp', btMetrics.observedFirstPaint);
                
                let dcl = btMetrics.observedDomContentLoaded;
                setBtVal('dom-int', dcl ? dcl - 15 : null); 
                setBtVal('dom-content', dcl);
                
                setBtVal('onload', btMetrics.observedLoad);
                setBtVal('fully', btMetrics.observedTraceEnd);
            }
        }
    </script>
</body>
</html>