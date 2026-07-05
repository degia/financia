<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Financial Report</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 20px 10px; }
        .header { background: linear-gradient(135deg, #1e293b, #334155); border-radius: 12px; padding: 24px; text-align: center; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p { margin: 6px 0 0; font-size: 14px; opacity: .8; }
        .summary-row { display: flex; gap: 10px; margin-top: 16px; }
        .summary-card { flex: 1; background: #fff; border-radius: 10px; padding: 14px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .summary-card .label { font-size: 11px; text-transform: uppercase; color: #6b7280; letter-spacing: .5px; }
        .summary-card .value { font-size: 18px; font-weight: 700; margin-top: 4px; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-gray { color: #6b7280; }
        .section { background: #fff; border-radius: 10px; padding: 16px; margin-top: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .section h2 { font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 10px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; }
        .section table { width: 100%; border-collapse: collapse; }
        .section td { padding: 6px 0; font-size: 13px; }
        .section td:last-child { text-align: right; font-weight: 600; }
        .section tr + tr td { border-top: 1px solid #f3f4f6; }
        .account-item { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; border-bottom: 1px solid #f3f4f6; }
        .account-item:last-child { border-bottom: none; }
        .account-name { color: #374151; }
        .account-balance { font-weight: 600; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #9ca3af; }
        .footer a { color: #6366f1; text-decoration: none; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fef2f2; color: #dc2626; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }
        @media only screen and (max-width: 480px) {
            .summary-row { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Daily Financial Report</h1>
            <p>{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
        </div>

        <div class="summary-row">
            <div class="summary-card">
                <div class="label">Income</div>
                <div class="value text-green">{{ $symbol }}{{ number_format($data['income'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Expense</div>
                <div class="value text-red">{{ $symbol }}{{ number_format($data['expense'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Net</div>
                <div class="value {{ $data['net'] >= 0 ? 'text-green' : 'text-red' }}">
                    {{ $data['net'] >= 0 ? '+' : '' }}{{ $symbol }}{{ number_format($data['net'], 0, ',', '.') }}
                </div>
            </div>
        </div>

        @if (!empty($data['categories']))
            <div class="section">
                <h2>Category Breakdown</h2>
                <table>
                    @foreach ($data['categories'] as $cat)
                        <tr>
                            <td>{{ $cat['name'] }}</td>
                            <td class="{{ $cat['type'] === 'income' ? 'text-green' : 'text-red' }}">
                                {{ $cat['type'] === 'income' ? '+' : '-' }}{{ $symbol }}{{ number_format($cat['total'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if (!empty($data['accounts']))
            <div class="section">
                <h2>Account Balances</h2>
                @foreach ($data['accounts'] as $acct)
                    <div class="account-item">
                        <span class="account-name">{{ $acct['name'] }}</span>
                        <span class="account-balance">{{ $symbol }}{{ number_format($acct['balance'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if (!empty($data['transactions']))
            <div class="section">
                <h2>Recent Transactions</h2>
                <table>
                    @foreach ($data['transactions'] as $t)
                        <tr>
                            <td>
                                <div>{{ $t['description'] ?: 'No description' }}</div>
                                <div style="font-size:11px;color:#9ca3af;">{{ $t['category'] }} &middot; {{ $t['account'] }}</div>
                            </td>
                            <td class="{{ $t['type'] === 'income' ? 'text-green' : 'text-red' }}">
                                {{ $t['type'] === 'income' ? '+' : '-' }}{{ $symbol }}{{ number_format($t['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if (!empty($data['budgets']))
            <div class="section">
                <h2>Budget Progress ({{ $data['budgets_month'] }})</h2>
                <table>
                    @foreach ($data['budgets'] as $b)
                        <tr>
                            <td>{{ $b['category'] }}</td>
                            <td>
                                <span class="badge {{ $b['percentage'] > 100 ? 'badge-red' : ($b['percentage'] > 80 ? 'badge-gray' : 'badge-green') }}">
                                    {{ $b['spent'] > 0 ? $b['percentage'] . '%' : '0%' }}
                                </span>
                            </td>
                            <td class="text-gray">{{ $symbol }}{{ number_format($b['spent'], 0, ',', '.') }} / {{ $symbol }}{{ number_format($b['budgeted'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <div class="footer">
            <p>Sent automatically by <strong>Financia</strong></p>
            <p style="margin-top:4px;">
                <a href="{{ route('dashboard') }}">View Dashboard</a> &middot;
                <a href="{{ route('settings.index') }}">Report Settings</a>
            </p>
        </div>
    </div>
</body>
</html>
