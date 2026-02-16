<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Update Status Pesanan</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9fafb; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #f43f5e 0%, #7c3aed 100%); padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 24px; font-weight: 700;">Status Pesanan Diperbarui</h1>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <p style="font-size: 16px; margin-bottom: 20px;">Halo <strong>{{ $order->customer_name }}</strong>,</p>
            
            <p style="font-size: 16px; margin-bottom: 30px;">
                Pesanan Anda <strong>#{{ $order->id }}</strong> telah diperbarui. Saat ini status pesanan Anda adalah:
            </p>

            <!-- Status Badge -->
            <div style="text-align: center; margin-bottom: 35px;">
                <span style="display: inline-block; padding: 12px 24px; border-radius: 50px; font-size: 18px; font-weight: bold; background-color: 
                    @if($order->status == 'new') #dbeafe; color: #1e40af;
                    @elseif($order->status == 'processing') #fef9c3; color: #854d0e;
                    @elseif($order->status == 'shipped') #f3e8ff; color: #6b21a8;
                    @elseif($order->status == 'delivered') #dcfce7; color: #166534;
                    @elseif($order->status == 'cancelled') #fee2e2; color: #991b1b;
                    @else #f3f4f6; color: #374151; @endif">
                    {{ __('order.status.' . $order->status) }}
                </span>
            </div>

            <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
                @if($order->status == 'processing')
                    Kami sedang memproses pesanan Anda dan akan segera dikirim.
                @elseif($order->status == 'shipped')
                    Pesanan Anda sedang dalam perjalanan.
                @elseif($order->status == 'delivered')
                    Pesanan telah sampai. Terima kasih telah berbelanja di {{ config('app.name') }}!
                @endif
            </p>

            <div style="border-top: 1px solid #e5e7eb; margin: 30px 0;"></div>

            <!-- Order Details -->
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 15px;">Detail Pesanan</h3>
                <p style="margin: 5px 0;"><strong>Nomor Order:</strong> #{{ $order->id }}</p>
                <p style="margin: 5px 0;"><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y, H:i:s') }}</p>
                <p style="margin: 5px 0;"><strong>Total Pembayaran:</strong> Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
            </div>

            <!-- CTA -->
            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('orders.track', ['email' => $order->customer_email]) }}" style="display: inline-block; padding: 12px 30px; background-color: #111827; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">Cek Status Pesanan</a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 5px;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="margin: 0;">Butuh bantuan? Hubungi WhatsApp kami di <a href="https://wa.me/6289601905406" style="color: #f43f5e; text-decoration: none;">0896 0190 5406</a></p>
        </div>
    </div>
</body>
</html>
