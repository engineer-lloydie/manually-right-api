@extends('emails.templates.content')
@section('content')

<table role="presentation" cellpadding="0" cellspacing="0" style="max-width: 720px; width: 100%; margin: 0 auto; padding: 48px 40px 33px;">
    <tr>
        <td>
            <h2 style="font-size: 24px; font-weight: 600; margin: 0 0 26px; padding: 0; color: #000000;">Thank you for your order!</h2>
            <h3 style="font-size: 18px; font-weight: 600; color: #000; margin: 0 0 20px; padding: 0; line-height: 1.5em;">Hi {{ $data['first_name'] }},</h3>
            <p style="font-size: 14px; color: #000; margin: 0; padding: 0; line-height: 1.5em;">Order number is {{ $data['order_number'] }}.</p>
            <p style="font-size: 14px; color: #000; margin: 0; padding: 0; line-height: 1.5em;">You can check the status of your order on your account at any time.</p>
        </td>
    </tr>
</table>
<table style="width: 100%; max-width: 640px; margin: 0 auto;">
    <tr>
        <td style="border-bottom: 1px solid #DADADA;"></td>
    </tr>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="max-width: 720px; width: 100%; margin: 0 auto; padding: 32px 40px 20px;">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <h4 style="margin: 0; padding: 0 0 5px; color: #828282; font-size: 12px; font-weight: 600; text-transform: uppercase;">Order Date</h4>
            <p style="margin: 0; padding: 0; color: #000; font-size: 14px; font-weight: 500;">{{ $data['purchase_date'] }}</p>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <h4 style="margin: 0; padding: 0 0 5px; color: #828282; font-size: 12px; font-weight: 600; text-transform: uppercase;">Payment Method</h4>
            <p style="margin: 0; padding: 0; color: #000; font-size: 14px; font-weight: 500;">PayPal</p>
        </td>
    </tr>
</table>
<table style="width: 100%; max-width: 640px; margin: 0 auto;">
    <tr>
        <td style="border-bottom: 1px solid #DADADA;"></td>
    </tr>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto; padding:0 40px; max-width: 720px; width: 100%;">
    <tr style="color: #828282; font-size: 12px; font-weight: 600; background: #ffff; margin: 0;">
        <td style="padding: 13px 0; margin: 0; text-align: left; width: 130px;">THUMBNAIL</td>
        <td style="padding: 13px 0 13px 30px; margin: 0;">NAME</td>
        <td style="padding: 13px 0; margin: 0; text-align: right; width: 150px;">PRICE</td>
    </tr>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto; padding:0 40px; max-width: 720px; width: 100%;">
    <tr>
        <td style="border-bottom: 1px dashed #DADADA;"></td>
    </tr>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto; padding:0 40px; max-width: 720px; width: 100%;">
    @foreach ($data['items'] as $item)
        <tr>
            <td style="margin: 0; text-align: center; width: 130px; padding: 20px 0 24px;">
                <img src="{{ $item['thumbnail'] }}" style="width: 130px; object-fit: cover; display: inline-block; vertical-align: middle; margin: 0 2px;" />
            </td>
            <td style="padding: 24px 0 24px 30px; margin: 0; vertical-align: top;">
                <h4 style="font-size: 16px; font-weight: 600; color: #000; line-height: 1.3em; display: block; margin: 0 0 9px;">{{ $item['manual_name'] }}</h4>
                <span style="font-size: 14px; font-weight: 400; line-height: 17px; display: block; color: #757575; margin-bottom: 4px;">Quantity: {{ $item['quantity'] }}</span>
            </td>
            <td style="margin: 0; text-align: right; vertical-align: top; width: 150px; padding: 24px 0;">
                <span style="font-size: 16px; font-weight: 600; color: #000;">{{ '$'.$item['subtotal'] }}</span>
            </td>
        </tr>
    @endforeach
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="max-width: 720px; width: 100%; margin: 0 auto 10px; background: #F3F3F3; padding: 23px 40px 28px;">
<tr>
    <td>
        <div style="min-width: 450px; float: right; line-height: 1.5em; font-size: 14px;">
            <div style="border-top: 1px solid #A6A6A6; font-weight: 600; font-size: 18px; color: #000; padding: 15px 0 0; margin: 15px 0 0;">
                <div style="margin-bottom: 5px;">
                    <span style="font-size: ;">TOTAL</span>
                    <span style="float: right;">{{ '$'.number_format($data['total_price'], 2) }}</span>
                </div>
            </div>
        </div>
    </td>
</tr>
</table>
{{-- <table role="presentation" cellpadding="0" cellspacing="0" style="max-width: 720px; width: 100%; margin: 16px auto; text-align: center;">
  <tr>
    <td>
        <a href="{{ rtrim($details['site_url'], '/')."/login?redirect=mypage/orders&order={$details['order']->id}&invoice_no={$details['order']->invoice_no}&email={$details['order']->email}".(!$details['order']->user_id ? "&guest=1" : '').(isset($details['order']->code) ? "&code=".$details['order']->code : '') }}" href="javascript:;" style="background: #CB2842; color: #ffffff; font-size: 14px; font-weight: 700; display: block; padding: 10px 0; width: 122px; margin: 0; text-decoration: none; float: right; border-radius: 6px;">View Order</a>
    </td>
  </tr>
</table> --}}
<table style="width: 100%; max-width: 720px; margin: 0 auto;">
    <tr>
        <td style="border-bottom: 1px solid #DADADA;"></td>
    </tr>
</table>
</body>

</html>
@endsection