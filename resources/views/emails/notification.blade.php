<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->app_name }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
    <!-- Email Container -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Content Card -->
                <table width="600" cellpadding="0" cellspacing="0" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 8px 32px 0 rgba(124, 58, 237, 0.2);">

                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); padding: 40px 30px; text-align: center; border-radius: 16px 16px 0 0;">
                            <h1 style="margin: 0; color: white; font-size: 28px; font-weight: bold; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                {{ $settings->app_name }}
                            </h1>
                            <p style="margin: 10px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 14px;">
                                Your Trusted Earnings Platform
                            </p>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="padding: 40px 30px 20px;">
                            <h2 style="margin: 0 0 20px; color: #ffffff; font-size: 22px;">
                                Hello, {{ $user->full_name }}! 👋
                            </h2>
                        </td>
                    </tr>

                    <!-- Message Content Box -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.15) 0%, rgba(168, 85, 247, 0.15) 100%); border-radius: 12px; border: 1px solid rgba(168, 85, 247, 0.3);">
                                <tr>
                                    <td style="padding: 30px;">
                                        <div style="color: rgba(255, 255, 255, 0.95); font-size: 16px; line-height: 1.8;">
                                            {!! nl2br(e($messageContent)) !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Action Button (if applicable) -->
                    <tr>
                        <td style="padding: 0 30px 40px;" align="center">
                            <a href="{{ $settings->app_url ?? 'https://crowdpower.ng' }}/user/dashboard"
                               style="display: inline-block; background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: white; text-decoration: none; padding: 14px 35px; border-radius: 10px; font-size: 15px; font-weight: bold; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);">
                                🚀 View Dashboard
                            </a>
                        </td>
                    </tr>

                    <!-- Support Section -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: rgba(59, 130, 246, 0.1); border-radius: 12px; border: 1px solid rgba(59, 130, 246, 0.3);">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px; text-align: center; line-height: 1.6;">
                                            💬 Need help? Contact us at
                                            <a href="mailto:{{ $settings->support_email ?? 'support@crowdpower.ng' }}"
                                               style="color: #60a5fa; text-decoration: none; font-weight: bold;">
                                                {{ $settings->support_email ?? 'support@crowdpower.ng' }}
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: rgba(0, 0, 0, 0.2); padding: 30px; text-align: center; border-radius: 0 0 16px 16px;">
                            <p style="margin: 0 0 5px; color: rgba(255, 255, 255, 0.5); font-size: 12px;">
                                © {{ now()->year }} {{ $settings->app_name }}. All rights reserved.
                            </p>
                            <p style="margin: 5px 0 0; color: rgba(255, 255, 255, 0.4); font-size: 11px;">
                                This is an automated message, please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
