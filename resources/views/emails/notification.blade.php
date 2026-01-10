<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->app_name }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a0033 0%, #2d1b4e 100%); min-height: 100vh;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #1a0033 0%, #2d1b4e 100%); padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(168, 85, 247, 0.2); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);">

                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);">
                                {{ $notificationTitle ?? $settings->app_name }}
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255, 255, 255, 0.95); font-size: 16px;">
                                {{ $notificationSubtitle ?? 'Your Trusted Earnings Platform' }}
                            </p>
                        </td>
                    </tr>

                    <!-- Content Area -->
                    <tr>
                        <td style="padding: 40px 30px; color: #e2e8f0;">

                            <p style="margin: 0 0 20px 0; font-size: 18px; line-height: 1.6;">
                                Hi <strong style="color: #a855f7;">{{ $user->full_name }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.8; color: #cbd5e1;">
                                {!! nl2br($messageContent) !!}
                            </p>

                            @if(isset($actionUrl) && isset($actionButtonText))
                            <!-- Action Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $actionUrl }}"
                                           style="display: inline-block; background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);">
                                            {{ $actionButtonText }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin: 25px 0 0 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                                If you have any questions, our support team is always here to help.
                            </p>

                            <p style="margin: 25px 0 0 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                                Best regards,<br>
                                <strong style="color: #a855f7;">The {{ $settings->app_name }} Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: rgba(0, 0, 0, 0.3); padding: 25px 30px; text-align: center; border-top: 1px solid rgba(168, 85, 247, 0.2);">
                            <p style="margin: 0 0 10px 0; color: #94a3b8; font-size: 13px;">
                                © {{ date('Y') }} {{ $settings->app_name }}. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #64748b; font-size: 12px;">
                                Need help? Contact us at <a href="mailto:{{ $settings->support_email ?? 'support@crowdpower.ng' }}" style="color: #a855f7; text-decoration: none;">{{ $settings->support_email ?? 'support@crowdpower.ng' }}</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
