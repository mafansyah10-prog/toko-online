<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $order->id }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            margin: 0 auto; /* Center on page */
            padding: 4mm 0; /* Vertical padding only */
            width: 72mm; /* Safe print width for 80mm paper to avoid cutting off */
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            text-align: left;
            border-bottom: 1px solid #000;
            font-size: 10px;
            padding: 2px 0;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
            word-wrap: break-word; /* Ensure long words don't overflow */
        }
        .qty {
            width: 10%;
            text-align: center;
            white-space: nowrap;
        }
        .item {
            width: 55%;
        }
        .price {
            width: 35%;
            text-align: right;
            white-space: nowrap;
        }
        .totals {
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .totals div {
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
            font-size: 9px;
            color: #000;
        }
        @media print {
            body {
                margin: 0 auto; /* Keep centering in print */
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>Toko Online</h1>
        <p>Jl. Contoh No. 123, Jakarta</p>
        <p>Telp: 021-555-0123</p>
    </div>

    <div class="meta">
        <div><span>No. Transaksi:</span> <span>#{{ $order->id }}</span></div>
        <div><span>Tanggal:</span> <span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
        <div><span>Kasir:</span> <span>{{ auth()->user()->name ?? 'Admin' }}</span></div>
        @php
            $paymentMethods = [
                'cash' => 'Tunai',
                'qris' => 'QRIS',
                'transfer' => 'Transfer',
                'debit' => 'Debit'
            ];
        @endphp
        <div><span>Metode Bayar:</span> <span>{{ $paymentMethods[$order->payment_method] ?? ucfirst($order->payment_method) }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="item">Item</th>
                <th class="qty">Qty</th>
                <th class="price">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="item">
                    {{ $item->product->name ?? 'Produk Dihapus' }}
                    <br>
                    <small>@ Rp {{ number_format($item->unit_amount, 0, ',', '.') }}</small>
                </td>
                <td class="qty">{{ $item->quantity }}</td>
                <td class="price">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>
            <span>Subtotal</span>
            <span>Rp {{ number_format($order->items->sum('total_amount'), 0, ',', '.') }}</span>
        </div>
        @if($order->discount_amount > 0)
        <div>
            <span>Diskon {{ $order->voucher_code ? '('.$order->voucher_code.')' : '' }}</span>
            <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="grand-total">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
        </div>
        @if($order->payment_method === 'cash')
        <div style="margin-top: 5px;">
            <span>Tunai</span>
            <span>Rp {{ number_format($order->received_amount, 0, ',', '.') }}</span>
        </div>
        <div>
            <span>Kembali</span>
            <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($order->payment_method === 'cash' && request('received_amount'))
             <!-- Optional: Show Change if passed via query param, though usually not stored in DB unless we added it -->
             <!-- Since we don't store changed amount in DB easily (unless we add it), we might skip it or pass it as a query param to the print route -->
        @endif
    </div>

    <div class="footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    </div>
</body>
</html>
