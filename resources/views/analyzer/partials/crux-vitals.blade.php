<div class="crux-container">
    <div class="crux-header">
        <div class="crux-status-area">
            <div class="crux-icon" id="crux-status-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <h2 class="crux-title">Core Web Vitals Assessment: <span id="crux-status-text">Loading...</span></h2>
        </div>
        <div class="crux-toggle-area">
            <span class="crux-toggle-label">Results for:</span>
            <div class="crux-btn-group">
                <button class="crux-btn active" id="btn-this-url">This URL</button>
                <button class="crux-btn" id="btn-origin">Origin</button>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="crux-empty-state" style="display: none; padding: 40px; text-align: center; color: var(--text-muted); background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 10px; opacity: 0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <div style="font-size: 16px; font-weight: 500; color: var(--text-main); margin-bottom: 5px;">Data Tidak Tersedia (N/A)</div>
        <div>Tidak ada data pengalaman pengguna dunia nyata (CrUX) yang memadai untuk halaman ini. Biasanya terjadi pada website baru atau yang traffic-nya belum cukup besar.</div>
    </div>

    <!-- Metrics Content -->
    <div id="crux-metrics-content" style="display: none;">
        <div class="crux-grid" id="crux-grid-top">
            <!-- LCP -->
            <div class="crux-card" id="card-lcp">
                <div class="crux-card-header">
                    <div class="crux-card-title">Largest Contentful Paint (LCP)</div>
                    <div class="crux-badge" id="badge-lcp">-</div>
                </div>
                <div class="crux-card-val" id="val-lcp">N/A</div>
                
                <!-- Bar -->
                <div class="crux-bar-container">
                    <div class="crux-bar-seg bg-green" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-orange" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-red" style="width: 33.33%;"></div>
                    <div class="crux-needle" id="needle-lcp"></div>
                </div>

                <div class="crux-table-wrap">
                    <div class="crux-table-row"><span>Good <small>(&le; 2.5 s)</small></span> <span id="dist-lcp-good">0%</span></div>
                    <div class="crux-table-row"><span>Needs Improvement <small>(2.5 s - 4 s)</small></span> <span id="dist-lcp-ni">0%</span></div>
                    <div class="crux-table-row"><span>Poor <small>(&gt; 4 s)</small></span> <span id="dist-lcp-poor">0%</span></div>
                </div>
            </div>

            <!-- INP -->
            <div class="crux-card" id="card-inp">
                <div class="crux-card-header">
                    <div class="crux-card-title">Interaction to Next Paint (INP)</div>
                    <div class="crux-badge" id="badge-inp">-</div>
                </div>
                <div class="crux-card-val" id="val-inp">N/A</div>
                
                <div class="crux-bar-container">
                    <div class="crux-bar-seg bg-green" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-orange" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-red" style="width: 33.33%;"></div>
                    <div class="crux-needle" id="needle-inp"></div>
                </div>

                <div class="crux-table-wrap">
                    <div class="crux-table-row"><span>Good <small>(&le; 200 ms)</small></span> <span id="dist-inp-good">0%</span></div>
                    <div class="crux-table-row"><span>Needs Improvement <small>(200 - 500 ms)</small></span> <span id="dist-inp-ni">0%</span></div>
                    <div class="crux-table-row"><span>Poor <small>(&gt; 500 ms)</small></span> <span id="dist-inp-poor">0%</span></div>
                </div>
            </div>

            <!-- CLS -->
            <div class="crux-card" id="card-cls">
                <div class="crux-card-header">
                    <div class="crux-card-title">Cumulative Layout Shift (CLS)</div>
                    <div class="crux-badge" id="badge-cls">-</div>
                </div>
                <div class="crux-card-val" id="val-cls">N/A</div>
                
                <div class="crux-bar-container">
                    <div class="crux-bar-seg bg-green" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-orange" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-red" style="width: 33.33%;"></div>
                    <div class="crux-needle" id="needle-cls"></div>
                </div>

                <div class="crux-table-wrap">
                    <div class="crux-table-row"><span>Good <small>(&le; 0.1)</small></span> <span id="dist-cls-good">0%</span></div>
                    <div class="crux-table-row"><span>Needs Improvement <small>(0.1 - 0.25)</small></span> <span id="dist-cls-ni">0%</span></div>
                    <div class="crux-table-row"><span>Poor <small>(&gt; 0.25)</small></span> <span id="dist-cls-poor">0%</span></div>
                </div>
            </div>
        </div>

        <div class="crux-separator">OTHER NOTABLE METRICS</div>

        <div class="crux-grid" id="crux-grid-bottom" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
            <!-- FCP -->
            <div class="crux-card" id="card-fcp">
                <div class="crux-card-header">
                    <div class="crux-card-title">First Contentful Paint (FCP)</div>
                    <div class="crux-badge" id="badge-fcp">-</div>
                </div>
                <div class="crux-card-val" id="val-fcp">N/A</div>
                
                <div class="crux-bar-container">
                    <div class="crux-bar-seg bg-green" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-orange" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-red" style="width: 33.33%;"></div>
                    <div class="crux-needle" id="needle-fcp"></div>
                </div>

                <div class="crux-table-wrap">
                    <div class="crux-table-row"><span>Good <small>(&le; 1.8 s)</small></span> <span id="dist-fcp-good">0%</span></div>
                    <div class="crux-table-row"><span>Needs Improvement <small>(1.8 s - 3 s)</small></span> <span id="dist-fcp-ni">0%</span></div>
                    <div class="crux-table-row"><span>Poor <small>(&gt; 3 s)</small></span> <span id="dist-fcp-poor">0%</span></div>
                </div>
            </div>

            <!-- TTFB -->
            <div class="crux-card" id="card-ttfb">
                <div class="crux-card-header">
                    <div class="crux-card-title">Time to First Byte (TTFB)</div>
                    <div class="crux-badge" id="badge-ttfb">-</div>
                </div>
                <div class="crux-card-val" id="val-ttfb">N/A</div>
                
                <div class="crux-bar-container">
                    <div class="crux-bar-seg bg-green" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-orange" style="width: 33.33%;"></div>
                    <div class="crux-bar-seg bg-red" style="width: 33.33%;"></div>
                    <div class="crux-needle" id="needle-ttfb"></div>
                </div>

                <div class="crux-table-wrap">
                    <div class="crux-table-row"><span>Good <small>(&le; 0.8 s)</small></span> <span id="dist-ttfb-good">0%</span></div>
                    <div class="crux-table-row"><span>Needs Improvement <small>(0.8 s - 1.8 s)</small></span> <span id="dist-ttfb-ni">0%</span></div>
                    <div class="crux-table-row"><span>Poor <small>(&gt; 1.8 s)</small></span> <span id="dist-ttfb-poor">0%</span></div>
                </div>
            </div>
            
            <!-- Empty third column to keep grid aligned -->
            <div></div>
        </div>
    </div>
</div>
