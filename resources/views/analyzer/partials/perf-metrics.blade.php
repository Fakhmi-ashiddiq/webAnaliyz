<div class="pm-container">
    <div class="pm-header">
        <div>
            <h3 class="section-title">Performance Metrics</h3>
            <p class="section-desc">The following metrics are generated using Lighthouse Performance data.</p>
        </div>
        <div class="pm-toggle-wrap">
            <span class="pm-toggle-label">Metric details</span>
            <label class="pm-switch">
                <input type="checkbox" id="pm-details-toggle" checked>
                <span class="pm-slider">
                    <span class="pm-slider-text on">ON</span>
                    <span class="pm-slider-text off">OFF</span>
                </span>
            </label>
        </div>
    </div>

    <div class="pm-grid">
        <!-- First Contentful Paint -->
        <div class="pm-card" style="--card-border: #4dabf5;">
            <div class="pm-card-left">
                <h4 class="pm-card-title">First Contentful Paint</h4>
                <div class="pm-card-desc">
                    How quickly content like text or images are painted onto your page. A good user experience is 1.8s or less. <a href="https://web.dev/articles/fcp" target="_blank" class="pm-learn-more">Learn more.</a>
                </div>
            </div>
            <div class="pm-card-right">
                <div class="pm-badge" id="pm-badge-fcp">N/A</div>
                <div class="pm-val" id="pm-val-fcp">-</div>
            </div>
        </div>

        <!-- Time to Interactive -->
        <div class="pm-card" style="--card-border: #ab47bc;">
            <div class="pm-card-left">
                <h4 class="pm-card-title">Time to Interactive</h4>
                <div class="pm-card-desc">
                    How long it takes for your page to become fully interactive. A good user experience is 3.8s or less. <a href="https://web.dev/articles/tti" target="_blank" class="pm-learn-more">Learn more.</a>
                </div>
            </div>
            <div class="pm-card-right">
                <div class="pm-badge" id="pm-badge-tti">N/A</div>
                <div class="pm-val" id="pm-val-tti">-</div>
            </div>
        </div>

        <!-- Speed Index -->
        <div class="pm-card" style="--card-border: #ec407a;">
            <div class="pm-card-left">
                <h4 class="pm-card-title">Speed Index</h4>
                <div class="pm-card-desc">
                    How quickly the contents of your page are visibly populated. A good user experience is 3.4s or less. <a href="https://web.dev/articles/speed-index" target="_blank" class="pm-learn-more">Learn more.</a>
                </div>
            </div>
            <div class="pm-card-right">
                <div class="pm-badge" id="pm-badge-si">N/A</div>
                <div class="pm-val" id="pm-val-si">-</div>
            </div>
        </div>

        <!-- Total Blocking Time -->
        <div class="pm-card" style="--card-border: #5c6bc0;">
            <div class="pm-card-left">
                <h4 class="pm-card-title">Total Blocking Time</h4>
                <div class="pm-card-desc">
                    How much time is blocked by scripts during your page loading process. A good user experience is 200ms or less. <a href="https://web.dev/articles/tbt" target="_blank" class="pm-learn-more">Learn more.</a>
                </div>
            </div>
            <div class="pm-card-right">
                <div class="pm-badge" id="pm-badge-tbt">N/A</div>
                <div class="pm-val" id="pm-val-tbt">-</div>
            </div>
        </div>

        <!-- Largest Contentful Paint -->
        <div class="pm-card" style="--card-border: #29b6f6;">
            <div class="pm-card-left">
                <h4 class="pm-card-title">Largest Contentful Paint</h4>
                <div class="pm-card-desc">
                    How long it takes for the largest element of content (i.e. a hero image) to be painted on your page. A good user experience is 2.5s or less. <a href="https://web.dev/articles/lcp" target="_blank" class="pm-learn-more">Learn more.</a>
                </div>
            </div>
            <div class="pm-card-right">
                <div class="pm-badge" id="pm-badge-lcp">N/A</div>
                <div class="pm-val" id="pm-val-lcp">-</div>
            </div>
        </div>

        <!-- Cumulative Layout Shift -->
        <div class="pm-card" style="--card-border: #26a69a;">
            <div class="pm-card-left">
                <h4 class="pm-card-title">Cumulative Layout Shift</h4>
                <div class="pm-card-desc">
                    How much your page's layout shifts as it loads. A good user experience is a score of 0.1 or less. <a href="https://web.dev/articles/cls" target="_blank" class="pm-learn-more">Learn more.</a>
                </div>
            </div>
            <div class="pm-card-right">
                <div class="pm-badge" id="pm-badge-cls">N/A</div>
                <div class="pm-val" id="pm-val-cls">-</div>
            </div>
        </div>
    </div>
</div>
