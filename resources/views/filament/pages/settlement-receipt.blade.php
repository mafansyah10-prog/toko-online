<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Setelmen #{{ $settlement->id }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            margin: 0 auto;
            padding: 4mm 0;
            width: 72mm;
            background-color: #fff;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        .meta {
            margin-bottom: 10px;
            font-size: 10px;
        }
        .meta div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 9px;
            color: #000;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>LAPORAN SETELMEN</h1>
        <p>Toko Online</p>
        <p>Laporan Penutupan Kasir</p>
    </div>

    <div class="meta">
        <div><span>ID Setelmen:</span> <span>#{{ $settlement->id }}</span></div>
        <div><span>Tanggal Buka:</span> <span>{{ $settlement->opened_at->format('d/m/Y H:i') }}</span></div>
        <div><span>Tanggal Tutup:</span> <span>{{ $settlement->closed_at->format('d/m/Y H:i') }}</span></div>
        <div><span>Kasir:</span> <span>{{ $settlement->user->name }}</span></div>
    </div>

    <div class="section-title">Ringkasan Penjualan</div>
    <div class="row">
        <span>Tunai (Kas)</span>
        <span>Rp {{ number_format($settlement->cash_sales, 0, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>QRIS</span>
        <span>Rp {{ number_format($settlement->qris_sales, 0, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Transfer</span>
        <span>Rp {{ number_format($settlement->transfer_sales, 0, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Debit</span>
        <span>Rp {{ number_format($settlement->debit_sales, 0, ',', '.') }}</span>
    </div>
    
    <div class="grand-total">
        <div class="row">
            <span>TOTAL PENJUALAN</span>
            <span>Rp {{ number_format($settlement->total_sales, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="section-title">Audit Kas</div>
    <div class="row">
        <span>Kas Seharusnya</span>
        <span>Rp {{ number_format($settlement->cash_sales, 0, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Kas Fisik</span>
        <span>Rp {{ number_format($settlement->actual_cash_amount, 0, ',', '.') }}</span>
    </div>
    @php $diff = $settlement->actual_cash_amount - $settlement->cash_sales; @endphp
    <div class="row" style="font-weight: bold;">
        <span>Selisih</span>
        <span>Rp {{ number_format($diff, 0, ',', '.') }}</span>
    </div>

    @if($settlement->notes)
    <div class="section-title">Catatan</div>
    <p style="margin: 0; font-size: 10px;">{{ $settlement->notes }}</p>
    @endif

    <div class="footer">
        <p>Laporan dicetak pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Tanda Tangan Kasir,</p>
        <br><br><br>
        <p>({{ $settlement->user->name }})</p>
    </div>
</body>
</html>
