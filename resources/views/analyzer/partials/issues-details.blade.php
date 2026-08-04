<div class="issues-details-container">
    <!-- Left Column: Top Issues -->
    <div class="issues-col">
        <div class="issues-header">
            <div>
                <h3 class="section-title">Top Issues</h3>
                <p class="section-desc">These audits are identified as the top issues impacting <strong>your performance</strong>.</p>
            </div>
            <div class="issues-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="fcp">FCP</button>
                <button class="filter-btn" data-filter="lcp">LCP</button>
                <button class="filter-btn" data-filter="tbt">TBT</button>
                <button class="filter-btn" data-filter="cls">CLS</button>
            </div>
        </div>
        
        <div class="issues-list" id="issues-list">
            <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 14px;">Menganalisis isu performa...</div>
            <!-- JS will populate accordions here -->
        </div>
        <p class="section-desc" style="margin-top: 15px;">Improving these audits seen here can help as a starting point for overall performance gains.</p>
    </div>

    <!-- Right Column: Page Details -->
    <div class="details-col">
        <div class="details-header">
            <h3 class="section-title">Page Details</h3>
            <p class="section-desc">Pages with smaller total sizes and fewer requests tend to load faster.</p>
        </div>

        <!-- Fully Loaded Time (TTI) -->
        <div class="detail-timeline">
            <div class="timeline-track">
                <div class="timeline-marker" style="left: 50%;">
                    <div class="marker-val" id="val-fully-loaded">0s</div>
                    <div class="marker-label">Fully Loaded Time</div>
                </div>
            </div>
        </div>

        <!-- Total Page Size -->
        <div class="detail-block">
            <div class="detail-title">Total Page Size - <span id="val-total-size">0MB</span></div>
            <div class="stacked-bar" id="bar-page-size">
                <!-- JS will populate segments -->
            </div>
        </div>

        <!-- Total Page Requests -->
        <div class="detail-block" style="margin-top: 30px;">
            <div class="detail-title">Total Page Requests - <span id="val-total-req">0</span></div>
            <div class="stacked-bar" id="bar-page-req">
                <!-- JS will populate segments -->
            </div>
        </div>
        
        <p class="section-desc" style="margin-top: 20px;">Look into reducing JavaScript, reducing web-fonts, and image optimization to ensure a lightweight and streamlined website.</p>
    </div>
</div>
