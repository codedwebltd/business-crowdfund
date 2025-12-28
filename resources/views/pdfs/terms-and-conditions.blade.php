<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Terms and Conditions - {{ $companyName }}</title>
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
            <div class="company-tagline">Empowering Income Through AI Training & Task Completion</div>
        </div>

        <!-- Document Title -->
        <h1 class="doc-title">{{ $documentTitle }}</h1>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Effective Date:</span>
                <span class="info-value">{{ $effectiveDate }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Last Updated:</span>
                <span class="info-value">{{ now()->format('F d, Y') }}</span>
            </div>
        </div>

        <!-- Introduction -->
        <div class="section">
            <h2 class="section-title">1. Introduction & Acceptance</h2>
            <div class="section-content">
                <p>Welcome to <strong>{{ $companyName }}</strong>! These Terms and Conditions govern your use of our platform and services. By registering an account, you acknowledge that you have read, understood, and agree to be bound by these terms.</p>

                <div class="highlight-box">
                    <strong>Important:</strong> If you do not agree to these terms, please do not use our platform. Your continued use constitutes acceptance of these terms.
                </div>
            </div>
        </div>

        <!-- Eligibility -->
        <div class="section">
            <h2 class="section-title">2. Eligibility Requirements</h2>
            <div class="section-content">
                <p>To use {{ $companyName }}, you must:</p>
                <ul>
                    <li>Be at least <strong>18 years of age</strong> or the legal age of majority in your jurisdiction</li>
                    <li>Provide accurate and truthful information during registration</li>
                    <li>Have the legal capacity to enter into binding contracts</li>
                    <li>Not be prohibited from using our services under applicable laws</li>
                    <li>Maintain only <strong>ONE account per person</strong> (multi-accounting is strictly forbidden)</li>
                </ul>
            </div>
        </div>

        <!-- Account Registration -->
        <div class="section">
            <h2 class="section-title">3. Account Registration & Activation</h2>
            <div class="section-content">
                <p><strong>3.1 Registration Process:</strong> You must provide a valid phone number, full name, and bank account details for identity verification and payment processing.</p>

                <p><strong>3.2 Plan Activation:</strong> Your account becomes active only after:</p>
                <ol>
                    <li>Selecting an activation plan (Basic, Premium, or VIP)</li>
                    <li>Making payment of the activation amount</li>
                    <li>Admin verification and approval of your payment</li>
                </ol>

                <p><strong>3.3 Account Security:</strong> You are responsible for maintaining the confidentiality of your login credentials. Any activity conducted through your account is your responsibility.</p>
            </div>
        </div>

        <!-- Task Completion -->
        <div class="section page-break">
            <h2 class="section-title">4. Task Completion & Earnings</h2>
            <div class="section-content">
                <p><strong>4.1 Task Types:</strong> Our platform offers various tasks including AI response rating, text moderation, video watching, surveys, and product reviews.</p>

                <p><strong>4.2 Task Integrity:</strong> You must:</p>
                <ul>
                    <li>Complete tasks honestly and accurately</li>
                    <li>Not use automated tools, bots, or scripts</li>
                    <li>Spend the minimum required time on each task</li>
                    <li>Provide genuine responses based on the task requirements</li>
                </ul>

                <p><strong>4.3 Earnings & Balance:</strong></p>
                <ul>
                    <li><strong>Pending Balance:</strong> Task rewards are held for {{ $settings->pending_balance_maturation_hours ?? 72 }} hours before becoming withdrawable</li>
                    <li><strong>Withdrawable Balance:</strong> Matured earnings available for withdrawal, subject to minimum thresholds</li>
                    <li><strong>Referral Bonuses:</strong> Commission from your downline's activities, credited directly to withdrawable balance</li>
                </ul>

                <div class="success-box">
                    <strong>Good Faith Principle:</strong> {{ $companyName }} operates on trust. We expect all users to complete tasks in good faith and contribute genuine data for AI training purposes.
                </div>
            </div>
        </div>

        <!-- Referral System -->
        <div class="section">
            <h2 class="section-title">5. Referral Program</h2>
            <div class="section-content">
                <p><strong>5.1 Referral Structure:</strong> Our platform uses a {{ $settings->referral_levels_depth ?? 40 }}-level deep referral network. You earn commissions from your direct referrals and their networks.</p>

                <p><strong>5.2 Prohibited Practices:</strong></p>
                <ul>
                    <li>Creating fake accounts to refer yourself</li>
                    <li>Using deceptive marketing or false promises to recruit</li>
                    <li>Circular referral chains (A refers B, B refers C, C refers A)</li>
                    <li>Spamming or unsolicited promotional activities</li>
                </ul>

                <p><strong>5.3 Commission Rates:</strong> Commission percentages are dynamic and set by the platform. Rates may be adjusted to ensure platform sustainability.</p>
            </div>
        </div>

        <!-- Withdrawals -->
        <div class="section">
            <h2 class="section-title">6. Withdrawal Policy</h2>
            <div class="section-content">
                <p><strong>6.1 Withdrawal Requirements:</strong></p>
                <ul>
                    <li>Minimum withdrawal: {{ \App\Helpers\CountryHelper::formatMoney($settings->minimum_withdrawal ?? 5000, $settings->country_of_operation) }}</li>
                    <li>Maximum withdrawal varies by rank ({{ \App\Helpers\CountryHelper::formatMoney($settings->maximum_withdrawal ?? 50000, $settings->country_of_operation) }} for Bronze)</li>
                    <li>KYC verification required for withdrawals above {{ \App\Helpers\CountryHelper::formatMoney($settings->kyc_withdrawal_threshold ?? 50000, $settings->country_of_operation) }}</li>
                </ul>

                <p><strong>6.2 Processing Times:</strong> Withdrawals are processed within 24-72 hours depending on your rank. Diamond users receive priority processing.</p>

                <p><strong>6.3 First Withdrawal Requirement:</strong> Before your first withdrawal, you must submit a testimonial describing your experience with {{ $companyName }}. This helps build trust in our community.</p>

                <div class="highlight-box">
                    <strong>Platform Reserves:</strong> {{ $companyName }} reserves the right to delay withdrawals during periods of high volume or to verify suspicious activity. We are committed to paying all legitimate earnings.
                </div>
            </div>
        </div>

        <!-- Prohibited Activities -->
        <div class="section page-break">
            <h2 class="section-title">7. Prohibited Activities</h2>
            <div class="section-content">
                <p>The following activities will result in immediate account suspension or termination:</p>
                <ul>
                    <li>Creating multiple accounts</li>
                    <li>Using VPNs or proxies to bypass geolocation restrictions</li>
                    <li>Bot-like task completion (too fast or identical patterns)</li>
                    <li>Manipulating referral networks</li>
                    <li>Attempting to hack, reverse engineer, or exploit platform vulnerabilities</li>
                    <li>Fraudulent payment methods or chargebacks</li>
                    <li>Impersonating other users or {{ $companyName }} staff</li>
                </ul>
            </div>
        </div>

        <!-- Account Termination -->
        <div class="section">
            <h2 class="section-title">8. Account Suspension & Termination</h2>
            <div class="section-content">
                <p><strong>8.1 Suspension:</strong> Your account may be suspended temporarily for investigation if fraudulent activity is suspected.</p>

                <p><strong>8.2 Termination:</strong> We reserve the right to terminate accounts that violate these terms. Upon termination:</p>
                <ul>
                    <li>Access to your account will be permanently revoked</li>
                    <li>Pending balances may be forfeited if fraud is confirmed</li>
                    <li>Withdrawable balances from legitimate activity will be paid out</li>
                </ul>

                <p><strong>8.3 Appeal Process:</strong> If you believe your account was wrongly suspended, contact support with evidence within 14 days.</p>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="section">
            <h2 class="section-title">9. Disclaimer of Warranties</h2>
            <div class="section-content">
                <p>{{ $companyName }} is provided "AS IS" without warranties of any kind. We do not guarantee:</p>
                <ul>
                    <li>Uninterrupted or error-free service</li>
                    <li>Specific earnings or income levels</li>
                    <li>Availability of tasks at all times</li>
                    <li>Continued operation of the platform indefinitely</li>
                </ul>
            </div>
        </div>

        <!-- Limitation of Liability -->
        <div class="section">
            <h2 class="section-title">10. Limitation of Liability</h2>
            <div class="section-content">
                <p>To the maximum extent permitted by law, {{ $companyName }} and its operators shall not be liable for:</p>
                <ul>
                    <li>Loss of earnings or expected profits</li>
                    <li>Business interruption or loss of data</li>
                    <li>Indirect, incidental, or consequential damages</li>
                    <li>Damages arising from third-party actions or payment processors</li>
                </ul>
            </div>
        </div>

        <!-- Changes to Terms -->
        <div class="section">
            <h2 class="section-title">11. Changes to Terms</h2>
            <div class="section-content">
                <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting. Your continued use of the platform constitutes acceptance of modified terms.</p>

                <p>Users will be notified of significant changes via email or in-app notification.</p>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="section">
            <h2 class="section-title">12. Contact & Support</h2>
            <div class="section-content">
                <p>For questions, concerns, or support, contact us at:</p>
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $settings->support_email ?? 'support@'.$settings->app_url }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $settings->support_phone ?? 'Available in Dashboard' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">WhatsApp:</span>
                        <span class="info-value">{{ $settings->support_whatsapp ?? 'Available in Dashboard' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acknowledgment -->
        <div class="section">
            <div class="success-box">
                <strong>By using {{ $companyName }}, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</strong>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="pdf-footer">
        <div class="footer-content">
            <span>© {{ now()->year }} {{ $companyName }}. All rights reserved.</span>
            <span>Document ID: TC-{{ now()->format('Ymd') }}-{{ substr(md5($user->id), 0, 8) }}</span>
        </div>
    </div>
</body>
</html>
