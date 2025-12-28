<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
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
                                Welcome to {{ config('app.name') }}! 🎉
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255, 255, 255, 0.95); font-size: 16px;">
                                Your journey to financial freedom starts here
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
                                Thank you for registering with <strong>{{ config('app.name') }}</strong>! We're excited to have you join our growing community of earners.
                            </p>

                            <!-- Account Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: rgba(168, 85, 247, 0.08); border: 1px solid rgba(168, 85, 247, 0.2); border-radius: 16px; margin: 30px 0; padding: 25px;">
                                <tr>
                                    <td>
                                        <h3 style="margin: 0 0 15px 0; color: #a855f7; font-size: 18px;">📋 Your Account Details</h3>
                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px; width: 40%;">Phone Number:</td>
                                                <td style="color: #e2e8f0; font-size: 14px; font-weight: 600;">{{ $user->phone_number }}</td>
                                            </tr>
                                            @if($user->email)
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Email:</td>
                                                <td style="color: #e2e8f0; font-size: 14px; font-weight: 600;">{{ $user->email }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Referral Code:</td>
                                                <td style="color: #a855f7; font-size: 16px; font-weight: 700;">{{ $user->referral_code }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <h3 style="margin: 30px 0 15px 0; color: #a855f7; font-size: 20px;">🚀 Next Steps:</h3>

                            <ol style="margin: 0; padding-left: 20px; color: #cbd5e1; font-size: 15px; line-height: 2;">
                                <li><strong style="color: #e2e8f0;">Verify your account</strong> using the OTP code sent to your phone/email</li>
                                <li><strong style="color: #e2e8f0;">Choose your subscription plan</strong> to unlock earning opportunities</li>
                                <li><strong style="color: #e2e8f0;">Complete your profile</strong> and set up withdrawal details</li>
                                <li><strong style="color: #e2e8f0;">Start completing tasks</strong> and earn daily rewards</li>
                            </ol>

                            <p style="margin: 30px 0 20px 0; font-size: 15px; line-height: 1.8; color: #cbd5e1;">
                                Once you activate your plan, you'll receive a detailed welcome package including your personalized contract and earning guide.
                            </p>

                            <!-- Important Note -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: rgba(236, 72, 153, 0.1); border-left: 4px solid #ec4899; border-radius: 8px; margin: 25px 0; padding: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #fda4af;">
                                            <strong>⚠️ Important:</strong> Your referral code <strong style="color: #ec4899;">{{ $user->referral_code }}</strong> is unique to you. Share it with friends and family to build your team and unlock higher ranks!
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 25px 0 0 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                                If you have any questions, our support team is always here to help.
                            </p>

                            <p style="margin: 25px 0 0 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                                Best regards,<br>
                                <strong style="color: #a855f7;">The {{ config('app.name') }} Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: rgba(0, 0, 0, 0.3); padding: 25px 30px; text-align: center; border-top: 1px solid rgba(168, 85, 247, 0.2);">
                            <p style="margin: 0 0 10px 0; color: #94a3b8; font-size: 13px;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #64748b; font-size: 12px;">
                                This email was sent to {{ $user->email ?? $user->phone_number }} because you registered for an account.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
