<div class="two-col">
    <div class="col-left">
        <div class="section-title">Top Issues</div>
        <p class="section-desc">These audits are identified as the top issues impacting your performance.</p>
        
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="fcp">FCP</button>
            <button class="filter-btn" data-filter="lcp">LCP</button>
            <button class="filter-btn" data-filter="tbt">TBT</button>
            <button class="filter-btn" data-filter="cls">CLS</button>
        </div>

        <div id="issues-list" class="issues-container">
            <!-- JS renders here -->
        </div>
        <p class="section-desc" style="margin-top: 10px;">Improving these audits seen here can help as a starting point for overall performance gains.</p>
    </div>

    <div class="col-right">
        <div class="section-title">Page Details <span class="help-icon">?</span></div>
        <p class="section-desc">Pages with smaller total sizes and fewer requests tend to load faster.</p>
        
        <div class="card pd-box">
            <div class="pd-header">Total Page Size - <span id="total-size">0 MB</span></div>
            <div class="pd-bar" id="size-bar">
                <!-- JS renders here -->
            </div>

            <div class="pd-header" style="margin-top:25px;">Total Page Requests - <span id="total-req">0</span></div>
            <div class="pd-bar" id="req-bar">
                <!-- JS renders here -->
            </div>
            
            <p class="pd-footer">Look into reducing JavaScript, reducing web-fonts, and image optimization to ensure a lightweight and streamlined website.</p>
        </div>
    </div>
</div>
