<div class="header">
    <div class="url-title">
        <a href="{{ $report->url }}" target="_blank" class="url-link">{{ $report->url }}</a>
        <span class="badge-strategy" id="strategy-badge"></span>
    </div>
    
    <div class="header-actions">
        <!-- Theme Toggle Switch -->
        <label class="theme-switch" for="theme-toggle" title="Toggle Dark/Light Mode">
            <input type="checkbox" id="theme-toggle">
            <span class="slider"></span>
        </label>
        
        <!-- Export Dropdown -->
        <div class="export-dropdown">
            <button class="btn-export">Export Report ▼</button>
            <div class="export-menu">
                <a href="{{ route('analyzer.export', $report->id) }}" class="export-item">Export to PDF</a>
                <a href="{{ route('analyzer.exportWord', $report->id) }}" class="export-item">Export to Word (.docx)</a>
            </div>
        </div>

        <a href="{{ route('analyzer.index') }}" class="btn-back">← Analyze Another</a>
    </div>
</div>
