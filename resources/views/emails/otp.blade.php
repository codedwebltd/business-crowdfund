<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code - {{ $settings->app_name }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 8px 32px 0 rgba(124, 58, 237, 0.2);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); padding: 30px; text-align: center; border-radius: 16px 16px 0 0;">
                            <h1 style="margin: 0; color: white; font-size: 24px; font-weight: bold;">
                                🔐 Verification Code
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px; color: rgba(255, 255, 255, 0.8); font-size: 16px;">
                                Hello, <strong style="color: #ffffff;">{{ $user->full_name }}</strong>
                            </p>

                            <p style="margin: 0 0 30px; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.6;">
                                Your verification code for {{ $settings->app_name }} is:
                            </p>

                            <!-- OTP Code Box -->
                            <div style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.3) 0%, rgba(168, 85, 247, 0.3) 100%); border-radius: 12px; border: 2px solid rgba(168, 85, 247, 0.5); padding: 30px; text-align: center; margin-bottom: 30px;">
                                <div style="font-size: 48px; font-weight: bold; color: #ffffff; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                                    {{ $otp }}
                                </div>
                            </div>

                            <div style="background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 13px;">
                                    <strong style="color: #f59e0b;">⚠️ Security Notice:</strong> This code expires in <strong>10 minutes</strong>. Never share it with anyone!
                                </p>
                            </div>

                            <p style="margin: 0; color: rgba(255, 255, 255, 0.6); font-size: 13px; line-height: 1.6;">
                                If you didn't request this code, please ignore this email or contact our support team immediately.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: rgba(0, 0, 0, 0.2); padding: 20px; text-align: center; border-radius: 0 0 16px 16px;">
                            <p style="margin: 0; color: rgba(255, 255, 255, 0.5); font-size: 12px;">
                                © {{ now()->year }} {{ $settings->app_name }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
