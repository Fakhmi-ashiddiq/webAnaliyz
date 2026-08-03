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

        <a href="{{ route('analyzer.index') }}" class="btn-back">← Analyze Another</a>
    </div>
</div>
