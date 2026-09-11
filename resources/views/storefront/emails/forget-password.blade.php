<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background-color: #fff;">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" bgcolor="#fff">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                <tr>
                    <td align="center" valign="top" style="padding: 36px 24px;">
                        <img src="{{ asset('storefront/assets/images/logo/logo.png') }}" alt="Logo" border="0" height="60" style="display: block;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="center" bgcolor="#fff">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 36px 24px 0; font-family: Helvetica, Arial, sans-serif; border-top: 3px solid #fff;">
                        <h1 style="margin: 0; font-size: 28px; font-weight: 500; text-align: center;">Reset Your Password</h1>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="center" bgcolor="#fff">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                        <p style="margin: 0;">We received a request to reset your password. Click the button below to choose a new one.</p>
                    </td>
                </tr>

                <tr>
                    <td align="center" bgcolor="#ffffff" style="padding: 12px;">
                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; padding: 14px 42px; font-family: Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px; background: #000;">Reset Your Password</a>
                    </td>
                </tr>

                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 22px; color: #666;">
                        <p style="margin: 0;">If that doesn't work, copy and paste this link into your browser:</p>
                        <p style="margin: 0;"><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
                        <p style="margin: 16px 0 0;">This link expires in 60 minutes. If you didn't request this, you can safely ignore this email.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="center" bgcolor="#fff" style="padding: 24px; font-family: Helvetica, Arial, sans-serif; font-size: 13px; color: #999;">
            {{ config('app.name') }} &copy; {{ date('Y') }}
        </td>
    </tr>
</table>

</body>
</html>