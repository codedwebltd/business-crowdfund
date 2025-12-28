<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a0033 0%, #2d1b4e 100%); min-height: 100vh;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #1a0033 0%, #2d1b4e 100%); padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);">

                    <!-- Header with Red/Orange Warning Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #f97316 100%); padding: 40px 30px; text-align: center;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 48px;">⚠️</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);">
                                {{ $title }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Content Area -->
                    <tr>
                        <td style="padding: 40px 30px; color: #e2e8f0;">

                            <p style="margin: 0 0 20px 0; font-size: 18px; line-height: 1.6;">
                                Hi <strong style="color: #f97316;">{{ $user->full_name }}</strong>,
                            </p>

                            <p style="margin: 0 0 30px 0; font-size: 16px; line-height: 1.8; color: #cbd5e1;">
                                {{ $messageContent ?? $message }}
                            </p>

                            <!-- Detection Info Box -->
                            @if(isset($vpn_data))
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px; margin: 30px 0; padding: 25px;">
                                <tr>
                                    <td>
                                        <h3 style="margin: 0 0 15px 0; color: #f97316; font-size: 18px;">🔍 Detection Details</h3>
                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            @if(isset($vpn_data['is_vpn']) && $vpn_data['is_vpn'])
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px; width: 40%;">Detection Type:</td>
                                                <td style="color: #fca5a5; font-size: 14px; font-weight: 600;">VPN Detected</td>
                                            </tr>
                                            @endif
                                            @if(isset($vpn_data['is_proxy']) && $vpn_data['is_proxy'])
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Detection Type:</td>
                                                <td style="color: #fca5a5; font-size: 14px; font-weight: 600;">Proxy Detected</td>
                                            </tr>
                                            @endif
                                            @if(isset($vpn_data['isp']))
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">ISP:</td>
                                                <td style="color: #e2e8f0; font-size: 14px; font-weight: 600;">{{ $vpn_data['isp'] }}</td>
                                            </tr>
                                            @endif
                                            @if(isset($vpn_data['threat_level']))
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Threat Level:</td>
                                                <td style="color: #fbbf24; font-size: 14px; font-weight: 600;">{{ $vpn_data['threat_level'] }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Action Type Specific Messages -->
                            @if($type === 'vpn_warning')
                            <h3 style="margin: 30px 0 15px 0; color: #fbbf24; font-size: 20px;">⚠️ First Warning</h3>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.8; color: #cbd5e1;">
                                This is your <strong style="color: #fbbf24;">first warning</strong>. Using VPN or proxy services violates our terms of service. Please:
                            </p>
                            <ul style="margin: 0 0 20px 20px; padding: 0; color: #cbd5e1; font-size: 15px; line-height: 2;">
                                <li>Disable your VPN/Proxy immediately</li>
                                <li>Use your genuine IP address for all future logins</li>
                                <li>Contact support if you need assistance</li>
                            </ul>
                            <p style="margin: 20px 0; padding: 15px; background: rgba(251, 191, 36, 0.1); border-left: 4px solid #fbbf24; border-radius: 8px; font-size: 14px; color: #fde68a;">
                                <strong>⚠️ Next Offense:</strong> Task access will be suspended for 48 hours.
                            </p>

                            @elseif($type === 'vpn_suspension')
                            <h3 style="margin: 30px 0 15px 0; color: #f97316; font-size: 20px;">🚫 Second Warning - Task Suspension</h3>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.8; color: #cbd5e1;">
                                Your task access has been <strong style="color: #f97316;">suspended for 48 hours</strong>. This is a serious violation of our policies.
                            </p>
                            <p style="margin: 20px 0; padding: 15px; background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; border-radius: 8px; font-size: 14px; color: #fca5a5;">
                                <strong>🚨 Final Warning:</strong> One more VPN detection will result in a 7-day account suspension requiring manual system review.
                            </p>

                            @elseif($type === 'vpn_ban')
                            <h3 style="margin: 30px 0 15px 0; color: #ef4444; font-size: 20px;">🔒 Account Suspended</h3>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.8; color: #cbd5e1;">
                                Your account has been <strong style="color: #ef4444;">suspended for 7 days</strong> due to repeated VPN usage violations.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: rgba(239, 68, 68, 0.15); border: 2px solid #ef4444; border-radius: 12px; margin: 25px 0; padding: 20px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #fca5a5; font-weight: 600;">
                                            🔒 Your account requires manual system review before reinstatement.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin: 30px 0 0 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                                If you believe this is an error, please contact our support team immediately.
                            </p>

                            <p style="margin: 25px 0 0 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                                Best regards,<br>
                                <strong style="color: #f97316;">The {{ config('app.name') }} Security Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: rgba(0, 0, 0, 0.3); padding: 25px 30px; text-align: center; border-top: 1px solid rgba(239, 68, 68, 0.2);">
                            <p style="margin: 0 0 10px 0; color: #94a3b8; font-size: 13px;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #64748b; font-size: 12px;">
                                This is an automated security alert. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
