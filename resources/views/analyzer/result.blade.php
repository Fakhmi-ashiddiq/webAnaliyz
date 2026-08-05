<!DOCTYPE html>
<html lang="id" data-theme="dark">
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
            padding: 15px 20px;
            margin-bottom: 30px;
            background: var(--bg-body);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .url-title { display: flex; align-items: center; gap: 15px; font-size: 1.2rem; font-weight: 600; }
        .url-link { color: var(--primary); text-decoration: none; }
        .url-link:hover { text-decoration: underline; }
        .badge-strategy { background: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid rgba(59, 130, 246, 0.3); font-weight: 600; }
        
        .header-actions { display: flex; align-items: center; gap: 15px; }
        .btn-back { padding: 8px 16px; background: var(--bg-card); color: var(--text-main); border-radius: 8px; font-weight: 500; font-size: 0.9rem; text-decoration: none; border: 1px solid var(--border-color); }
        .btn-back:hover { background: var(--border-color); }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 50px;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Tab Navigation */
        .tab-nav { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; }
        .tab-btn { background: transparent; border: none; padding: 10px 20px; font-size: 15px; font-weight: 500; color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent; transition: 0.2s; outline: none; }
        .tab-btn:hover { color: var(--text-main); }
        .tab-btn.active { color: #22679c; border-bottom-color: #22679c; }
        [data-theme="dark"] .tab-btn.active { color: #5dade2; border-bottom-color: #5dade2; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* VirusTotal Dashboard */
        .vt-dashboard { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; font-family: 'Inter', sans-serif; }
        .vt-score-panel { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; padding: 25px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 160px; color: var(--text-main); }
        .vt-circle-wrapper { width: 100px; height: 100px; margin-bottom: 15px; position: relative; }
        .vt-circular-chart { display: block; margin: 0 auto; max-width: 100%; max-height: 250px; }
        .vt-circle-bg { fill: none; stroke: var(--border-color); stroke-width: 3.5; }
        .vt-circle { fill: none; stroke-width: 3.5; stroke-linecap: round; stroke: #45b78b; transition: stroke-dasharray 1s ease-out; }
        .vt-score-main { fill: #45b78b; font-size: 12px; font-weight: 500; text-anchor: middle; font-family: 'Outfit', sans-serif; }
        .vt-score-sub { fill: var(--text-muted); font-size: 6px; text-anchor: middle; }
        .vt-community-box { display: flex; align-items: center; justify-content: space-between; width: 100%; padding-top: 15px; border-top: 1px solid var(--border-color); }
        .vt-comm-label { font-size: 11px; color: var(--text-muted); line-height: 1.2; }
        .vt-comm-score-badge { background: var(--bg-body); color: #45b78b; font-size: 14px; font-weight: 500; padding: 4px 10px; border-radius: 20px; display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); }
        .vt-comm-arrows { display: flex; flex-direction: column; align-items: center; gap: 2px; }
        
        .vt-info-panel { flex: 1; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; display: flex; flex-direction: column; min-width: 300px; overflow: hidden; }
        .vt-info-header { background: var(--bg-body); padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid var(--border-color); }
        .vt-url-report-title { color: var(--text-main); font-size: 13px; }
        .vt-actions { display: flex; gap: 15px; }
        .vt-btn-action { background: transparent; border: none; color: var(--text-muted); font-size: 13px; display: flex; align-items: center; gap: 5px; cursor: pointer; transition: 0.2s; }
        .vt-btn-action:hover { color: var(--text-main); }
        
        .vt-info-body { padding: 25px; display: flex; flex-direction: column; justify-content: center; flex: 1; gap: 25px; }
        .vt-main-url { color: var(--text-main); font-size: 18px; font-weight: 500; margin-bottom: 5px; word-break: break-all; }
        .vt-domain-ip { color: var(--text-main); font-size: 14px; margin-bottom: 12px; opacity: 0.9; }
        .vt-ip-text { color: var(--text-muted); margin-left: 8px; }
        .vt-tags { display: flex; flex-wrap: wrap; gap: 8px; }
        .vt-tag { background: rgba(69, 183, 139, 0.1); color: #45b78b; font-size: 11px; padding: 3px 10px; border-radius: 20px; border: 1px solid rgba(69, 183, 139, 0.2); }
        
        .vt-stats-grid { display: flex; justify-content: flex-end; align-items: center; gap: 30px; flex-wrap: wrap; margin-top: auto; }
        .vt-stat-item { display: flex; flex-direction: column; }
        .vt-stat-label { color: var(--text-muted); font-size: 11px; margin-bottom: 4px; }
        .vt-stat-val { color: var(--text-main); font-size: 13px; font-weight: 500; }
        .vt-globe-icon { color: var(--text-muted); margin-left: 10px; }

        /* VirusTotal Inner Tabs & Summary */
        .vt-inner-tabs { display: flex; gap: 20px; border-bottom: 1px solid var(--border-color); margin-bottom: 25px; padding-bottom: 0; }
        .vt-inner-tab { background: transparent; border: none; padding: 10px 0 15px 0; font-size: 14px; font-weight: 600; color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent; transition: 0.2s; text-transform: uppercase; letter-spacing: 0.5px; }
        .vt-inner-tab:hover { color: var(--text-main); }
        .vt-inner-tab.active { color: #5dade2; border-bottom-color: #5dade2; }
        .vt-tab-pane { display: none; }
        .vt-tab-pane.active { display: block; }
        
        .vt-page-stats-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; margin-bottom: 30px; }
        .vt-ps-header { background: rgba(0,0,0,0.02); border-bottom: 1px solid var(--border-color); padding: 15px 20px; font-size: 14px; font-weight: 600; color: var(--text-main); }
        [data-theme="dark"] .vt-ps-header { background: rgba(255,255,255,0.02); }
        .vt-ps-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; padding: 20px; }
        .vt-ps-item { display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border-color); border-radius: 4px; padding: 12px 15px; background: var(--card-bg); }
        .vt-ps-left { display: flex; align-items: center; gap: 10px; }
        .vt-ps-icon { width: 16px; height: 16px; color: var(--text-muted); }
        .vt-icon-text { font-size: 10px; font-weight: bold; border: 1px solid var(--text-muted); border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; }
        .vt-ps-label { font-size: 13px; color: var(--text-main); font-weight: 500; }
        .vt-ps-info { width: 14px; height: 14px; border: 1px solid var(--border-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; color: var(--text-muted); cursor: help; }
        .vt-ps-val { font-size: 13px; font-weight: 600; color: #5dade2; background: rgba(93, 173, 226, 0.1); padding: 2px 8px; border-radius: 20px; }

        .vt-url-overview { padding: 20px 25px 25px 25px; display: flex; flex-direction: column; gap: 15px; font-size: 13px; color: var(--text-main); }
        .vt-uo-row { display: flex; align-items: flex-start; }
        .vt-uo-label { font-weight: 600; width: 180px; flex-shrink: 0; color: var(--text-main); }
        .vt-uo-val { flex: 1; word-break: break-all; }
        .vt-uo-tag { background: rgba(0,0,0,0.05); color: var(--text-main); padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; margin-right: 5px; margin-bottom: 5px; border: 1px solid var(--border-color); }
        [data-theme="dark"] .vt-uo-tag { background: #35425b; color: white; border-color: #435372; }

        .vt-history-box { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; margin-bottom: 30px; }
        .vt-history-row { display: flex; padding: 12px 20px; border-bottom: 1px solid var(--border-color); font-size: 13px; color: var(--text-main); }
        .vt-history-row:last-child { border-bottom: none; }
        .vt-history-label { width: 250px; flex-shrink: 0; font-weight: 500; }
        .vt-history-val { flex: 1; display: flex; align-items: center; gap: 8px; }
        .vt-history-unknown { color: var(--text-muted); font-size: 11px; text-transform: uppercase; display: flex; align-items: center; }

        .vt-tech-table { width: 100%; border-collapse: collapse; font-size: 13px; color: var(--text-main); }
        .vt-tech-table th { text-align: left; padding: 15px 20px; border-bottom: 1px solid var(--border-color); font-weight: 600; color: var(--text-main); }
        .vt-tech-table td { padding: 12px 20px; border-bottom: 1px solid var(--border-color); }
        .vt-tech-table tr:last-child td { border-bottom: none; }
        .tech-icon { width: 16px; height: 16px; vertical-align: middle; margin-right: 8px; flex-shrink: 0; }
        .tech-name-cell { display: flex; align-items: center; }

        /* Crowdsourced Context */
        .vt-cs-box { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; margin-bottom: 30px; }
        .vt-cs-header { display: flex; gap: 20px; padding: 12px 25px; border-bottom: 1px solid var(--border-color); background: rgba(0,0,0,0.02); }
        [data-theme="dark"] .vt-cs-header { background: rgba(255,255,255,0.02); }
        .vt-cs-sev { font-size: 11px; font-weight: 600; color: var(--text-muted); cursor: pointer; transition: 0.2s; }
        .vt-cs-sev.active { color: #f1c40f; border-bottom: 2px solid #f1c40f; padding-bottom: 12px; margin-bottom: -13px; }
        .vt-cs-sev span { font-weight: normal; margin-left: 2px; }
        
        .vt-cs-items { padding: 25px; }
        .vt-cs-item { display: flex; gap: 15px; align-items: flex-start; margin-bottom: 15px; padding: 15px; border-radius: 4px; border: 1px solid transparent; }
        .vt-cs-item:last-child { margin-bottom: 0; }
        .vt-cs-icon { flex-shrink: 0; width: 18px; height: 18px; margin-top: 2px; }
        .vt-cs-content { font-size: 13px; color: var(--text-main); line-height: 1.5; }
        .vt-cs-title { font-weight: 600; margin-bottom: 8px; }
        .vt-cs-title span { font-weight: 400; color: var(--text-muted); }
        .vt-cs-detail { font-size: 12px; }
        .vt-cs-tree-line { padding-left: 15px; border-left: 1px solid var(--border-color); margin-top: 5px; position: relative; }
        .vt-cs-tree-line::before { content: '└'; position: absolute; left: 0; color: var(--text-muted); margin-left: 2px; }

        /* Security Vendors Grid */
        .vt-vendors-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; margin-bottom: 30px; }
        .vt-vendors-grid { display: grid; grid-template-columns: repeat(2, 1fr); background: var(--border-color); gap: 1px; }
        @media (max-width: 768px) {
            .vt-vendors-grid { grid-template-columns: 1fr; }
        }
        .vt-vendor-item { background: var(--card-bg); display: flex; align-items: center; justify-content: space-between; padding: 12px 25px; font-size: 13px; color: var(--text-main); }
        .vt-vendor-name { font-weight: 500; }
        .vt-vendor-result { display: flex; align-items: center; gap: 8px; font-weight: 500; }
        
        .vt-icon-clean { color: #45b78b; }
        .vt-icon-malicious { color: #e74c3c; }
        .vt-icon-unrated { color: var(--text-muted); }

        /* Details Tab Layout */
        .vt-details-layout { display: flex; align-items: flex-start; gap: 30px; margin-bottom: 30px; }
        .vt-toc { width: 250px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; flex-shrink: 0; position: sticky; top: 90px; }
        .vt-toc-title { font-size: 13px; font-weight: 600; padding: 15px 20px; border-bottom: 1px solid var(--border-color); color: var(--text-main); }
        .vt-toc-list { list-style: none; padding: 0; margin: 0; }
        .vt-toc-item { padding: 12px 20px; border-bottom: 1px solid var(--border-color); font-size: 13px; color: var(--text-main); cursor: pointer; transition: 0.2s; }
        .vt-toc-item:hover { background: rgba(0,0,0,0.02); color: #5dade2; }
        [data-theme="dark"] .vt-toc-item:hover { background: rgba(255,255,255,0.02); }
        .vt-toc-item:last-child { border-bottom: none; }
        @media (max-width: 900px) {
            .vt-details-layout { flex-direction: column; }
            .vt-toc { width: 100%; position: relative; top: 0; }
        }
        
        .vt-details-content { flex: 1; min-width: 0; }
        .vt-detail-box { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; margin-bottom: 30px; }
        .vt-detail-header { background: rgba(0,0,0,0.02); padding: 12px 20px; font-size: 13px; font-weight: 600; color: var(--text-main); border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px; }
        [data-theme="dark"] .vt-detail-header { background: rgba(255,255,255,0.02); }
        
        .vt-detail-row { display: flex; padding: 12px 20px; border-bottom: 1px solid var(--border-color); font-size: 13px; color: var(--text-main); }
        .vt-detail-row:last-child { border-bottom: none; }
        .vt-detail-label { width: 200px; flex-shrink: 0; font-weight: 500; }
        .vt-detail-val { flex: 1; word-break: break-word; }
        
        .vt-detail-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
        .vt-detail-table th { padding: 12px 20px; font-weight: 600; border-bottom: 1px solid var(--border-color); background: rgba(0,0,0,0.01); }
        .vt-detail-table td { padding: 12px 20px; border-bottom: 1px solid var(--border-color); }
        [data-theme="dark"] .vt-detail-table th { background: rgba(255,255,255,0.01); }

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
        .crux-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
        


        /* Export Dropdown */
        .export-dropdown { position: relative; display: inline-block; }
        .btn-export { background: #22679c; border: none; color: white; padding: 8px 16px; border-radius: 4px; font-size: 14px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-export:hover { background: #1a527c; }
        .export-menu { display: none; position: absolute; right: 0; top: 100%; margin-top: 5px; background: var(--card-bg); min-width: 180px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 4px; border: 1px solid var(--border-color); z-index: 100; overflow: hidden; }
        .export-dropdown:hover .export-menu { display: block; }
        .export-item { display: block; padding: 10px 16px; color: var(--text-main); text-decoration: none; font-size: 13px; transition: background 0.2s; }
        .export-item:hover { background: rgba(34, 103, 156, 0.1); color: #22679c; }
        [data-theme="dark"] .export-item:hover { background: rgba(93, 173, 226, 0.1); color: #5dade2; }

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

        /* Structure Section */
        .sa-container { margin-top: 50px; }
        .sa-header-area { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px; flex-wrap: wrap; gap: 15px; }
        .sa-filters { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .sa-filter-label { font-size: 13px; color: var(--text-muted); margin-right: 5px; }
        .sa-filter-btn { background: #f0f0f0; border: 1px solid #ddd; color: var(--text-muted); padding: 4px 12px; border-radius: 4px; font-size: 13px; cursor: pointer; transition: 0.2s; }
        [data-theme="dark"] .sa-filter-btn { background: #2a2a2a; border-color: #444; }
        .sa-filter-btn.active { background: #22679c; color: white; border-color: #22679c; }
        
        .sa-table-header { display: flex; padding: 10px 15px; font-size: 11px; font-weight: bold; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border-color); }
        .sa-col-impact { width: 100px; text-align: center; }
        .sa-col-audit { flex: 1; padding-left: 20px; }
        
        .sa-row { display: flex; flex-wrap: wrap; background: var(--card-bg); border-bottom: 1px solid var(--border-color); align-items: center; cursor: pointer; }
        .sa-row:hover { background: #fafafa; }
        [data-theme="dark"] .sa-row:hover { background: #1f1f1f; }
        
        .sa-impact-box { width: 100px; padding: 15px 10px; color: white; font-weight: 500; text-align: center; font-size: 14px; flex-shrink: 0; align-self: stretch; display: flex; align-items: center; justify-content: center; }
        .sa-impact-high { background: #e06d6b; }
        .sa-impact-med { background: #f3b34c; }
        .sa-impact-medlow { background: #98c65f; }
        .sa-impact-low { background: #50b848; }
        .sa-impact-none { background: #2c8c36; }
        .sa-impact-na { background: #b0b0b0; }
        
        .sa-audit-main { flex: 1; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; min-width: 0; gap: 15px; }
        .sa-audit-left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; flex: 1; min-width: 0; }
        .sa-audit-title { font-size: 14px; font-weight: 500; color: #22679c; margin: 0; word-break: break-word; }
        [data-theme="dark"] .sa-audit-title { color: #5dade2; }
        
        .sa-tag { font-size: 10px; padding: 2px 6px; background: #e8e8e8; color: #666; border-radius: 3px; border: 1px solid #ddd; }
        [data-theme="dark"] .sa-tag { background: #333; color: #ccc; border-color: #555; }
        
        .sa-audit-right { display: flex; align-items: center; gap: 15px; flex-shrink: 0; }
        .sa-display-val { font-size: 13px; color: var(--text-muted); }
        .sa-chevron { color: var(--text-muted); transition: transform 0.3s; }
        .sa-row.open .sa-chevron { transform: rotate(180deg); }
        
        .sa-details { width: 100%; padding: 20px 20px 20px 120px; background: #f9f9f9; border-top: 1px dashed #eee; font-size: 13px; color: var(--text-main); display: none; line-height: 1.5; }
        [data-theme="dark"] .sa-details { background: #161616; border-top-color: #333; }
        .sa-row.open .sa-details { display: block; }
        
        .sa-toggle-no-impact { background: #e6e9ec; color: #555; text-align: center; padding: 12px; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; border-radius: 4px; margin-top: 10px; transition: 0.2s; }
        [data-theme="dark"] .sa-toggle-no-impact { background: #2a2a2a; color: #ccc; }
        .sa-toggle-no-impact:hover { background: #dce0e3; }
        [data-theme="dark"] .sa-toggle-no-impact:hover { background: #333; }

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

        <div id="error-box">Gagal memuat atau memproses data API. Silakan coba lagi.</div>

        <div id="app-content">
            <div class="tab-nav">
                <button class="tab-btn active" data-target="tab-performance">Performance</button>
                <button class="tab-btn" data-target="tab-security">Security Engine</button>
            </div>

            <!-- Main Dashboard -->
            <div class="dashboard-container">
                
                <!-- Tab: Performance -->
                <div id="tab-performance" class="tab-content active">
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

                        <!-- NEW: Structure Section -->
                        @include('analyzer.partials.structure-audits')
                    </div>
                </div>

                <!-- Tab: Security -->
                <div id="tab-security" class="tab-content">
                    <div class="dashboard-card" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                        <!-- VirusTotal Header -->
                        @include('analyzer.partials.vt-header')

                        <!-- VirusTotal Inner Content (Summary, Detection, Details) -->
                        @include('analyzer.partials.vt-summary')
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Web Performance Analyzer. All rights reserved.
        </div>
    </div>

    <script>
        const rawJson = @json($report->raw_api_data);
        let data = null;

        // Fallback icon bila CDN Simple Icons gagal / slug tidak dikenal
        function techFallback(el) {
            el.outerHTML = '<svg class="tech-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>';
        }

        // Map nama teknologi -> slug icon lokal (agar laporan lama tanpa field icon tetap dapat gambar)
        const TECH_ICONS = {
            'Apache': 'apache', 'Nginx': 'nginx', 'LiteSpeed': 'litespeed', 'IIS': 'microsoft-iis',
            'Cloudflare': 'cloudflare', 'Amazon CloudFront': 'amazonwebservices', 'PHP': 'php',
            'Express': 'express', 'Node.js': 'nodedotjs', 'Next.js': 'nextdotjs', 'ASP.NET': 'dotnet',
            'CodeIgniter': 'codeigniter', 'Laravel': 'laravel', 'CakePHP': 'cakephp',
            'WordPress': 'wordpress', 'Elementor': 'elementor', 'WooCommerce': 'woocommerce',
            'Yoast SEO': 'yoast', 'Joomla': 'joomla', 'Drupal': 'drupal', 'Shopify': 'shopify',
            'Magento': 'magento', 'PrestaShop': 'prestashop', 'Wix': 'wix',
            'jQuery': 'jquery', 'Bootstrap': 'bootstrap', 'React': 'react', 'Vue.js': 'vuedotjs',
            'Angular': 'angular', 'Alpine.js': 'alpinejs', 'htmx': 'htmx', 'GSAP': 'gsap',
            'Chart.js': 'chartdotjs', 'Select2': 'select2', 'SweetAlert2': 'sweetalert2',
            'TinyMCE': 'tinymce', 'CKEditor': 'ckeditor', 'Axios': 'axios', 'Moment.js': 'moment',
            'Lodash': 'lodash', 'Slick Carousel': 'slick', 'Owl Carousel': 'owlcarousel',
            'core-js': 'corejs', 'core-js-pure': 'corejs', 'Isotope': 'isotope',
            'Font Awesome': 'fontawesome', 'Bootstrap Icons': 'bootstrap', 'Material Icons': 'materialdesign',
            'Google Fonts': 'googlefonts', 'Tailwind CSS': 'tailwindcss',
            'Google Analytics': 'googleanalytics', 'Google Tag Manager': 'googletagmanager',
            'Facebook Pixel': 'facebook', 'Hotjar': 'hotjar', 'Matomo': 'matomo',
            'Yandex Metrika': 'yandex', 'jsDelivr': 'jsdelivr', 'cdnjs': 'cloudflare',
            'unpkg': 'unpkg', 'Firebase': 'firebase', 'Vercel': 'vercel', 'Netlify': 'netlify'
        };

        // Bersihkan nama teknologi dari sisipan versi, mis. "PHP/7.4.33" -> "PHP", "core-js-pure@3.32.2" -> "core-js-pure"
        function cleanTechName(name) {
            let n = String(name || '').trim();
            n = n.replace(/([\/@])\d+(\.\d+)*.*$/i, '');
            return n;
        }

        // Cari slug icon: cocokkan persis, lalu substring terpanjang (case-insensitive)
        function lookupTechIcon(name) {
            const clean = cleanTechName(name);
            if(!clean) return '';
            const lower = clean.toLowerCase();
            if(TECH_ICONS[clean]) return TECH_ICONS[clean];
            let best = '';
            let bestLen = 0;
            for(const [k, slug] of Object.entries(TECH_ICONS)) {
                const kl = k.toLowerCase();
                if(lower.includes(kl) && kl.length > bestLen) { best = slug; bestLen = kl.length; }
                else if(kl.includes(lower) && lower.length > bestLen && clean.length > 2) { best = slug; bestLen = lower.length; }
            }
            return best;
        }

        document.addEventListener('DOMContentLoaded', () => {
            try {
                data = JSON.parse(rawJson);
                if(data && data.pagespeed && data.pagespeed.lighthouseResult) {
                    document.getElementById('app-content').style.display = 'block';
                    initDashboard(data);
                } else {
                    let errMsg = "Data dari Google Lighthouse (PageSpeed API) kosong atau gagal diakses. Situs target mungkin menolak pemindaian atau terlalu lambat.";
                    const errBox = document.getElementById('error-box');
                    errBox.innerHTML = `<strong>Peringatan!</strong> ${errMsg}<br><small style="color:var(--text-muted);margin-top:10px;display:block;">Sistem tidak dapat memproses laporan karena minimnya data vital.</small>`;
                    errBox.style.display = 'block';
                }
            } catch (e) {
                console.error("Data parse error:", e);
                const errBox = document.getElementById('error-box');
                errBox.innerHTML = `<strong>Error!</strong> Terjadi kesalahan sistem saat memuat data JavaScript: <br><small style="color:var(--red);">${e.message}</small>`;
                errBox.style.display = 'block';
            }
        });

        function getScoreStatus(scoreVal) {
            if (scoreVal >= 90) return 'green';
            if (scoreVal >= 50) return 'orange';
            return 'red';
        }

        function submitAnalysis() {
            const urlInput = document.getElementById('url-input').value;
            if(!urlInput) {
                alert('Please enter a URL');
                return;
            }
            
            // ... (rest remains same)
        }

        window.gotoDetails = function(sectionId) {
            document.querySelectorAll('.vt-inner-tab').forEach(btn => btn.classList.remove('active'));
            const detailTabBtn = document.querySelector('.vt-inner-tab[data-target="vt-tab-details"]');
            if(detailTabBtn) detailTabBtn.classList.add('active');
            
            document.querySelectorAll('.vt-tab-pane').forEach(p => p.classList.remove('active'));
            const detailPane = document.getElementById('vt-tab-details');
            if(detailPane) detailPane.classList.add('active');
            
            setTimeout(() => {
                const sec = document.getElementById('sec-' + sectionId);
                if(sec) {
                    const offset = 80;
                    const top = sec.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({top: top, behavior: 'smooth'});
                }
            }, 100);
        };

        function initDashboard(data) {
            // Tab switching logic
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    btn.classList.add('active');
                    document.getElementById(btn.dataset.target).classList.add('active');
                });
            });

            // VirusTotal Logic
            if(data.virustotal && data.virustotal.data) {
                const attr = data.virustotal.data.attributes;
                const httpInfo = data.http_info || {};
                const stats = attr.last_analysis_stats || {};
                
                const malicious = stats.malicious || 0;
                const totalEngines = (stats.malicious || 0) + (stats.suspicious || 0) + (stats.undetected || 0) + (stats.harmless || 0) + (stats.timeout || 0);
                
                document.getElementById('vt-malicious-count').textContent = malicious;
                document.getElementById('vt-engine-count').textContent = '/' + totalEngines;
                
                const scoreCircle = document.getElementById('vt-score-circle');
                const vtScoreText = document.getElementById('vt-malicious-count');
                
                if (malicious > 0) {
                    scoreCircle.style.stroke = '#e06d6b';
                    vtScoreText.style.fill = '#e06d6b';
                    const pct = (malicious / totalEngines) * 100;
                    setTimeout(() => { scoreCircle.style.strokeDasharray = `${pct}, 100`; }, 300);
                } else {
                    scoreCircle.style.stroke = '#45b78b';
                    vtScoreText.style.fill = '#45b78b';
                    // Full circle
                    setTimeout(() => { scoreCircle.style.strokeDasharray = `100, 100`; }, 300);
                }
                
                const votes = attr.total_votes || { harmless: 0, malicious: 0 };
                const commScore = votes.harmless - votes.malicious;
                document.getElementById('vt-comm-score').textContent = commScore;
                if(commScore < 0) {
                    document.getElementById('vt-comm-badge').style.color = '#e06d6b';
                }
                
                if(attr.last_analysis_date) {
                    const d = new Date(attr.last_analysis_date * 1000);
                    const dateOptions = { timeZone: 'Asia/Jakarta', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                    let dateString = 'N/A';
                    try {
                        dateString = new Intl.DateTimeFormat('id-ID', dateOptions).format(d).replace(/\./g, ':') + ' WIB.';
                    } catch (e) {
                        dateString = d.toLocaleString() + ' (Lokal).';
                    }
                    document.getElementById('vt-analysis-date').textContent = dateString;
                    
                    const diffTime = Math.abs(new Date() - d);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    document.getElementById('vt-last-analysis').textContent = diffDays + (diffDays > 1 ? ' days ago' : ' day ago');
                }
                
                document.getElementById('vt-main-url').textContent = attr.url || 'Unknown URL';
                
                // VT API doesn't always provide the exact domain/IP in the root url endpoint nicely.
                // We will try to extract domain from URL
                try {
                    const urlObj = new URL(attr.url);
                    document.getElementById('vt-domain').textContent = urlObj.hostname;
                } catch(e) {
                    document.getElementById('vt-domain').textContent = '';
                }
                
                // IP Address in Header
                document.getElementById('vt-ip').textContent = '{{ $resolved_ip }}';
                document.getElementById('vt-status-code').textContent = attr.last_http_response_code || httpInfo.status_code || 'N/A';
                
                let headers = attr.last_http_response_headers || {};
                let cType = 'N/A';
                for (const [k, v] of Object.entries(headers)) {
                    if(k.toLowerCase() === 'content-type') { cType = v; break; }
                }
                if(cType === 'N/A' && httpInfo.content_type) { cType = httpInfo.content_type; }
                document.getElementById('vt-content-type').textContent = cType;
                
                const tagsContainer = document.getElementById('vt-tags');
                let tagHtml = '';
                if(cType.includes('html')) tagHtml += `<span class="vt-tag">text/html</span>`;
                if(attr.categories) {
                    const cats = Object.values(attr.categories);
                    cats.forEach(c => {
                        tagHtml += `<span class="vt-tag">${c.toLowerCase()}</span>`;
                    });
                }
                tagsContainer.innerHTML = tagHtml;
            }

            // VirusTotal Inner Tabs Logic
            const vtInnerBtns = document.querySelectorAll('.vt-inner-tab');
            const vtInnerPanes = document.querySelectorAll('.vt-tab-pane');
            vtInnerBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    vtInnerBtns.forEach(b => b.classList.remove('active'));
                    vtInnerPanes.forEach(p => p.classList.remove('active'));
                    btn.classList.add('active');
                    document.getElementById(btn.dataset.target).classList.add('active');
                });
            });

            // Calculate Page Stats using Engine Data
            if(data.pagespeed && data.pagespeed.lighthouseResult) {
                const lh = data.pagespeed.lighthouseResult;
                const netReqs = lh.audits['network-requests'];
                if(netReqs && netReqs.details && netReqs.details.items) {
                    const items = netReqs.details.items;
                    
                    let totalReqs = items.length;
                    let httpsReqs = 0;
                    let domains = new Set();
                    let totalSize = 0;

                    items.forEach(req => {
                        if(req.url.startsWith('https://')) httpsReqs++;
                        try {
                            let urlObj = new URL(req.url);
                            domains.add(urlObj.hostname);
                        } catch(e) {}
                        
                        totalSize += (req.transferSize || 0);
                    });

                    let httpsPercent = totalReqs > 0 ? Math.round((httpsReqs / totalReqs) * 100) : 0;
                    
                    // Format Size
                    let sizeStr = '0 B';
                    if(totalSize > 1024 * 1024) {
                        sizeStr = (totalSize / (1024 * 1024)).toFixed(2) + ' MB';
                    } else if(totalSize > 1024) {
                        sizeStr = (totalSize / 1024).toFixed(2) + ' KB';
                    } else {
                        sizeStr = totalSize + ' B';
                    }

                    // Populate UI
                    document.getElementById('vt-ps-requests').textContent = totalReqs;
                    document.getElementById('vt-ps-https').textContent = httpsPercent + '%';
                    document.getElementById('vt-ps-domains').textContent = domains.size;
                    document.getElementById('vt-ps-size').textContent = sizeStr;
                    
                    // Fixed Single IP Resolution
                    document.getElementById('vt-ps-ips').textContent = '{{ $resolved_ip }}' !== 'N/A' ? '1' : '0';
                    
                    // The rest defaults to N/A as they are not reliably provided by free generic APIs without additional latency
                }
            }

            // URL Overview Logic
            if(data.virustotal && data.virustotal.data) {
                const attr = data.virustotal.data.attributes;
                const httpInfo = data.http_info || {};
                const stats = attr.last_analysis_stats || {};
                const malicious = stats.malicious || 0;

                const uoVendors = document.getElementById('vt-uo-vendors');
                if (malicious > 0) {
                    uoVendors.innerHTML = `<svg class="vt-ps-icon" style="color: var(--red);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    <span style="color: var(--red);">${malicious} security vendor${malicious > 1 ? 's' : ''} flagged this URL as malicious</span>`;
                }
                
                document.getElementById('vt-uo-url').textContent = attr.url || 'Unknown URL';
                
                try {
                    document.getElementById('vt-uo-domain').textContent = new URL(attr.url).hostname;
                } catch(e) {}
                
                document.getElementById('vt-uo-status').textContent = attr.last_http_response_code || httpInfo.status_code || 'N/A';
                document.getElementById('vt-uo-ip').textContent = '{{ $resolved_ip }}';
                
                const catContainer = document.getElementById('vt-uo-category');
                const tContainer = document.getElementById('vt-uo-tags');
                let catHtml = '';
                let tHtml = '';
                
                if(attr.categories) {
                    Object.values(attr.categories).forEach(c => {
                        catHtml += `<span class="vt-uo-tag">${c.toLowerCase()}</span>`;
                    });
                }
                catContainer.innerHTML = catHtml || '-';
                
                if(attr.tags) {
                    attr.tags.forEach(t => {
                        tHtml += `<span class="vt-uo-tag">${t.toLowerCase()}</span>`;
                    });
                }
                if(!tHtml && httpInfo.content_type && String(httpInfo.content_type).toLowerCase().includes('html')) {
                    tHtml += `<span class="vt-uo-tag">text/html</span>`;
                }
                tContainer.innerHTML = tHtml || '-';

                // History Logic
                function formatVtDate(unix) {
                    const formatUtc = (ts) => {
                        if(!ts) return 'N/A';
                        const d = new Date(ts * 1000);
                        const dateOptions = { timeZone: 'Asia/Jakarta', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                        try {
                            return new Intl.DateTimeFormat('id-ID', dateOptions).format(d).replace(/\./g, ':') + ' WIB';
                        } catch (e) {
                            return d.toLocaleString() + ' (Lokal)';
                        }
                    };
                    return formatUtc(unix);
                }
                
                if(attr.first_submission_date) {
                    document.getElementById('vt-hist-first').textContent = formatVtDate(attr.first_submission_date);
                } else {
                    document.getElementById('vt-hist-first').textContent = '- ';
                    document.getElementById('vt-hist-first-unknown').style.display = 'inline-flex';
                }

                if(attr.last_submission_date) {
                    document.getElementById('vt-hist-last').textContent = formatVtDate(attr.last_submission_date);
                } else {
                    document.getElementById('vt-hist-last').textContent = '- ';
                    document.getElementById('vt-hist-last-unknown').style.display = 'inline-flex';
                }

                if(attr.last_analysis_date) {
                    document.getElementById('vt-hist-analysis').textContent = formatVtDate(attr.last_analysis_date);
                } else {
                    document.getElementById('vt-hist-analysis').textContent = '-';
                }

                // Web Technologies
                const techBody = document.getElementById('vt-tech-body');
                let techList = [];

                // From server-side detection (HTML + header scan)
                if(data.technologies && data.technologies.length) {
                    data.technologies.forEach(t => {
                        techList.push({ name: t.name, version: t.version || '-', category: t.category || '', icon: t.icon || '' });
                    });
                }
                
                // From Lighthouse JS Libraries
                if(data.pagespeed && data.pagespeed.lighthouseResult) {
                    const jsLibs = data.pagespeed.lighthouseResult.audits['js-libraries'];
                    if(jsLibs && jsLibs.details && jsLibs.details.items) {
                        jsLibs.details.items.forEach(item => {
                            techList.push({ name: item.name, version: item.version || '-' });
                        });
                    }
                }
                
                // From VT Headers
                if(attr.last_http_response_headers) {
                    const headers = attr.last_http_response_headers;
                    // Check Server
                    if(headers.server) techList.push({ name: headers.server, version: '-' });
                    // Check X-Powered-By (pisahkan nama & versi, mis. "PHP/7.4.33")
                    if(headers['x-powered-by']) {
                        const poweredBy = String(headers['x-powered-by']).split(';')[0].trim();
                        const pbm = poweredBy.match(/^(.+?)\/(\d[\w.]*)$/);
                        if(pbm) {
                            techList.push({ name: pbm[1], version: pbm[2] });
                        } else {
                            techList.push({ name: poweredBy, version: '-' });
                        }
                    }
                    // Check Cloudflare
                    if(headers.via && headers.via.toLowerCase().includes('cloudflare') || headers['cf-ray']) {
                        techList.push({ name: 'Cloudflare', version: '-' });
                    }
                }
                
                // Deduplicate by name
                const uniqueTechs = {};
                techList.forEach(t => {
                    const k = t.name.toLowerCase();
                    if(!uniqueTechs[k] || uniqueTechs[k].version === '-') {
                        uniqueTechs[k] = t;
                    }
                });
                
                const finalTechs = Object.values(uniqueTechs);
                const iconBase = '/icons';
                let techHtml = '';
                if(finalTechs.length > 0) {
                    finalTechs.forEach(t => {
                        const iconSlug = t.icon || lookupTechIcon(t.name);
                        const iconHtml = iconSlug
                            ? `<img class="tech-icon" src="${iconBase}/${iconSlug}.svg" alt="" width="16" height="16" loading="lazy" onerror="techFallback(this)">`
                            : `<svg class="tech-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>`;
                        techHtml += `<tr><td><span class="tech-name-cell">${iconHtml}<span>${t.name}</span></span></td><td>${t.version}</td></tr>`;
                    });
                } else {
                    techHtml = `<tr><td colspan="2" style="text-align:center; color:var(--text-muted);">No technologies detected</td></tr>`;
                }
                techBody.innerHTML = techHtml;

                // DETECTION TAB LOGIC
                
                // 1. Crowdsourced Context
                if(attr.crowdsourced_context && attr.crowdsourced_context.length > 0) {
                    const ctx = attr.crowdsourced_context;
                    document.getElementById('vt-crowdsourced-container').style.display = 'block';
                    
                    let counts = { high: 0, medium: 0, low: 0, info: 0, success: 0 };
                    let itemsHtml = '';
                    
                    ctx.forEach(c => {
                        let sev = (c.severity || 'info').toLowerCase();
                        if(counts[sev] !== undefined) counts[sev]++;
                        
                        let sevColor = '#5dade2';
                        let iconSvg = `<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>`;
                        if(sev === 'high') { sevColor = '#e74c3c'; iconSvg = `<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>`; }
                        else if(sev === 'medium') { sevColor = '#e67e22'; }
                        else if(sev === 'low') { sevColor = '#f1c40f'; iconSvg = `<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>`; }
                        else if(sev === 'success') { sevColor = '#45b78b'; iconSvg = `<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>`; }

                        itemsHtml += `
                        <div class="vt-cs-item" style="background: ${sevColor}0A; border-color: ${sevColor}33;">
                            <svg class="vt-cs-icon" style="color: ${sevColor};" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${iconSvg}</svg>
                            <div class="vt-cs-content">
                                <div class="vt-cs-title">${c.title || c.source} <span>- according to source ${c.source || 'Unknown'}</span></div>
                                <div class="vt-cs-tree-line">
                                    <div class="vt-cs-detail" style="margin-left: 8px;">${c.details || ''}</div>
                                </div>
                            </div>
                        </div>`;
                    });
                    
                    document.getElementById('vt-cs-high').innerHTML = `HIGH <span>${counts.high}</span>`;
                    document.getElementById('vt-cs-medium').innerHTML = `MEDIUM <span>${counts.medium}</span>`;
                    
                    const lowTab = document.getElementById('vt-cs-low');
                    lowTab.innerHTML = `LOW <span>${counts.low}</span>`;
                    if(counts.low > 0) lowTab.classList.add('active');
                    else if(counts.high > 0) document.getElementById('vt-cs-high').classList.add('active');
                    else document.getElementById('vt-cs-info').classList.add('active');

                    document.getElementById('vt-cs-info').innerHTML = `INFO <span>${counts.info}</span>`;
                    document.getElementById('vt-cs-success').innerHTML = `SUCCESS <span>${counts.success}</span>`;
                    
                    document.getElementById('vt-cs-items').innerHTML = itemsHtml;
                }

                // 2. Security Vendors Grid
                if(attr.last_analysis_results) {
                    const results = attr.last_analysis_results;
                    const vendorsGrid = document.getElementById('vt-vendors-grid');
                    let gridHtml = '';
                    
                    // Convert to array and sort alphabetically by engine_name
                    const vendorsArray = Object.values(results).sort((a, b) => a.engine_name.localeCompare(b.engine_name));
                    
                    vendorsArray.forEach(v => {
                        let cat = v.category || 'unrated'; // harmless, malicious, suspicious, undetected, unrated, timeout
                        let resultText = v.result || 'Unrated';
                        if(resultText === 'clean') resultText = 'Clean';
                        
                        let iconClass = 'vt-icon-unrated';
                        let iconSvg = `<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>`;
                        let textClass = 'vt-icon-unrated';
                        
                        if(cat === 'harmless' || cat === 'undetected') {
                            iconClass = 'vt-icon-clean';
                            textClass = ''; // Default white/black
                            iconSvg = `<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>`;
                        } else if(cat === 'malicious' || cat === 'suspicious') {
                            iconClass = 'vt-icon-malicious';
                            textClass = 'vt-icon-malicious';
                            iconSvg = `<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>`;
                        }
                        
                        gridHtml += `
                        <div class="vt-vendor-item">
                            <span class="vt-vendor-name">${v.engine_name}</span>
                            <span class="vt-vendor-result ${textClass}">
                                <svg class="${iconClass}" style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${iconSvg}</svg>
                                ${resultText}
                            </span>
                        </div>
                        `;
                    });
                    
                    vendorsGrid.innerHTML = gridHtml;
                }

                // 3. Details Tab
                const tocList = document.getElementById('vt-toc-list');
                const detContent = document.getElementById('vt-details-content');
                if (tocList && detContent) {
                    let tocHtml = '';
                    let detHtml = '';
                    
                    function addDetailSection(id, title, contentHtml) {
                        tocHtml += `<li class="vt-toc-item" onclick="gotoDetails('${id}')">${title}</li>`;
                        detHtml += `
                        <div class="vt-detail-box" id="sec-${id}">
                            <div class="vt-detail-header">${title}</div>
                            ${contentHtml}
                        </div>
                        `;
                    }

                    // A. Categories
                    if(attr.categories && Object.keys(attr.categories).length > 0) {
                        let catHtml = '';
                        for(const [vendor, cat] of Object.entries(attr.categories)) {
                            catHtml += `<div class="vt-detail-row"><div class="vt-detail-label">${vendor}</div><div class="vt-detail-val">${cat}</div></div>`;
                        }
                        addDetailSection('categories', 'Categories', catHtml);
                    }

                    // B. History
                    if(attr.first_submission_date || attr.last_submission_date || attr.last_analysis_date) {
                        let histHtml = `
                        <div class="vt-detail-row"><div class="vt-detail-label">First Submission</div><div class="vt-detail-val">${formatVtDate(attr.first_submission_date)}</div></div>
                        <div class="vt-detail-row"><div class="vt-detail-label">Last Submission</div><div class="vt-detail-val">${formatVtDate(attr.last_submission_date)}</div></div>
                        <div class="vt-detail-row"><div class="vt-detail-label">Last Analysis</div><div class="vt-detail-val">${formatVtDate(attr.last_analysis_date)}</div></div>
                        `;
                        addDetailSection('history', 'History', histHtml);
                    }

                    // C. HTTP Response
                    if(attr.last_http_response_code || attr.last_http_response_headers) {
                        let finalUrlStr = attr.url || (data.url || '-');
                        let servingIp = '{{ $resolved_ip }}';
                        
                        let serverHtml = `
                        <div class="vt-detail-row"><div class="vt-detail-label">Serving IP Address</div><div class="vt-detail-val">${servingIp}</div></div>`;
                        
                        let httpHtml = `
                        <div class="vt-detail-row"><div class="vt-detail-label">Final URL</div><div class="vt-detail-val"><a href="${finalUrlStr}" target="_blank" style="color:#5dade2;text-decoration:none;">${finalUrlStr}</a></div></div>
                        ${serverHtml}
                        <div class="vt-detail-row"><div class="vt-detail-label">Status Code</div><div class="vt-detail-val">${attr.last_http_response_code || 'N/A'}</div></div>
                        `;
                        if(attr.last_http_response_content_length) {
                            httpHtml += `<div class="vt-detail-row"><div class="vt-detail-label">Body Length</div><div class="vt-detail-val">${attr.last_http_response_content_length} bytes</div></div>`;
                        }
                        if(attr.last_http_response_content_sha256) {
                            httpHtml += `<div class="vt-detail-row"><div class="vt-detail-label">Body SHA-256</div><div class="vt-detail-val">${attr.last_http_response_content_sha256}</div></div>`;
                        }
                        
                        if(attr.last_http_response_headers && Object.keys(attr.last_http_response_headers).length > 0) {
                            httpHtml += `<div style="padding: 15px 20px; font-weight:600; border-bottom:1px solid var(--border-color); background:rgba(0,0,0,0.02);">Headers</div>`;
                            for(const [k, v] of Object.entries(attr.last_http_response_headers)) {
                                httpHtml += `<div class="vt-detail-row"><div class="vt-detail-label">${k}</div><div class="vt-detail-val">${v}</div></div>`;
                            }
                        }
                        addDetailSection('http-response', 'HTTP Response', httpHtml);
                    }

                    // D. HTML Info
                    if(attr.html_meta && Object.keys(attr.html_meta).length > 0) {
                        let metaHtml = '';
                        for(const [k, v] of Object.entries(attr.html_meta)) {
                            let valStr = Array.isArray(v) ? v.join(', ') : v;
                            metaHtml += `<div class="vt-detail-row"><div class="vt-detail-label">${k}</div><div class="vt-detail-val">${valStr}</div></div>`;
                        }
                        addDetailSection('html-info', 'HTML Info', metaHtml);
                    }

                    // E. Trackers
                    if(attr.trackers && Object.keys(attr.trackers).length > 0) {
                        let trHtml = '';
                        for(const [trName, trData] of Object.entries(attr.trackers)) {
                            trHtml += `<div class="vt-detail-row"><div class="vt-detail-label">${trName}</div><div class="vt-detail-val">Detected</div></div>`;
                        }
                        addDetailSection('trackers', 'Trackers', trHtml);
                    }

                    // F. Network Requests
                    if(data.pagespeed && data.pagespeed.lighthouseResult && data.pagespeed.lighthouseResult.audits['network-requests']) {
                        const reqs = data.pagespeed.lighthouseResult.audits['network-requests'].details.items;
                        if(reqs && reqs.length > 0) {
                            let nrHtml = `<div style="overflow-x:auto;"><table class="vt-detail-table"><thead><tr><th>URL</th><th>Status</th><th>Type</th><th>Size</th></tr></thead><tbody>`;
                            reqs.slice(0, 50).forEach(r => {
                                let sizeStr = r.transferSize ? (r.transferSize / 1024).toFixed(1) + ' KB' : '-';
                                nrHtml += `<tr>
                                    <td style="word-break:break-all; max-width:400px;"><a href="${r.url}" target="_blank" style="color:#5dade2;text-decoration:none;">${r.url}</a></td>
                                    <td>${r.statusCode || 200}</td>
                                    <td>${r.resourceType}</td>
                                    <td>${sizeStr}</td>
                                </tr>`;
                            });
                            nrHtml += `</tbody></table></div>`;
                            if(reqs.length > 50) nrHtml += `<div style="padding:15px; text-align:center; color:var(--text-muted); font-size:12px;">Showing 50 of ${reqs.length} requests (data limited by proxy API)</div>`;
                            
                            addDetailSection('network', 'Network Requests / HTTP Transactions', nrHtml);
                        }
                    }
                    
                    tocList.innerHTML = tocHtml;
                    detContent.innerHTML = detHtml;
                }
            }

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

            // 8. STRUCTURE (ALL AUDITS)
            const metricsToExclude = [
                'first-contentful-paint', 'interactive', 'speed-index', 'total-blocking-time', 
                'largest-contentful-paint', 'cumulative-layout-shift', 'metrics', 'diagnostics', 
                'network-requests', 'network-server-latency', 'mainthread-work-breakdown', 
                'screenshot-thumbnails', 'final-screenshot', 'script-treemap-data'
            ];
            
            let allStructureAudits = [];
            
            for(const [key, audit] of Object.entries(audits)) {
                if(metricsToExclude.includes(key)) continue;
                if(!audit.title) continue;
                
                let score = audit.score;
                let displayMode = audit.scoreDisplayMode;
                let impactClass = 'sa-impact-na';
                let impactText = 'N/A';
                let sortScore = 0; // Higher = worse
                
                if(displayMode === 'notApplicable' || score === null) {
                    impactClass = 'sa-impact-na';
                    impactText = 'N/A';
                    sortScore = 1;
                } else if(displayMode === 'informative' || displayMode === 'manual' || score === 1) {
                    impactClass = 'sa-impact-none';
                    impactText = 'None';
                    sortScore = 0;
                } else if(score >= 0.9) {
                    impactClass = 'sa-impact-low';
                    impactText = 'Low';
                    sortScore = 2;
                } else if(score >= 0.7) {
                    impactClass = 'sa-impact-medlow';
                    impactText = 'Med-Low';
                    sortScore = 3;
                } else if(score >= 0.5) {
                    impactClass = 'sa-impact-med';
                    impactText = 'Med';
                    sortScore = 4;
                } else {
                    impactClass = 'sa-impact-high';
                    impactText = 'High';
                    sortScore = 5;
                }
                
                // Tags
                let tags = [];
                if(key.includes('lcp') || key.includes('render-blocking') || key.includes('server-response') || key.includes('payload')) tags.push('LCP');
                if(key.includes('fcp') || key.includes('render-blocking') || key.includes('server-response')) tags.push('FCP');
                if(key.includes('tbt') || key.includes('mainthread') || key.includes('bootup') || key.includes('javascript') || key.includes('dom')) tags.push('TBT');
                if(key.includes('cls') || key.includes('image-aspect-ratio') || key.includes('unsized-images') || key.includes('layout-shift') || key.includes('animation')) tags.push('CLS');
                tags = [...new Set(tags)];
                
                let displayVal = audit.displayValue || '';
                
                // Helper to escape HTML tags in title and description
                const escapeHtml = (unsafe) => {
                    return (unsafe || '').toString()
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;");
                };
                
                allStructureAudits.push({
                    id: key,
                    title: escapeHtml(audit.title),
                    description: escapeHtml(audit.description),
                    impactClass: impactClass,
                    impactText: impactText,
                    sortScore: sortScore,
                    displayVal: displayVal,
                    tags: tags,
                    rawScore: score !== null ? score : 1 
                });
            }
            
            allStructureAudits.sort((a, b) => {
                if(b.sortScore !== a.sortScore) return b.sortScore - a.sortScore;
                return a.rawScore - b.rawScore;
            });
            
            const saImpactList = document.getElementById('sa-impact-list');
            const saNoImpactList = document.getElementById('sa-no-impact-list');
            
            function renderStructure(filter) {
                if(!saImpactList || !saNoImpactList) return;
                saImpactList.innerHTML = '';
                saNoImpactList.innerHTML = '';
                
                let hasImpact = false;
                let hasNoImpact = false;
                
                allStructureAudits.forEach(audit => {
                    if(filter !== 'all' && !audit.tags.includes(filter.toUpperCase())) return;
                    
                    const tagsHtml = audit.tags.map(t => `<span class="sa-tag">${t}</span>`).join('');
                    const descHtml = (audit.description || '').replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" style="color:#22679c;">$1</a>');
                    
                    const row = document.createElement('div');
                    row.className = 'sa-row';
                    row.innerHTML = `
                        <div class="sa-impact-box ${audit.impactClass}">${audit.impactText}</div>
                        <div class="sa-audit-main" onclick="this.parentElement.classList.toggle('open')">
                            <div class="sa-audit-left">
                                <h4 class="sa-audit-title">${audit.title}</h4>
                                ${tagsHtml}
                            </div>
                            <div class="sa-audit-right">
                                <div class="sa-display-val">${audit.displayVal}</div>
                                <div class="sa-chevron">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                            </div>
                        </div>
                        <div class="sa-details">
                            ${descHtml}
                        </div>
                    `;
                    
                    if(audit.impactText === 'None') {
                        saNoImpactList.appendChild(row);
                        hasNoImpact = true;
                    } else {
                        saImpactList.appendChild(row);
                        hasImpact = true;
                    }
                });
                
                if(!hasImpact) {
                    saImpactList.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">No issues found for this filter.</div>';
                }
                if(!hasNoImpact) {
                    saNoImpactList.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted);">No passed audits found for this filter.</div>';
                }
            }
            
            renderStructure('all');
            
            const saFilterBtns = document.querySelectorAll('.sa-filter-btn');
            saFilterBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    saFilterBtns.forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    renderStructure(e.target.dataset.filter);
                });
            });
            
            const saToggleBtn = document.getElementById('sa-toggle-btn');
            const saToggleText = document.getElementById('sa-toggle-text');
            const saToggleIcon = document.getElementById('sa-toggle-icon');
            if(saToggleBtn && saNoImpactList) {
                saToggleBtn.addEventListener('click', () => {
                    if(saNoImpactList.style.display === 'none') {
                        saNoImpactList.style.display = 'block';
                        saToggleText.textContent = 'Hide No Impact Audits';
                        saToggleIcon.style.transform = 'rotate(180deg)';
                    } else {
                        saNoImpactList.style.display = 'none';
                        saToggleText.textContent = 'Show No Impact Audits';
                        saToggleIcon.style.transform = 'rotate(0deg)';
                    }
                });
            }
        }
    </script>
</body>
</html>