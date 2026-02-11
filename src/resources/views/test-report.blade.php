<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Automated Test Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }
        
        .header {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 3px solid #000;
            position: relative;
            min-height: 80px;
        }
        .logo-left {
            position: absolute;
            top: 0;
            left: 0;
        }
        .logo-left img {
            width: 100px;
            height: auto;
            display: block;
        }
        .title-center {
            text-align: center;
            padding-top: 10px;
        }
        .title-center h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header-meta {
            margin-top: 5px;
            font-size: 10px;
            color: #666;
        }
        .header-meta span {
            display: block;
            margin: 3px 0;
        }
        
        .summary {
            background-color: #ecf0f1;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
            border-left: 5px solid #3498db;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            padding: 5px;
            text-align: center;
        }
        .summary-item strong {
            display: block;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
        }
        .value-passed { color: #27ae60; }
        .value-failed { color: #e74c3c; }
        .value-total { color: #3498db; }
        
        .category-header {
            background-color: #3498db;
            color: white;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-passed { color: #27ae60; font-weight: bold; }
        .status-failed { color: #e74c3c; font-weight: bold; }
        .status-risky  { color: #e67e22; font-weight: bold; }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        
        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title-center">
            <h1>AUTOMATED TESTING REPORT</h1>
            <div class="header-meta">
                <span>Generated on: {{ $date ?? now()->format('d/m/Y H:i:s') }}</span>
                <span>Total Duration: {{ $duration ?? '-' }}</span>
            </div>
        </div>
    </div>
    
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Total Tests</strong>
                <div class="summary-value value-total">{{ $total_tests ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <strong>Passed</strong>
                <div class="summary-value value-passed">{{ $passed ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <strong>Failed</strong>
                <div class="summary-value value-failed">{{ $failed ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <strong>Risky</strong>
                <div class="summary-value status-risky">{{ $risky ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <strong>Success Rate</strong>
                @php
                    $total = $total_tests ?? 0;
                    $pass  = $passed ?? 0;
                    $rate  = $total > 0 ? round(($pass / $total) * 100, 1) : 0;
                @endphp
                <div class="summary-value">{{ $rate }}%</div>
            </div>
        </div>
    </div>
    
    <div class="category-summary-section">
        <div class="category-header">Summary by Category</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Category / Class</th>
                    <th style="width: 15%;">Total</th>
                    <th style="width: 15%;">Pass</th>
                    <th style="width: 15%;">Fail</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $summaryNo = 1; @endphp
                @foreach($category_summary ?? [] as $category => $stats)
                <tr>
                    <td>{{ $summaryNo++ }}</td>
                    <td>{{ $category }}</td>
                    <td>{{ $stats['total'] }}</td>
                    <td class="status-passed">{{ $stats['passed'] }}</td>
                    <td class="{{ $stats['failed'] > 0 ? 'status-failed' : 'status-passed' }}">{{ $stats['failed'] }}</td>
                    <td class="{{ $stats['failed'] > 0 ? 'status-failed' : 'status-passed' }}">
                        {{ $stats['failed'] > 0 ? 'FAILED' : 'PASSED' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    @foreach($results ?? collect([]) as $category => $tests)
        <div class="category-header">
            Detail: {{ $category }} ({{ count($tests) }} tests)
        </div>
        <table>
            </table>

        {{-- Hanya berikan page-break jika BUKAN tabel terakhir --}}
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    {{-- Footer diletakkan langsung di bawah tabel terakhir --}}
    <div class="footer">
        <p><strong>Universal Automated Testing Report</strong></p>
        <p>This document is an automated output of the system test suite.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Generated via Nelson Export Package</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("helvetica", "normal");
            $size = 9;
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $pdf->page_text(500, 815, $pageText, $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>
</body>
</html>