<div class="crux-section">
    <div class="crux-header" id="crux-header">
        <div class="crux-title-wrap">
            <div class="crux-icon" id="crux-icon">✖</div>
            <div class="crux-title-text">Core Web Vitals Assessment: <span id="crux-status-text">Failed</span></div>
        </div>
        <div class="crux-header-right">
            <span class="crux-results-for">Results for: <a href="{{ $report->url }}" target="_blank">{{ $report->url }}</a></span>
            <div class="crux-tabs">
                <button class="crux-tab active" data-target="url">This URL</button>
                <button class="crux-tab" data-target="origin">Origin</button>
            </div>
        </div>
    </div>
    
    <div id="crux-grid" class="crux-grid">
        <!-- JS renders here -->
    </div>
    <div class="crux-footer">
        <span>OTHER NOTABLE METRICS</span>
    </div>
    <div id="crux-grid-other" class="crux-grid">
        <!-- JS renders here -->
    </div>
    <div id="crux-empty" style="padding: 20px; text-align: center; color: var(--text-muted); display: none;">
        No real-world data (CrUX) available for this URL/Origin.
    </div>
</div>
