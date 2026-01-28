<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate Dashboard Alerts</title>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #3498db;
            --light: #ecf0f1;
            --dark: #1a252f;
            --shadow: rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --radius: 6px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .dashboard-container {
            width: 100%;
            max-width: 680px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .dashboard-header {
            background-color: var(--primary);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-header h1 {
            font-size: 18px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .dashboard-filter {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            padding: 5px 12px;
            border-radius: var(--radius);
            font-size: 12px;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn:hover, .filter-btn.active {
            background: rgba(255, 255, 255, 0.3);
        }

        .alerts-container {
            padding: 0;
            max-height: 540px;
            overflow-y: auto;
        }

        .alert {
            padding: 15px 20px;
            margin: 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: flex-start;
            position: relative;
            opacity: 1;
            transform: translateY(0);
            transition: var(--transition);
            overflow: hidden;
        }

        .alert.dismissed {
            opacity: 0;
            transform: translateX(100%);
            max-height: 0;
            padding: 0;
            margin: 0;
        }

        .alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .alert-content {
            flex-grow: 1;
        }

        .alert-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }

        .alert-time {
            font-size: 12px;
            color: #95a5a6;
            font-weight: normal;
        }

        .alert-message {
            font-size: 13px;
            line-height: 1.4;
            color: #576574;
        }

        .alert-actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }

        .alert-btn {
            border: none;
            background: none;
            font-size: 12px;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .primary-action {
            background-color: var(--light);
            color: var(--primary);
        }

        .primary-action:hover {
            background-color: var(--primary);
            color: white;
        }

        .alert-dismiss {
            color: #95a5a6;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            position: absolute;
            right: 15px;
            top: 15px;
            opacity: 0;
        }

        .alert:hover .alert-dismiss {
            opacity: 1;
        }

        .alert-dismiss:hover {
            background-color: var(--light);
            color: var(--danger);
        }

        /* Alert type styling */
        .alert-critical .alert-icon {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger);
        }

        .alert-warning .alert-icon {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }

        .alert-info .alert-icon {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--info);
        }

        .alert-success .alert-icon {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success);
        }

        /* Tooltip styles */
        .tooltip {
            position: relative;
        }

        .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: var(--dark);
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 6px 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: var(--dark) transparent transparent transparent;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* No alerts message */
        .no-alerts {
            padding: 60px 20px;
            text-align: center;
            color: #95a5a6;
        }

        .no-alerts svg {
            opacity: 0.3;
            margin-bottom: 15px;
        }

        .no-alerts p {
            font-size: 15px;
        }

        /* Summary bar */
        .summary-bar {
            display: flex;
            padding: 15px 25px;
            background-color: var(--light);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .summary-item {
            display: flex;
            align-items: center;
            margin-right: 20px;
            font-size: 13px;
        }

        .summary-count {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            font-weight: 600;
            font-size: 12px;
        }

        .critical-count {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--danger);
        }

        .warning-count {
            background-color: rgba(243, 156, 18, 0.15);
            color: var(--warning);
        }

        .info-count {
            background-color: rgba(52, 152, 219, 0.15);
            color: var(--info);
        }

        /* Animation for new alerts */
        @keyframes slideInFromTop {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert-new {
            animation: slideInFromTop 0.4s ease forwards;
        }

        /* Custom scrollbar */
        .alerts-container::-webkit-scrollbar {
            width: 8px;
        }

        .alerts-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .alerts-container::-webkit-scrollbar-thumb {
            background: #ccd1d6;
            border-radius: 4px;
        }

        .alerts-container::-webkit-scrollbar-thumb:hover {
            background: #b3b9be;
        }

        /* Responsive adjustments */
        @media (max-width: 500px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .dashboard-filter {
                margin-top: 10px;
            }
            
            .alert {
                padding: 12px 15px;
            }
            
            .alert-icon {
                width: 32px;
                height: 32px;
            }
            
            .summary-bar {
                flex-wrap: wrap;
            }
            
            .summary-item {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>System Notifications</h1>
            <div class="dashboard-filter">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="critical">Critical</button>
                <button class="filter-btn" data-filter="warning">Warning</button>
                <button class="filter-btn" data-filter="info">Info</button>
            </div>
        </div>

        <div class="summary-bar">
            <div class="summary-item">
                <span class="summary-count critical-count">3</span>
                <span>Critical</span>
            </div>
            <div class="summary-item">
                <span class="summary-count warning-count">4</span>
                <span>Warning</span>
            </div>
            <div class="summary-item">
                <span class="summary-count info-count">5</span>
                <span>Info</span>
            </div>
        </div>

        <div class="alerts-container">
            <div class="alert alert-critical">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Database server approaching capacity <span class="alert-time">12 min ago</span></div>
                    <div class="alert-message">Primary database server at 92% capacity. Additional load may impact system performance.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Scale Resources
                            <span class="tooltip-text">Automatically provision additional database resources</span>
                        </button>
                        <button class="alert-btn tooltip">View Metrics
                            <span class="tooltip-text">See detailed performance metrics and usage trends</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-warning">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">API rate limit reached <span class="alert-time">34 min ago</span></div>
                    <div class="alert-message">Payment processor API has reached 85% of hourly rate limit. May impact transaction processing times.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Adjust Threshold
                            <span class="tooltip-text">Modify rate limit parameters for this service</span>
                        </button>
                        <button class="alert-btn tooltip">View API Status
                            <span class="tooltip-text">Check current API status and request volume</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-critical">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Security alert: Unusual login pattern <span class="alert-time">47 min ago</span></div>
                    <div class="alert-message">Multiple failed login attempts detected from IP 183.56.121.88 for admin account. Location: Singapore.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Lock Account
                            <span class="tooltip-text">Temporarily lock this account for security</span>
                        </button>
                        <button class="alert-btn tooltip">Review Activity
                            <span class="tooltip-text">View detailed login attempt logs</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-info">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Certificate expiring soon <span class="alert-time">1 hour ago</span></div>
                    <div class="alert-message">SSL certificate for api.example.com will expire in 14 days. Renewal recommended.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Auto-Renew
                            <span class="tooltip-text">Automatically renew this certificate</span>
                        </button>
                        <button class="alert-btn tooltip">Certificate Details
                            <span class="tooltip-text">View certificate information and configuration</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-warning">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Storage space running low <span class="alert-time">2 hours ago</span></div>
                    <div class="alert-message">Document storage volume is at 85% capacity. 23.4GB remaining of 156GB total allocation.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Add Storage
                            <span class="tooltip-text">Increase storage allocation for this service</span>
                        </button>
                        <button class="alert-btn tooltip">Analyze Usage
                            <span class="tooltip-text">View storage usage breakdown by folder</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-critical">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Backend service degradation <span class="alert-time">3 hours ago</span></div>
                    <div class="alert-message">Order processing microservice experiencing high latency. Avg response time: 3.2s (SLA: 800ms).</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Scale Service
                            <span class="tooltip-text">Add additional processing instances</span>
                        </button>
                        <button class="alert-btn tooltip">View Metrics
                            <span class="tooltip-text">See detailed service performance data</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-info">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">New API version available <span class="alert-time">5 hours ago</span></div>
                    <div class="alert-message">Inventory API v3.4.2 has been released with enhanced batch processing capability. Current version: 3.3.8</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">View Changes
                            <span class="tooltip-text">See new features and improvements</span>
                        </button>
                        <button class="alert-btn tooltip">Schedule Update
                            <span class="tooltip-text">Plan API version upgrade for your system</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-warning">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Scheduled maintenance upcoming <span class="alert-time">8 hours ago</span></div>
                    <div class="alert-message">Database indexing maintenance scheduled for June 15, 02:00-04:00 UTC. Expected read-only mode for 45 minutes.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Acknowledge
                            <span class="tooltip-text">Mark this maintenance as acknowledged</span>
                        </button>
                        <button class="alert-btn tooltip">Reschedule
                            <span class="tooltip-text">Request a different maintenance window</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-info">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Analytics pipeline optimization <span class="alert-time">1 day ago</span></div>
                    <div class="alert-message">Cost optimization available for ETL pipeline. Current: $342/mo. Potential savings: $112/mo with recommended adjustments.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Apply Recommendations
                            <span class="tooltip-text">Automatically apply cost-saving changes</span>
                        </button>
                        <button class="alert-btn tooltip">Review Details
                            <span class="tooltip-text">See detailed optimization suggestions</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-warning">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">User license threshold <span class="alert-time">1 day ago</span></div>
                    <div class="alert-message">Enterprise license at 91% utilization (455/500 seats). Additional licenses may be required soon.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Purchase Licenses
                            <span class="tooltip-text">Add additional user licenses</span>
                        </button>
                        <button class="alert-btn tooltip">Audit Usage
                            <span class="tooltip-text">Review user license allocation</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="alert alert-info">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">CRM integration available <span class="alert-time">2 days ago</span></div>
                    <div class="alert-message">New HubSpot integration module available for your instance. Enables bi-directional customer data sync.</div>
                    <div class="alert-actions">
                        <button class="alert-btn primary-action tooltip">Enable Integration
                            <span class="tooltip-text">Set up CRM data synchronization</span>
                        </button>
                        <button class="alert-btn tooltip">Learn More
                            <span class="tooltip-text">See integration capabilities and requirements</span>
                        </button>
                    </div>
                </div>
                <div class="alert-dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
            </div>

            <div class="no-alerts" style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <p>All clear! No notifications to display.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle alert dismissal
            const alertDismissButtons = document.querySelectorAll('.alert-dismiss');
            
            alertDismissButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert');
                    alert.classList.add('dismissed');
                    
                    // Update counters after dismissal
                    setTimeout(() => {
                        alert.remove();
                        updateCounters();
                        checkNoAlerts();
                    }, 300);
                });
            });
            
            // Handle filter buttons
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Set active state on the clicked button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filterType = this.getAttribute('data-filter');
                    const alerts = document.querySelectorAll('.alert');
                    
                    alerts.forEach(alert => {
                        if (filterType === 'all') {
                            alert.style.display = '';
                        } else {
                            if (alert.classList.contains(`alert-${filterType}`)) {
                                alert.style.display = '';
                            } else {
                                alert.style.display = 'none';
                            }
                        }
                    });
                    
                    checkNoAlerts();
                });
            });
            
            // Function to check if there are no visible alerts
            function checkNoAlerts() {
                const visibleAlerts = document.querySelectorAll('.alert:not([style*="display: none"])');
                const noAlertsMessage = document.querySelector('.no-alerts');
                
                if (visibleAlerts.length === 0) {
                    noAlertsMessage.style.display = 'block';
                } else {
                    noAlertsMessage.style.display = 'none';
                }
            }
            
            // Function to update counter badges
            function updateCounters() {
                const criticalAlerts = document.querySelectorAll('.alert-critical:not(.dismissed)').length;
                const warningAlerts = document.querySelectorAll('.alert-warning:not(.dismissed)').length;
                const infoAlerts = document.querySelectorAll('.alert-info:not(.dismissed)').length;
                
                document.querySelector('.critical-count').textContent = criticalAlerts;
                document.querySelector('.warning-count').textContent = warningAlerts;
                document.querySelector('.info-count').textContent = infoAlerts;
            }
            
            // Handle action buttons (prevent form submission)
            const actionButtons = document.querySelectorAll('.alert-btn');
            
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Simulate action with visual feedback
                    const originalText = this.textContent;
                    this.textContent