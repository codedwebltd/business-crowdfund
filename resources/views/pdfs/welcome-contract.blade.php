<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome Partnership Agreement - {{ $companyName }}</title>
    {!! $baseCSS !!}
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">{{ $watermark }}</div>

    <div class="pdf-container">
        <!-- Header -->
        <div class="pdf-header">
            <div class="logo-container">
                <img src="{{ $logo }}" alt="{{ $companyName }} Logo">
            </div>
            <div class="company-name">{{ $companyName }}</div>
            <div class="company-tagline">Welcome to the Future of Earning!</div>
        </div>

        <!-- Document Title -->
        <h1 class="doc-title">🎉 Partnership Agreement & Welcome Package</h1>

        <!-- Welcome Message -->
        <div class="success-box">
            <strong>Congratulations, {{ $userName }}!</strong><br>
            You've just taken the first step towards financial freedom. This document confirms your partnership with {{ $companyName }} and outlines the exciting opportunities ahead.
        </div>

        <!-- User Information -->
        <div class="section">
            <h2 class="section-title">Your Account Information</h2>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value">{{ $userName }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone Number:</span>
                    <span class="info-value">{{ $userPhone }}</span>
                </div>
                @if($userEmail)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $userEmail }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Referral Code:</span>
                    <span class="info-value"><strong>{{ $referralCode }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Registration Date:</span>
                    <span class="info-value">{{ $contractDate }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Current Rank:</span>
                    <span class="info-value">{{ $rankName }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Selected Plan:</span>
                    <span class="info-value">{{ $planName }}</span>
                </div>
            </div>
        </div>

        <!-- Partnership Agreement -->
        <div class="section">
            <h2 class="section-title">1. Partnership Overview</h2>
            <div class="section-content">
                <p>This agreement establishes a partnership between <strong>{{ $userName }}</strong> (hereinafter "Partner") and <strong>{{ $companyName }}</strong> (hereinafter "Platform") for the purpose of AI data contribution and task completion services.</p>

                <p>As a valued partner, you will contribute to training next-generation AI systems while earning competitive compensation for your time and expertise.</p>
            </div>
        </div>

        <!-- Earning Opportunities -->
        <div class="section">
            <h2 class="section-title">2. Your Earning Opportunities</h2>
            <div class="section-content">
                <h3 style="color: #7c3aed; font-size: 14px; margin-bottom: 10px;">💰 Task Completion Earnings</h3>
                <p>Complete daily tasks and earn money for rating AI responses, moderating content, watching videos, and sharing product feedback.</p>

                <table>
                    <tr>
                        <th>Your Plan Benefit</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Maximum Daily Tasks</td>
                        <td><strong>{{ $maxDailyTasks }} tasks per day</strong></td>
                    </tr>
                    <tr>
                        <td>Earning Multiplier</td>
                        <td><strong>{{ $taskMultiplier }}x</strong> base task reward</td>
                    </tr>
                    <tr>
                        <td>Average Daily Earning</td>
                        <td><strong>{{ \App\Helpers\CountryHelper::formatMoney($settings->daily_earning_average ?? 850, $settings->country_of_operation) }}</strong></td>
                    </tr>
                </table>

                <h3 style="color: #7c3aed; font-size: 14px; margin: 20px 0 10px;">🌟 Referral Commission (Passive Income)</h3>
                <p>Build your team and earn from <strong>{{ $settings->referral_levels_depth ?? 10 }} levels</strong> of downline activity!</p>

                <ul>
                    <li><strong>Level 1 (Direct Referrals):</strong> 20% activation commission + 10% ongoing task earnings</li>
                    <li><strong>Level 2:</strong> 10% activation commission + 5% ongoing task earnings</li>
                    <li><strong>Levels 3-{{ $settings->referral_levels_depth ?? 10 }}:</strong> Decreasing percentages ensuring passive income from your entire network</li>
                </ul>

                <div class="highlight-box">
                    <strong>💡 Pro Tip:</strong> The more people you refer, the more passive income you generate. Even small commissions from hundreds of downlines add up quickly!
                </div>

                <h3 style="color: #7c3aed; font-size: 14px; margin: 20px 0 10px;">🏆 Rank Progression Bonuses</h3>
                <p>As you build your team, you'll advance through ranks E.g(Bronze → Silver → Gold → Diamond) and unlock:</p>
                <ul>
                    <li>Higher withdrawal limits</li>
                    <li>Commission multipliers (up to +10% for Diamond)</li>
                    <li>Priority withdrawal processing</li>
                    <li>Monthly leadership bonuses (Diamond rank: up to {!! \App\Helpers\CountryHelper::formatMoney(50000, $settings->country_of_operation) !!})</li>
                </ul>
            </div>
        </div>

        <!-- How to Succeed -->
        <div class="section page-break">
            <h2 class="section-title">3. Your Path to Success</h2>
            <div class="section-content">
                <h3 style="color: #10b981; font-size: 14px; margin-bottom: 10px;">📋 Step 1: Complete Daily Tasks</h3>
                <p>Log in daily and complete your assigned tasks. Tasks take just 2-5 minutes each. The more consistent you are, the more you earn!</p>

                <div class="success-box">
                    <strong>Task Completion is KEY!</strong> Consistent task completion not only earns you direct money but also makes you eligible for rank promotions.
                </div>

                <h3 style="color: #f59e0b; font-size: 14px; margin: 20px 0 10px;">🤝 Step 2: Build Your Team</h3>
                <p>Share your referral code: <strong>{{ $referralCode }}</strong></p>
                <p>Every person who joins through your link becomes part of your network. You earn from their activations and ongoing task earnings.</p>

                <div class="highlight-box">
                    <strong>Referral Strategy:</strong> Focus on quality over quantity. Refer active people who will complete tasks regularly. Their consistent earnings mean consistent passive income for you!
                </div>

                <h3 style="color: #7c3aed; font-size: 14px; margin: 20px 0 10px;">📈 Step 3: Track Your Progress</h3>
                <p>Monitor your dashboard daily to see:</p>
                <ul>
                    <li>Pending balance (maturing in 72 hours)</li>
                    <li>Withdrawable balance (ready for payout)</li>
                    <li>Team size and network growth</li>
                    <li>Rank progression status</li>
                </ul>

                <h3 style="color: #ec4899; font-size: 14px; margin: 20px 0 10px;">💸 Step 4: Withdraw Your Earnings</h3>
                <p>Minimum withdrawal: {{ \App\Helpers\CountryHelper::formatMoney($settings->minimum_withdrawal ?? 5000, $settings->country_of_operation) }}</p>
                <p>Withdrawals are processed to your registered bank account or crypto wallet within 24-72 hours depending on your rank.</p>

                <div class="success-box">
                    <strong>First Withdrawal Requirement:</strong> Submit a testimonial about your experience before your first withdrawal. This builds trust in our community!
                </div>
            </div>
        </div>

        <!-- Important Reminders -->
        <div class="section">
            <h2 class="section-title">4. Important Reminders</h2>
            <div class="section-content">
                <table>
                    <tr>
                        <th>Rule</th>
                        <th>Why It Matters</th>
                    </tr>
                    <tr>
                        <td><strong>ONE account per person</strong></td>
                        <td>Multi-accounting leads to immediate ban and balance forfeiture</td>
                    </tr>
                    <tr>
                        <td><strong>Complete tasks honestly</strong></td>
                        <td>We detect patterns. Bot-like behavior results in account suspension</td>
                    </tr>
                    <tr>
                        <td><strong>No VPN usage</strong></td>
                        <td>VPNs trigger fraud alerts. Use your genuine IP address</td>
                    </tr>
                    <tr>
                        <td><strong>Activate within 7 days</strong></td>
                        <td>Unactivated accounts may be deleted to free up resources</td>
                    </tr>
                    <tr>
                        <td><strong>Stay active</strong></td>
                        <td>Complete at least 1 task per week to maintain account status</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Activation Details -->
        @if($activationAmount !== 'N/A')
        <div class="section">
            <h2 class="section-title">5. Plan Activation Summary</h2>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Selected Plan:</span>
                    <span class="info-value"><strong>{{ $planName }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Activation Amount:</span>
                    <span class="info-value"><strong>{{ $activationAmount }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ $user->status === 'ACTIVE' ? '✅ ACTIVATED' : '⏳ Pending Payment Verification' }}</span>
                </div>
            </div>

            @if($user->status !== 'ACTIVE')
            <div class="highlight-box">
                <strong>Next Step:</strong> Complete your activation payment to unlock task access. Once payment is verified by our admin team, you'll receive full access to daily tasks and start earning immediately!
            </div>
            @endif
        </div>
        @endif

        <!-- Motivational Closing -->
        <div class="section">
            <h2 class="section-title">Your Journey Starts Now!</h2>
            <div class="section-content">
                <p style="font-size: 14px; line-height: 1.8;">
                    <strong>{{ $userName }},</strong> you're now part of a growing community of {{ $settings->total_users ?? '10,000+' }} earners who are taking control of their financial future.
                </p>

                <p style="font-size: 14px; line-height: 1.8;">
                    Whether you're here to earn extra income through tasks or build a thriving network, {{ $companyName }} provides the tools and support you need to succeed.
                </p>

                <div class="success-box">
                    <strong style="font-size: 16px;">🚀 Your potential is unlimited. Let's build wealth together!</strong>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Partner Signature</strong><br>
                    {{ $userName }}
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-line">
                    <strong>{{ $companyName }}</strong><br>
                    Platform Management
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="pdf-footer">
        <div class="footer-content">
            <span>© {{ now()->year }} {{ $companyName }}. Partnership Agreement.</span>
            <span>Document ID: WC-{{ $referralCode }}-{{ now()->format('Ymd') }}</span>
        </div>
    </div>
</body>
</html>
