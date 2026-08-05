<div class="vt-sh-container">
    <!-- Score Panel -->
    <div class="vt-page-stats-card">
        <div class="vt-ps-header">Security Headers Score</div>
        <div class="vt-sh-score-wrap">
            <div class="vt-sh-score">
                <svg viewBox="0 0 36 36" class="vt-circular-chart">
                    <path class="vt-circle-bg"
                      d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <path class="vt-circle"
                      id="sh-score-circle"
                      stroke-dasharray="0, 100"
                      d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <text x="18" y="16" class="vt-score-main" id="sh-score-text">0</text>
                    <text x="18" y="24" class="vt-score-sub">/100</text>
                </svg>
            </div>
            <div class="vt-sh-score-side">
                <div class="vt-sh-grade" id="sh-grade">-</div>
                <div class="vt-sh-grade-desc" id="sh-grade-desc">Menunggu data...</div>
                <div class="vt-sh-final-url" id="sh-final-url"></div>
            </div>
        </div>
    </div>

    <!-- Per-Header Analysis -->
    <div class="vt-page-stats-card">
        <div class="vt-ps-header">Header Analysis</div>
        <div id="sh-items" class="vt-sh-items">
            <div class="vt-sh-loading">Memuat data security headers...</div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="vt-page-stats-card">
        <div class="vt-ps-header">Recommendations</div>
        <div id="sh-recommendations" class="vt-sh-rec">
            <div class="vt-sh-loading">Memuat rekomendasi...</div>
        </div>
    </div>

    <!-- Raw HTTP Response Headers -->
    <div class="vt-page-stats-card">
        <div class="vt-ps-header">HTTP Response Headers</div>
        <div class="vt-sh-table-wrap">
            <table class="vt-detail-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Header</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody id="sh-headers-body">
                    <tr>
                        <td colspan="2" style="text-align: center; color: var(--text-muted);">Header tidak tersedia</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
