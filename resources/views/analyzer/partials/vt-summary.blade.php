<div class="vt-inner-container">
    <!-- Inner Tabs -->
    <div class="vt-inner-tabs">
        <button class="vt-inner-tab active" data-target="vt-tab-summary">SUMMARY</button>
        <button class="vt-inner-tab" data-target="vt-tab-detection">DETECTION</button>
        <button class="vt-inner-tab" data-target="vt-tab-security-headers">SECURITY HEADERS</button>
        <button class="vt-inner-tab" data-target="vt-tab-details">DETAILS</button>
    </div>

    <!-- Tab Contents -->
    <div class="vt-inner-content">
        <!-- Summary Tab -->
        <div id="vt-tab-summary" class="vt-tab-pane active">
            <div class="vt-page-stats-card">
                <div class="vt-ps-header">Page Stats</div>
                <div class="vt-ps-grid">
                    
                    <div class="vt-ps-item" onclick="gotoDetails('network')" style="cursor: pointer;" title="View in Details tab">
                        <div class="vt-ps-left">
                            <svg class="vt-ps-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span class="vt-ps-label" style="color: #5dade2;">Network Requests</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-requests">...</div>
                    </div>

                    <div class="vt-ps-item" onclick="gotoDetails('network')" style="cursor: pointer;" title="View in Details tab">
                        <div class="vt-ps-left">
                            <svg class="vt-ps-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                            <span class="vt-ps-label" style="color: #5dade2;">HTTPS Requests</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-https">...</div>
                    </div>

                    <div class="vt-ps-item" onclick="gotoDetails('network')" style="cursor: pointer;" title="View in Details tab">
                        <div class="vt-ps-left">
                            <svg class="vt-ps-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                            <span class="vt-ps-label" style="color: #5dade2;">Domains</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-domains">...</div>
                    </div>

                    <div class="vt-ps-item" onclick="gotoDetails('http-response')" style="cursor: pointer;" title="View in Details tab">
                        <div class="vt-ps-left">
                            <div class="vt-ps-icon vt-icon-text" style="color: #5dade2; border-color: #5dade2;">IP</div>
                            <span class="vt-ps-label" style="color: #5dade2;">IP Addresses</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-ips">N/A</div>
                    </div>

                    <div class="vt-ps-item">
                        <div class="vt-ps-left">
                            <div class="vt-ps-icon vt-icon-text">IP</div>
                            <span class="vt-ps-label">IPv6 Addresses</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-ipv6">N/A</div>
                    </div>

                    <div class="vt-ps-item">
                        <div class="vt-ps-left">
                            <svg class="vt-ps-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            <span class="vt-ps-label">Countries</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-countries">N/A</div>
                    </div>

                    <div class="vt-ps-item" onclick="gotoDetails('http-response')" style="cursor: pointer;" title="View in Details tab">
                        <div class="vt-ps-left">
                            <svg class="vt-ps-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"></path><path d="M8 8h8v8H8z"></path></svg>
                            <span class="vt-ps-label" style="color: #5dade2;">Response Size</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-size">...</div>
                    </div>

                    <div class="vt-ps-item">
                        <div class="vt-ps-left">
                            <svg class="vt-ps-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8.5 9.5v.01"></path><path d="M15.5 9.5v.01"></path><path d="M12 14.5v.01"></path><path d="M9 16.5A5 5 0 0 0 15 16.5"></path></svg>
                            <span class="vt-ps-label">Cookies</span>
                        </div>
                        <div class="vt-ps-val" id="vt-ps-cookies">N/A</div>
                    </div>

                </div>
            </div>

            <!-- URL Overview -->
            <div class="vt-page-stats-card">
                <div class="vt-ps-header">URL Overview</div>
                <div class="vt-url-overview">
                    
                    <div class="vt-uo-row">
                        <div class="vt-uo-label">Vendors Analysis:</div>
                        <div class="vt-uo-val" id="vt-uo-vendors" style="display: flex; align-items: center; gap: 8px;">
                            <svg class="vt-ps-icon" style="color: var(--green);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            <span style="color: var(--green);">No security vendors flagged this URL as malicious</span>
                        </div>
                    </div>

                    <div class="vt-uo-row">
                        <div class="vt-uo-label">Final URL:</div>
                        <div class="vt-uo-val" id="vt-uo-url">...</div>
                    </div>

                    <div class="vt-uo-row">
                        <div class="vt-uo-label">Domain:</div>
                        <div class="vt-uo-val" id="vt-uo-domain">...</div>
                    </div>

                    <div class="vt-uo-row">
                        <div class="vt-uo-label">Serving IP:</div>
                        <div class="vt-uo-val" id="vt-uo-ip">N/A</div>
                    </div>

                    <div class="vt-uo-row">
                        <div class="vt-uo-label">Current Status Code:</div>
                        <div class="vt-uo-val" id="vt-uo-status">...</div>
                    </div>

                    <div class="vt-uo-row">
                        <div class="vt-uo-label">Category:</div>
                        <div class="vt-uo-val" id="vt-uo-category">
                            <!-- Category tags here -->
                        </div>
                    </div>

                    <div class="vt-uo-row">
                        <div class="vt-uo-label">Tags:</div>
                        <div class="vt-uo-val" id="vt-uo-tags">
                            <!-- Tags here -->
                        </div>
                    </div>

                </div>
            </div>

            <!-- History -->
            <div class="vt-history-box">
                <div class="vt-ps-header">History</div>
                <div class="vt-history-row">
                    <div class="vt-history-label">First Submission</div>
                    <div class="vt-history-val">
                        <span id="vt-hist-first">...</span>
                        <span class="vt-history-unknown" id="vt-hist-first-unknown" style="display:none;"><svg style="width:12px; height:12px; margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> UNKNOWN OR UNSPECIFIED</span>
                    </div>
                </div>
                <div class="vt-history-row">
                    <div class="vt-history-label">Last Submission</div>
                    <div class="vt-history-val">
                        <span id="vt-hist-last">...</span>
                        <span class="vt-history-unknown" id="vt-hist-last-unknown" style="display:none;"><svg style="width:12px; height:12px; margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> UNKNOWN OR UNSPECIFIED</span>
                    </div>
                </div>
                <div class="vt-history-row">
                    <div class="vt-history-label">Last Analysis</div>
                    <div class="vt-history-val">
                        <span id="vt-hist-analysis">...</span>
                    </div>
                </div>
            </div>

            <!-- Web Technologies -->
            <div class="vt-history-box">
                <div class="vt-ps-header">Web Technologies</div>
                <table class="vt-tech-table">
                    <thead>
                        <tr>
                            <th>Technology</th>
                            <th>Version</th>
                        </tr>
                    </thead>
                    <tbody id="vt-tech-body">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Detection Tab -->
        <div id="vt-tab-detection" class="vt-tab-pane">
            @include('analyzer.partials.vt-detection')
        </div>

        <!-- Details Tab -->
        <div id="vt-tab-details" class="vt-tab-pane">
            @include('analyzer.partials.vt-details')
        </div>

        <!-- Security Headers Tab -->
        <div id="vt-tab-security-headers" class="vt-tab-pane">
            @include('analyzer.partials.vt-security-headers')
        </div>
    </div>
</div>
