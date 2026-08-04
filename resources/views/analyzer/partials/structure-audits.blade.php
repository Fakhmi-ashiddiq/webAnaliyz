<div class="sa-container">
    <div class="sa-header-area">
        <h3 class="section-title">Structure</h3>
        <div class="sa-filters">
            <span class="sa-filter-label">Show Audits Relevant to</span>
            <button class="sa-filter-btn active" data-filter="all">All</button>
            <button class="sa-filter-btn" data-filter="fcp">FCP</button>
            <button class="sa-filter-btn" data-filter="lcp">LCP</button>
            <button class="sa-filter-btn" data-filter="tbt">TBT</button>
            <button class="sa-filter-btn" data-filter="cls">CLS</button>
        </div>
    </div>

    <div class="sa-table-header">
        <div class="sa-col-impact">IMPACT</div>
        <div class="sa-col-audit">AUDIT</div>
    </div>

    <div id="sa-impact-list" class="sa-list"></div>

    <div class="sa-toggle-no-impact" id="sa-toggle-btn">
        <span id="sa-toggle-text">Show No Impact Audits</span>
        <svg id="sa-toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.3s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </div>

    <div id="sa-no-impact-list" class="sa-list" style="display: none;"></div>
</div>
