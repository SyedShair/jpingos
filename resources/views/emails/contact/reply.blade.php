<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re: {{ $contactMessage->subject }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f7f7f8; font-family: Arial, Helvetica, sans-serif; color:#1A1A1A;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7f7f8; padding: 32px 0;">
        <tr>
            <td align="center">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:10px; overflow:hidden; max-width:600px; width:100%;">

                    <tr>
                        <td style="background-color:#1A1A1A; padding:28px 32px;">
                            <span style="color:#ffffff; font-size:20px; font-weight:bold;">
                                {{ config('app.name', 'Restaurant') }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 32px 8px;">
                            <p style="margin:0 0 6px; font-size:20px; font-weight:bold; color:#1A1A1A;">
                                Hi {{ $contactMessage->name }},
                            </p>
                            <p style="margin:0; font-size:15px; color:#6b7280;">
                                Thanks for getting in touch — here's our reply.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 32px 24px;">
                            <p style="margin:0; font-size:15px; color:#1A1A1A; white-space:pre-line;">{{ $contactMessage->admin_reply }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7f7f8; border-radius:8px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 8px; font-size:12px; font-weight:bold; letter-spacing:.04em; color:#8B7F73;">
                                            YOUR ORIGINAL MESSAGE
                                        </p>
                                        <p style="margin:0 0 6px; font-size:13px; color:#8B7F73;">
                                            Subject: {{ $contactMessage->subject }}
                                        </p>
                                        <p style="margin:0; font-size:14px; color:#4b5563; white-space:pre-line;">{{ $contactMessage->message }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px 32px; border-top:1px solid #EAE0D3;" align="center">
                            <p style="margin:0; font-size:12px; color:#8B7F73;">
                                {{ config('app.name', 'Restaurant') }} — you can reply directly to this email.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>