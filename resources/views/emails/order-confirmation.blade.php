<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #f43f5e 0%, #a855f7 100%); border-radius: 16px 16px 0 0; padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">🛍️ {{ config('app.name') }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">Terima kasih atas pesanan Anda!</p>
        </div>

        <!-- Main Content -->
        <div style="background: white; padding: 30px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <!-- Status Icon & Title -->
            <div style="text-align: center; margin-bottom: 25px;">
                @if($order->payment_status == 'paid')
                <div style="display: inline-block; background: #10b981; border-radius: 50%; padding: 15px;">
                    <span style="font-size: 30px; color: white;">✓</span>
                </div>
                <h2 style="color: #1f2937; margin: 15px 0 5px 0;">Pembayaran Berhasil!</h2>
                <p style="color: #6b7280; margin: 0;">Pesanan Anda sedang diproses dan akan segera dikirim.</p>
                @elseif($order->payment_status == 'pending')
                <div style="display: inline-block; background: #f59e0b; border-radius: 50%; padding: 15px;">
                    <span style="font-size: 30px; color: white;">!</span>
                </div>
                <h2 style="color: #1f2937; margin: 15px 0 5px 0;">Menunggu Pembayaran</h2>
                <p style="color: #6b7280; margin: 0;">Silakan selesaikan pembayaran agar pesanan dapat diproses.</p>
                @else
                <div style="display: inline-block; background: #ef4444; border-radius: 50%; padding: 15px;">
                    <span style="font-size: 30px; color: white;">✕</span>
                </div>
                <h2 style="color: #1f2937; margin: 15px 0 5px 0;">Pesanan Dibatalkan</h2>
                @endif
            </div>

            <!-- Payment Instructions (Only for Pending Bank Transfer/E-Wallet) -->
            @if($order->payment_status == 'pending' && in_array($order->payment_method, ['bank_transfer', 'e_wallet']))
            <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 12px; padding: 20px; margin-bottom: 25px; text-align: left;">
                <h3 style="color: #92400e; margin-top: 0; margin-bottom: 15px; font-size: 18px;">💳 Instruksi Pembayaran</h3>
                <p style="color: #b45309; margin-bottom: 15px; font-size: 14px;">Silakan transfer sejumlah <strong style="font-size: 16px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong> ke salah satu rekening berikut:</p>
                
                @if($order->payment_method == 'bank_transfer')
                <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #fde68a; margin-bottom: 10px;">
                    <p style="margin: 0; color: #78350f; font-size: 12px; font-weight: bold; text-transform: uppercase;">Bank BCA</p>
                    <p style="margin: 5px 0 0 0; color: #1f2937; font-size: 18px; font-family: monospace; font-weight: bold;">2190336380</p>
                    <p style="margin: 5px 0 0 0; color: #4b5563; font-size: 13px;">a.n. MUHAMMAD AFANSYAH</p>
                </div>
                <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #fde68a;">
                    <p style="margin: 0; color: #78350f; font-size: 12px; font-weight: bold; text-transform: uppercase;">Bank Mandiri</p>
                    <p style="margin: 5px 0 0 0; color: #1f2937; font-size: 18px; font-family: monospace; font-weight: bold;">700011875460</p>
                    <p style="margin: 5px 0 0 0; color: #4b5563; font-size: 13px;">a.n. NAILA AZ ZAHRA</p>
                </div>
                @elseif($order->payment_method == 'e_wallet')
                <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #fde68a;">
                    <p style="margin: 0; color: #78350f; font-size: 12px; font-weight: bold; text-transform: uppercase;">GoPay / OVO / DANA</p>
                    <p style="margin: 5px 0 0 0; color: #1f2937; font-size: 18px; font-family: monospace; font-weight: bold;">0896 0190 5406</p>
                    <p style="margin: 5px 0 0 0; color: #4b5563; font-size: 13px;">a.n. MUHAMMAD AFANSYAH</p>
                </div>
                @endif

                <p style="color: #b45309; margin-top: 15px; font-size: 13px; margin-bottom: 0;">
                    Setelah transfer, harap konfirmasi bukti pembayaran ke WhatsApp Admin: 
                    <a href="https://wa.me/6289601905406?text=Konfirmasi%20Pesanan%20%23{{ $order->id }}" style="color: #059669; font-weight: bold; text-decoration: underline;">0896 0190 5406</a>
                </p>
            </div>
            @endif

            <!-- Order Info -->
            <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Nomor Pesanan</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #1f2937;">#{{ $order->id }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Tanggal</td>
                        <td style="padding: 8px 0; text-align: right; color: #1f2937;">{{ $order->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Metode Pembayaran</td>
                        <td style="padding: 8px 0; text-align: right; color: #1f2937;">{{ __('order.payment_method.' . $order->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Status</td>
                        <td style="padding: 8px 0; text-align: right;">
                            <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                {{ __('order.payment_status.' . $order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Order Items -->
            <h3 style="color: #1f2937; margin-bottom: 15px; font-size: 16px;">📦 Detail Pesanan</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="padding: 12px; text-align: left; color: #64748b; font-weight: 600; font-size: 13px;">Produk</th>
                        <th style="padding: 12px; text-align: center; color: #64748b; font-weight: 600; font-size: 13px;">Qty</th>
                        <th style="padding: 12px; text-align: right; color: #64748b; font-weight: 600; font-size: 13px;">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px; color: #1f2937;">{{ $item->product->name ?? 'Produk' }}</td>
                        <td style="padding: 12px; text-align: center; color: #64748b;">{{ $item->quantity }}</td>
                        <td style="padding: 12px; text-align: right; color: #1f2937;">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total -->
            <div style="background: linear-gradient(135deg, #fdf2f8 0%, #faf5ff 100%); border-radius: 12px; padding: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280;">Subtotal</td>
                        <td style="padding: 5px 0; text-align: right; color: #1f2937;">Rp {{ number_format($order->grand_total - $order->shipping_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280;">Ongkos Kirim</td>
                        <td style="padding: 5px 0; text-align: right; color: #1f2937;">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0 0 0; font-weight: 700; font-size: 18px; color: #1f2937;">Total</td>
                        <td style="padding: 10px 0 0 0; text-align: right; font-weight: 700; font-size: 18px; background: linear-gradient(135deg, #f43f5e 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Customer Info -->
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <h3 style="color: #1f2937; margin-bottom: 10px; font-size: 16px;">📍 Informasi Pengiriman</h3>
                <p style="color: #64748b; margin: 0; line-height: 1.6;">
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->customer_email }}<br>
                    @if($order->customer_phone){{ $order->customer_phone }}<br>@endif
                    @php
                        // Parse address from notes field (format: "Name: ... | Email: ... | Phone: ... | Address: ...")
                        $address = '';
                        if ($order->notes && preg_match('/Address:\s*([^|]+)/', $order->notes, $matches)) {
                            $address = trim($matches[1]);
                        }
                    @endphp
                    @if($address)
                    <br><strong>Alamat:</strong><br>
                    {{ $address }}
                    @endif
                </p>
            </div>

            <!-- Footer -->
            <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <p style="color: #6b7280; font-size: 14px; margin: 0;">
                    Ada pertanyaan? Hubungi kami di<br>
                    <a href="mailto:{{ config('mail.from.address') }}" style="color: #f43f5e; text-decoration: none;">{{ config('mail.from.address') }}</a>
                </p>
                <p style="color: #9ca3af; font-size: 12px; margin-top: 15px;">
                    © {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
