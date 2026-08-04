<div class="vt-dashboard">
    <div class="vt-score-panel">
        <div class="vt-circle-wrapper">
            <svg viewBox="0 0 36 36" class="vt-circular-chart">
                <path class="vt-circle-bg"
                  d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831"
                />
                <path class="vt-circle"
                  id="vt-score-circle"
                  stroke-dasharray="0, 100"
                  d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831"
                />
                <text x="18" y="16" class="vt-score-main" id="vt-malicious-count">0</text>
                <text x="18" y="24" class="vt-score-sub" id="vt-engine-count">/0</text>
            </svg>
        </div>
        <div class="vt-community-box">
            <div class="vt-comm-label">Community<br>Score</div>
            <div class="vt-comm-score-badge" id="vt-comm-badge">
                <span id="vt-comm-score">0</span>
            </div>
        </div>
    </div>

    <div class="vt-info-panel">
        <div class="vt-info-header">
            <div class="vt-url-report-title">URL report for <span id="vt-analysis-date" style="font-weight: 600;">...</span></div>
        </div>
        
        <div class="vt-info-body">
            <div class="vt-info-main">
                <div class="vt-main-url" id="vt-main-url">...</div>
                <div class="vt-domain-ip">
                    <span id="vt-domain">...</span> 
                    <span id="vt-ip" class="vt-ip-text">...</span>
                </div>
                <div class="vt-tags" id="vt-tags">
                </div>
            </div>
            
            <div class="vt-stats-grid">
                <div class="vt-stat-item">
                    <div class="vt-stat-label">Status</div>
                    <div class="vt-stat-val" id="vt-status-code">...</div>
                </div>
                <div class="vt-stat-item">
                    <div class="vt-stat-label">Content Type</div>
                    <div class="vt-stat-val" id="vt-content-type">...</div>
                </div>
                <div class="vt-stat-item">
                    <div class="vt-stat-label">Last Analysis Date</div>
                    <div class="vt-stat-val" id="vt-last-analysis">...</div>
                </div>
                <div class="vt-globe-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
