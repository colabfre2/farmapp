<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi {{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #22c55e;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header-logo {
            width: 50px;
            height: 50px;
            background: #22c55e;
            border-radius: 50%;
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .header-text { padding-left: 16px; }
        .header-text h1 { font-size: 20px; margin: 0; color: #1a1a1a; }
        .header-text p { font-size: 12px; color: #666; margin: 2px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f4f6f8; }
        .text-right { text-align: right; }
        .text-success { color: #22c55e; }
        .text-danger { color: #ef4444; }
    </style>
</head>
<body>

    <table style="border:none; margin-bottom:24px;">
        <tr style="border:none;">
            <td style="border:none; width:60px;">
                <img src="{{ public_path('images/logo.png') }}" style="width:50px;height:50px;">
            </td>
            <td style="border:none;">
                <h1 style="margin:0; font-size:20px;">FarmApp</h1>
                <p style="margin:2px 0 0; color:#666;">Laporan Laba Rugi — Tahun {{ $year }}</p>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width:33%; text-align:center;">
                <div style="font-size:11px;color:#666;">Total Pemasukan</div>
                <div style="font-size:16px;font-weight:bold;color:#22c55e;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </td>
            <td style="width:33%; text-align:center;">
                <div style="font-size:11px;color:#666;">Total Pengeluaran</div>
                <div style="font-size:16px;font-weight:bold;color:#ef4444;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </td>
            <td style="width:33%; text-align:center;">
                <div style="font-size:11px;color:#666;">Laba Bersih</div>
                <div style="font-size:16px;font-weight:bold;color:{{ $netProfit >= 0 ? '#22c55e' : '#ef4444' }};">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Laba/Rugi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($months as $month)
            <tr>
                <td>{{ $month['month'] }}</td>
                <td class="text-right text-success">Rp {{ number_format($month['income'], 0, ',', '.') }}</td>
                <td class="text-right text-danger">Rp {{ number_format($month['expense'], 0, ',', '.') }}</td>
                <td class="text-right {{ $month['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($month['profit'], 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#f4f6f8;">
                <td>Total</td>
                <td class="text-right text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td class="text-right text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                <td class="text-right {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align:center; color:#999; font-size:10px; margin-top:32px;">
        Dicetak pada {{ now()->format('d M Y H:i') }} WIB
    </p>
</body>
</html>