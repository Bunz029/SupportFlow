<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($reportData['period']) }} SLA Performance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            background-color: #4338ca;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .stats-table th, .stats-table td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }
        .stats-table th {
            background-color: #e5e7eb;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        .metric-card {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .metric-title {
            font-size: 14px;
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        .metric-description {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .warning {
            color: #f59e0b;
        }
        .danger {
            color: #ef4444;
        }
        .success {
            color: #10b981;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($reportData['period']) }} SLA Performance Report</h1>
        <p>{{ $reportData['startDate']->format('M d, Y') }} - {{ $reportData['endDate']->format('M d, Y') }}</p>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        
        <p>This is an automated summary of SLA performance for the {{ $reportData['period'] }} period. Below are the key metrics:</p>
        
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-title">Total Tickets</div>
                <div class="metric-value">{{ $reportData['stats']['totalTickets'] }}</div>
                <div class="metric-description">Total tickets created in this period</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-title">SLA Coverage</div>
                <div class="metric-value">{{ $reportData['stats']['totalWithSla'] }}</div>
                <div class="metric-description">
                    {{ $reportData['stats']['totalTickets'] > 0 
                        ? round(($reportData['stats']['totalWithSla'] / $reportData['stats']['totalTickets']) * 100, 2) 
                        : 0 }}% of tickets
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-title">Response SLA Breaches</div>
                <div class="metric-value {{ $reportData['stats']['responseBreachRate'] > 10 ? 'danger' : 'success' }}">
                    {{ $reportData['stats']['responseBreaches'] }}
                </div>
                <div class="metric-description">
                    {{ $reportData['stats']['responseBreachRate'] }}% breach rate
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-title">Resolution SLA Breaches</div>
                <div class="metric-value {{ $reportData['stats']['resolutionBreachRate'] > 10 ? 'danger' : 'success' }}">
                    {{ $reportData['stats']['resolutionBreaches'] }}
                </div>
                <div class="metric-description">
                    {{ $reportData['stats']['resolutionBreachRate'] }}% breach rate
                </div>
            </div>
        </div>
        
        <p>For more detailed information, please view the full <a href="{{ route('reports.sla-performance') }}">SLA Performance Report</a> on the SupportFlow platform.</p>
        
        <p>Thank you for your attention to service quality.</p>
        
        <p>Best regards,<br>SupportFlow System</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} SupportFlow. All rights reserved.</p>
    </div>
</body>
</html> 