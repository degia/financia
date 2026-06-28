<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; font-size: 20px; margin-bottom: 5px; }
        .subtitle { text-align: center; font-size: 13px; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .summary { margin-top: 15px; }
        .summary table { width: auto; }
        .summary td { padding: 5px 20px 5px 0; border: none; }
        .summary .label { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Financial Report</h1>
    <p class="subtitle">{{ $filters['start_date'] }} — {{ $filters['end_date'] }}</p>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Income</td>
                <td class="text-green">{{ number_format($data['totalIncome'], 2) }}</td>
                <td class="label">Total Expense</td>
                <td class="text-red">{{ number_format($data['totalExpense'], 2) }}</td>
                <td class="label">Net</td>
                <td class="{{ ($data['totalIncome'] - $data['totalExpense']) >= 0 ? 'text-green' : 'text-red' }}">
                    {{ ($data['totalIncome'] - $data['totalExpense']) >= 0 ? '+' : '' }}{{ number_format($data['totalIncome'] - $data['totalExpense'], 2) }}
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th>Account</th>
                <th>Type</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['transactions'] as $t)
                <tr>
                    <td>{{ $t->date->format('Y-m-d') }}</td>
                    <td>{{ $t->description ?: '-' }}</td>
                    <td>{{ $t->category->name }}</td>
                    <td>{{ $t->account->name }}</td>
                    <td>{{ ucfirst($t->type) }}</td>
                    <td class="text-right {{ $t->type === 'income' ? 'text-green' : 'text-red' }}">
                        {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
