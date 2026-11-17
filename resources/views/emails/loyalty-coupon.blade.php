<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your reward code</title>
</head>
<body style="margin:0; padding:0; background-color:#f7f7f8; font-family: Arial, Helvetica, sans-serif; color:#1A1A1A;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7f7f8; padding:32px 0;">
        <tr>
            <td align="center">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:10px; overflow:hidden; max-width:600px; width:100%;">

                    <tr>
                        <td style="background-color:#1A1A1A; padding:28px 32px;">
                            <span style="color:#ffffff; font-size:20px; font-weight:bold;">{{ config('app.name', 'Restaurant') }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 32px 8px;">
                            <p style="margin:0 0 6px; font-size:22px; font-weight:bold; color:#1A1A1A;">Thank you for being a regular!</p>
                            <p style="margin:0; font-size:15px; color:#6b7280; line-height:1.5;">
                                You've ordered from us {{ $ordersRequired }} times, so here's <strong>{{ $coupon->discount_label }}</strong> your next order.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF5EE; border:2px dashed #D9A441; border-radius:10px;">
                                <tr>
                                    <td align="center" style="padding:22px 18px;">
                                        <span style="display:block; font-size:13px; color:#8B7F73;">Your code</span>
                                        <span style="display:block; margin-top:6px; font-size:30px; font-weight:bold; letter-spacing:3px; color:#1A1A1A;">{{ $coupon->code }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 32px 8px;">
                            <p style="margin:0 0 8px; font-size:14px; font-weight:bold; color:#1A1A1A;">How to use it</p>
                            <p style="margin:0; font-size:14px; color:#4b5563; line-height:1.6;">
                                Enter the code in the <strong>Discount code</strong> box at checkout, using this same email address
                                (<strong>{{ $coupon->email }}</strong>). It takes {{ $coupon->discount_label }} the food total; delivery isn't discounted.
                            </p>
                            <p style="margin:12px 0 0; font-size:14px; color:#4b5563; line-height:1.6;">
                                The code works <strong>once</strong> and is valid until <strong>{{ $coupon->expires_at->format('j F Y') }}</strong>.
                                After that, {{ $ordersRequired }} more orders earn you another one.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:24px 32px 32px;">
                            <a href="{{ route('storefront.menu.index') }}" style="display:inline-block; background-color:#1A1A1A; color:#ffffff; text-decoration:none; font-weight:bold; font-size:14px; padding:14px 32px; border-radius:8px;">
                                Order now
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px 32px; border-top:1px solid #EAE0D3;" align="center">
                            <p style="margin:0; font-size:12px; color:#8B7F73;">
                                {{ config('app.name', 'Restaurant') }} — questions? Just reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>