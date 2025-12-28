<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $settings->app_name }}</title>
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
                            <h1 style="margin: 0; color: white; font-size: 32px; font-weight: bold; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                🎉 Welcome to {{ $settings->app_name }}!
                            </h1>
                            <p style="margin: 10px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 16px;">
                                Your Journey to Financial Freedom Starts Now
                            </p>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="padding: 40px 30px 20px;">
                            <h2 style="margin: 0 0 20px; color: #ffffff; font-size: 24px;">
                                Hello, {{ $user->full_name }}! 👋
                            </h2>
                            <p style="margin: 0 0 15px; color: rgba(255, 255, 255, 0.8); font-size: 16px; line-height: 1.6;">
                                Congratulations on taking the first step towards building your income! We're thrilled to have you join our community of <strong style="color: #a855f7;">{{ $settings->total_users ?? '10,000+' }}</strong> earners.
                            </p>
                        </td>
                    </tr>

                    <!-- Account Details Box -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.2) 0%, rgba(168, 85, 247, 0.2) 100%); border-radius: 12px; border: 1px solid rgba(168, 85, 247, 0.3);">
                                <tr>
                                    <td style="padding: 25px;">
                                        <h3 style="margin: 0 0 20px; color: #a855f7; font-size: 18px;">
                                            📋 Your Account Details
                                        </h3>
                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="color: rgba(255, 255, 255, 0.7); font-size: 14px;">Referral Code:</td>
                                                <td style="color: #ffffff; font-size: 14px; font-weight: bold; text-align: right;">{{ $user->referral_code }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: rgba(255, 255, 255, 0.7); font-size: 14px;">Phone Number:</td>
                                                <td style="color: #ffffff; font-size: 14px; text-align: right;">{{ $user->phone_number }}</td>
                                            </tr>
                                            @if($user->email)
                                            <tr>
                                                <td style="color: rgba(255, 255, 255, 0.7); font-size: 14px;">Email:</td>
                                                <td style="color: #ffffff; font-size: 14px; text-align: right;">{{ $user->email }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="color: rgba(255, 255, 255, 0.7); font-size: 14px;">Current Status:</td>
                                                <td style="color: {{ $user->status === 'ACTIVE' ? '#10b981' : '#f59e0b' }}; font-size: 14px; font-weight: bold; text-align: right;">
                                                    {{ $user->status === 'ACTIVE' ? '✅ ACTIVE' : '⏳ Pending Activation' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- How Tasks Work -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <h3 style="margin: 0 0 15px; color: #ffffff; font-size: 20px;">
                                💰 How You Earn Money
                            </h3>
                            <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <h4 style="margin: 0 0 8px; color: #10b981; font-size: 16px;">1. Complete Daily Tasks</h4>
                                <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.6;">
                                    Rate AI responses, moderate content, watch videos, and share opinions. Each task takes 2-5 minutes and pays <strong>₦200-₦500</strong>!
                                </p>
                            </div>

                            <div style="background: rgba(168, 85, 247, 0.1); border-left: 4px solid #a855f7; padding: 15px; border-radius: 8px;">
                                <h4 style="margin: 0 0 8px; color: #a855f7; font-size: 16px;">2. Build Your Team (Passive Income!)</h4>
                                <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.6;">
                                    Share your referral code <strong style="color: #a855f7;">{{ $user->referral_code }}</strong> and earn from <strong>{{ $settings->referral_levels_depth ?? 40 }} levels</strong> of downline activity. Even if they're 20 levels below you, you still earn!
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Why Referrals Are Important -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(251, 146, 60, 0.2) 100%); border-radius: 12px; border: 1px solid rgba(245, 158, 11, 0.3);">
                                <tr>
                                    <td style="padding: 25px;">
                                        <h3 style="margin: 0 0 15px; color: #f59e0b; font-size: 18px;">
                                            🚀 Why Referrals = Real Wealth
                                        </h3>
                                        <p style="margin: 0 0 12px; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.6;">
                                            Tasks give you <strong>active income</strong>, but referrals create <strong>PASSIVE income</strong> that grows exponentially!
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.8;">
                                            <li>Refer 10 people who each refer 10 = You have 110 people earning for you!</li>
                                            <li>Even small 1-5% commissions from hundreds of downlines = <strong>₦50,000+/month passive</strong></li>
                                            <li>Top earners make <strong>₦500,000+/month</strong> from referrals alone</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Next Steps -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <h3 style="margin: 0 0 20px; color: #ffffff; font-size: 20px;">
                                📖 What's Next?
                            </h3>

                            @if($user->status !== 'ACTIVE')
                            <div style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.2) 0%, rgba(168, 85, 247, 0.2) 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid rgba(168, 85, 247, 0.3);">
                                <h4 style="margin: 0 0 10px; color: #a855f7; font-size: 16px;">Step 1: Activate Your Account</h4>
                                <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.6;">
                                    Choose a plan (Basic/Premium/VIP) and make payment. Once verified, you'll unlock daily tasks and start earning immediately!
                                </p>
                            </div>
                            @endif

                            <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">
                                    <strong style="color: #10b981;">✓</strong> Complete at least 1 task daily for consistency
                                </p>
                            </div>

                            <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">
                                    <strong style="color: #10b981;">✓</strong> Share your referral code: <strong style="color: #a855f7;">{{ $user->referral_code }}</strong>
                                </p>
                            </div>

                            <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px;">
                                <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">
                                    <strong style="color: #10b981;">✓</strong> Track your progress in the dashboard
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- PDF Attachments Notice -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: rgba(59, 130, 246, 0.1); border-radius: 12px; border: 1px solid rgba(59, 130, 246, 0.3);">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h4 style="margin: 0 0 10px; color: #3b82f6; font-size: 16px;">📎 Important Documents Attached</h4>
                                        <p style="margin: 0; color: rgba(255, 255, 255, 0.8); font-size: 14px; line-height: 1.6;">
                                            We've attached your <strong>Welcome Contract</strong> and <strong>Terms & Conditions</strong> as PDF files. Please review them carefully to understand your partnership with {{ $settings->app_name }}.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td style="padding: 0 30px 40px;" align="center">
                            <a href="{{ $settings->app_url ?? 'https://crowdpower.ng' }}/login"
                               style="display: inline-block; background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: white; text-decoration: none; padding: 16px 40px; border-radius: 12px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);">
                                🚀 Login to Your Dashboard
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: rgba(0, 0, 0, 0.2); padding: 30px; text-align: center; border-radius: 0 0 16px 16px;">
                            <p style="margin: 0 0 10px; color: rgba(255, 255, 255, 0.6); font-size: 14px;">
                                Need help? Contact us at <a href="mailto:{{ $settings->support_email ?? 'support@crowdpower.ng' }}" style="color: #a855f7; text-decoration: none;">{{ $settings->support_email ?? 'support@crowdpower.ng' }}</a>
                            </p>
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
