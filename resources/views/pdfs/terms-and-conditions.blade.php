<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Terms of Service - {{ $companyName }}</title>
    {!! $baseCSS !!}
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">{{ $watermark }}</div>

    <div class="pdf-container">
        <!-- Header -->
        <div class="pdf-header">
            {{-- <div class="logo-container">
                <img src="{{ $logo }}" alt="{{ $companyName }} Logo">
            </div> --}}
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
            <div class="info-row">
                <span class="info-label">Version:</span>
                <span class="info-value">{{ now()->format('Y.m') }}</span>
            </div>
        </div>

        <!-- Introduction -->
        <div class="section">
            <h2 class="section-title">1. Introduction & Acceptance</h2>
            <div class="section-content">
                <p>These Terms of Service constitute a legal agreement between you and <strong>{{ $companyName }}</strong> governing your use of the platform and services. By creating an account, you acknowledge that you have read, understood, and agree to be bound by these terms in their entirety.</p>

                <div class="highlight-box">
                    <strong>IMPORTANT:</strong> If you do not agree to these terms, do not use the platform. You must accept and abide by these Terms as presented; changes, additions, or deletions are not acceptable, and {{ $companyName }} may refuse access for noncompliance with any part of these Terms.
                </div>

                <p><strong>1.1 Agreement Formation:</strong> These Terms, together with our Privacy Policy and any other policies posted on the platform, constitute the entire agreement between you and {{ $companyName }} regarding your use of the platform and supersede all prior agreements.</p>

                <p><strong>1.2 Modifications:</strong> We reserve the right to modify these Terms at any time. Modifications become effective immediately upon posting. Your continued use constitutes acceptance of modified terms.</p>
            </div>
        </div>

        <!-- Eligibility -->
        <div class="section">
            <h2 class="section-title">2. Eligibility Requirements</h2>
            <div class="section-content">
                <p>To access and use {{ $companyName }}, you represent and warrant that you:</p>
                <ul>
                    <li>Are at least <strong>18 years of age</strong> or the legal age of majority in your jurisdiction</li>
                    <li>Possess the legal right and capacity to enter into binding contracts</li>
                    <li>Will provide accurate, current, and complete information during registration</li>
                    <li>Are not prohibited from using our services under applicable laws or regulations</li>
                    <li>Will maintain only <strong>ONE account per person</strong> (multi-accounting is strictly prohibited and will result in permanent ban)</li>
                    <li>Are not located in a jurisdiction where use of the platform is prohibited</li>
                    <li>Have not been previously suspended or removed from the platform</li>
                </ul>

                <div class="highlight-box">
                    <strong>Identity Verification:</strong> We reserve the right to verify your identity at any time using KYC (Know Your Customer) procedures, including National Identification Number (NIN), utility bills, selfie verification, and other documentation as required by law or platform policies.
                </div>
            </div>
        </div>

        <!-- Account Registration -->
        <div class="section">
            <h2 class="section-title">3. Account Registration & Activation</h2>
            <div class="section-content">
                <p><strong>3.1 Registration Process:</strong> You must provide a valid phone number, full legal name, bank account details (or cryptocurrency wallet address), and other required information for identity verification and payment processing. You confirm that all information provided is accurate and truthful.</p>

                <p><strong>3.2 Account Activation:</strong> Your account becomes active only after:</p>
                <ol>
                    <li>Completing the registration process with valid information</li>
                    <li>Selecting an activation plan (Basic, Premium, VIP, or other available tiers)</li>
                    <li>Making payment of the activation amount via approved payment methods (bank transfer, cryptocurrency, or other specified methods)</li>
                    <li>Administrative verification and approval of your payment</li>
                    <li>System assignment of your unique referral code in format CP-{COUNTRY}-{NUMBER}</li>
                </ol>

                <p><strong>3.3 Account Security:</strong> You are solely responsible for maintaining the confidentiality and security of your account credentials. Any activity conducted through your account is your responsibility. You must:</p>
                <ul>
                    <li>Not reveal your account information to anyone else or allow others to use your account</li>
                    <li>Not use anyone else's account or impersonate other users</li>
                    <li>Immediately notify {{ $companyName }} of any unauthorized use or security breach</li>
                    <li>Use strong, unique passwords and enable two-factor authentication if available</li>
                </ul>

                <p><strong>3.4 Account Limitations:</strong> {{ $companyName }} shall not be responsible for losses arising from unauthorized or improper use of your account.</p>
            </div>
        </div>

        <!-- Task Completion -->
        <div class="section page-break">
            <h2 class="section-title">4. Task Completion System & Earnings</h2>
            <div class="section-content">
                <p><strong>4.1 Task Categories:</strong> The platform offers various task categories, including but not limited to:</p>
                <ul>
                    <li><strong>Survey Tasks:</strong> AI-generated questionnaires and surveys for data collection and research purposes</li>
                    <li><strong>Video Tasks:</strong> Watching and reviewing video content with verification questions to ensure genuine engagement</li>
                    <li><strong>Product Review Tasks:</strong> Writing authentic reviews for products and services</li>
                    <li><strong>Data Sync Tasks:</strong> Device data synchronization for research and analytics purposes</li>
                    <li>Other task types as introduced by the platform from time to time</li>
                </ul>

                <p><strong>4.2 Task Integrity & Requirements:</strong> You agree to:</p>
                <ul>
                    <li>Complete all tasks honestly, accurately, and in good faith without deception</li>
                    <li>Not use automated tools, bots, scripts, or artificial intelligence to complete tasks</li>
                    <li>Spend the minimum required time on each task as specified in task instructions</li>
                    <li>Provide genuine responses based on task requirements and your actual opinions/experiences</li>
                    <li>Not share task content, answers, or strategies with other users</li>
                    <li>Not manipulate or circumvent task validation mechanisms</li>
                    <li>Complete tasks personally without delegation to third parties</li>
                </ul>

                <p><strong>4.3 Task Assignment & Availability:</strong> Tasks are assigned daily based on your subscription plan tier, rank level, platform capacity, and task availability. The number and type of tasks may vary. We do not guarantee specific numbers or types of tasks will be available daily.</p>

                <p><strong>4.4 Task Validation:</strong> All task completions are subject to automated and manual validation. Tasks may be rejected if completed too quickly, showing bot-like patterns, containing incorrect answers, or demonstrating fraudulent behavior. Rejected tasks will not be credited.</p>

                <p><strong>4.5 Earnings & Balance Management:</strong></p>
                <ul>
                    <li><strong>Pending Balance:</strong> Task rewards are initially credited to Pending Balance and must "mature" for {{ $settings->pending_balance_maturation_hours ?? 72 }} hours before becoming withdrawable. This maturation period allows for fraud detection and task validation.</li>
                    <li><strong>Withdrawable Balance:</strong> Matured earnings available for withdrawal, subject to minimum and maximum thresholds based on your rank. May also be used for plan upgrades.</li>
                    <li><strong>Referral Balance:</strong> Commission from your downline's activities (both activation commissions and task earnings commissions), credited directly to withdrawable balance with no maturation period.</li>
                    <li><strong>Bonus Balance:</strong> Platform bonuses, promotions, special rewards, and leadership bonuses for Diamond and Royal Diamond ranks.</li>
                </ul>

                <p><strong>4.6 No Guaranteed Earnings:</strong> {{ $companyName }} does not guarantee any specific level of earnings, income, or profitability. Actual earnings depend on task completion, referral activity, platform availability, and factors beyond our control.</p>

                <div class="success-box">
                    <strong>Good Faith Principle:</strong> {{ $companyName }} operates on trust and mutual respect. We expect all users to complete tasks in good faith and contribute genuine data for AI training and research purposes. Your honest participation helps build a sustainable platform for all members.
                </div>
            </div>
        </div>

        <!-- Referral System -->
        <div class="section">
            <h2 class="section-title">5. Referral Program & Commission Structure</h2>
            <div class="section-content">
                <p><strong>5.1 Referral Structure:</strong> The platform operates a multi-level referral network system with up to {{ $settings->referral_levels_depth ?? 40 }} levels deep. You earn commissions from your direct referrals (Level 1) and their subsequent networks (Levels 2-{{ $settings->referral_levels_depth ?? 40 }}).</p>

                <p><strong>5.2 Referral Code:</strong> Upon account activation, you receive a unique referral code in the format CP-{COUNTRY}-{NUMBER}. This code is your exclusive identifier for tracking referrals and must not be shared deceptively.</p>

                <p><strong>5.3 Commission Types:</strong></p>
                <ul>
                    <li><strong>Activation Commissions:</strong> When a member in your referral network activates their account or upgrades their plan, you earn a percentage commission based on your level relationship to that member. Commission rates are progressive and configurable by the platform.</li>
                    <li><strong>Task Earnings Commissions:</strong> When members in your referral network complete tasks, you earn a percentage of their task earnings. These commissions are accumulated and credited periodically (typically daily) directly to your Withdrawable Balance.</li>
                    <li><strong>Rank Multipliers:</strong> Higher-tier ranks (Gold, Platinum, Diamond, Royal Diamond) receive commission multipliers that increase earnings across all referral levels.</li>
                </ul>

                <p><strong>5.4 Prohibited Referral Practices:</strong> The following activities are strictly prohibited and will result in account termination and forfeiture of all balances:</p>
                <ul>
                    <li>Creating fake accounts to refer yourself or benefit from self-referrals</li>
                    <li>Using deceptive marketing, false promises, or misrepresentation to recruit</li>
                    <li>Circular referral chains (e.g., User A refers User B, User B refers User C, User C refers User A)</li>
                    <li>Spamming, unsolicited promotional activities, or harassment</li>
                    <li>Referring accounts from the same device or IP address</li>
                    <li>Paying individuals to create accounts under your referral code</li>
                    <li>Using VPNs or proxies to circumvent geographic or device restrictions</li>
                    <li>Manipulating referral networks through fraudulent or coordinated means</li>
                </ul>

                <p><strong>5.5 Commission Rates:</strong> Commission percentages are dynamically set by the platform and may be adjusted to ensure platform sustainability. Current commission rates are available in your account dashboard. We reserve the right to modify commission structures with reasonable notice to users.</p>

                <div class="highlight-box">
                    <strong>Referral Integrity:</strong> The success of {{ $companyName }} depends on the integrity of our referral network. We employ sophisticated fraud detection systems including device fingerprinting, IP tracking, and referral chain analysis to identify and prevent abuse. Violations will result in immediate account suspension and forfeiture of all balances, potentially affecting your entire referral network.
                </div>
            </div>
        </div>

        <!-- Rank System -->
        <div class="section">
            <h2 class="section-title">6. Rank System & Progression</h2>
            <div class="section-content">
                <p><strong>6.1 Rank Tiers:</strong> Users are assigned ranks based on performance metrics including direct referrals, total team size, network activity, and task completion. Ranks include (from lowest to highest): <strong>Bronze, Silver, Gold, Platinum, Diamond, and Royal Diamond</strong>.</p>

                <p><strong>6.2 Rank Benefits:</strong> Higher ranks receive increased withdrawal limits, faster processing times, commission rate multipliers, more daily task assignments, priority customer support, access to exclusive VIP groups, monthly leadership bonuses (Diamond and Royal Diamond), and platform recognition.</p>

                <p><strong>6.3 Rank Evaluation:</strong> Ranks are evaluated automatically by the system on a daily basis. Promotions occur when you meet the criteria for the next rank tier. Demotions may occur if you no longer meet minimum criteria for your current rank.</p>

                <p><strong>6.4 Star Rating System:</strong> In addition to ranks, users are assigned star ratings (1-5 stars) based on overall performance, including task completion rate, referral quality, network growth, and platform engagement. Five-star users ("Generals") receive priority withdrawal processing and enhanced benefits.</p>
            </div>
        </div>

        <!-- Withdrawals -->
        <div class="section page-break">
            <h2 class="section-title">7. Withdrawal Policy & Processing</h2>
            <div class="section-content">
                <p><strong>7.1 Withdrawal Requirements:</strong> To request a withdrawal, you must meet the following conditions:</p>
                <ul>
                    <li>Minimum withdrawal: {{ \App\Helpers\CountryHelper::formatMoney($settings->minimum_withdrawal ?? 5000, $settings->country_of_operation) }} (varies by rank tier)</li>
                    <li>Maximum withdrawal: {{ \App\Helpers\CountryHelper::formatMoney($settings->maximum_withdrawal ?? 50000, $settings->country_of_operation) }} (varies by rank tier)</li>
                    <li>Sufficient withdrawable balance to cover requested amount</li>
                    <li>Account status must be ACTIVE (not suspended or under review)</li>
                    <li>KYC verification required for withdrawals above {{ \App\Helpers\CountryHelper::formatMoney($settings->kyc_withdrawal_threshold ?? 50000, $settings->country_of_operation) }}</li>
                    <li>Daily withdrawal limit not exceeded (varies by rank)</li>
                    <li>Bank account or cryptocurrency wallet details must be verified and match KYC information</li>
                </ul>

                <p><strong>7.2 Processing Times:</strong> Withdrawal requests are processed according to your rank tier:</p>
                <ul>
                    <li><strong>Bronze:</strong> 48-72 hours</li>
                    <li><strong>Silver:</strong> 24-48 hours</li>
                    <li><strong>Gold:</strong> 12-24 hours</li>
                    <li><strong>Platinum:</strong> 6-12 hours</li>
                    <li><strong>Diamond/Royal Diamond:</strong> Priority processing (typically within 24 hours)</li>
                </ul>

                <p>Processing times are estimates and may vary based on platform volume, payment method, banking hours, verification requirements, and liquidity management. Five-star users receive priority queue placement.</p>

                <p><strong>7.3 First Withdrawal Requirements:</strong> Before processing your first withdrawal, you must submit a testimonial (minimum {{ $settings->testimonial_min_chars ?? 50 }} characters) describing your experience with {{ $companyName }}. This helps build trust and community credibility.</p>

                <p><strong>7.4 Payment Methods:</strong> Withdrawals are processed via bank transfer (direct to your registered bank account) or cryptocurrency (USDT transfer to your registered wallet address with real-time conversion rates), and other approved payment methods as specified by the platform.</p>

                <p><strong>7.5 Withdrawal Limitations:</strong> {{ $companyName }} reserves the right to:</p>
                <ul>
                    <li>Delay withdrawals during periods of high volume or for additional verification</li>
                    <li>Reject withdrawal requests that do not meet requirements or raise fraud concerns</li>
                    <li>Modify withdrawal limits and processing times based on platform liquidity and burn rate monitoring</li>
                    <li>Require additional documentation for large or unusual withdrawal requests</li>
                    <li>Impose temporary withdrawal restrictions during system maintenance or upgrades</li>
                    <li>Freeze withdrawals for accounts under investigation for fraud or Terms violations</li>
                    <li>Extend processing times to maintain platform stability and ensure all legitimate users can be paid</li>
                </ul>

                <p><strong>7.6 Priority Scoring:</strong> Withdrawals are processed based on a priority scoring algorithm that considers rank, star rating, account age, team size, withdrawal history, and other performance factors. Higher-priority users are processed first.</p>

                <div class="highlight-box">
                    <strong>Platform Commitment:</strong> {{ $companyName }} is committed to paying all legitimate earnings to users in good standing. We maintain adequate liquidity reserves, monitor burn rates, and implement sustainable financial practices to ensure long-term platform viability. However, we reserve the right to extend processing times if necessary to maintain platform stability and protect all users.
                </div>
            </div>
        </div>

        <!-- KYC Verification -->
        <div class="section">
            <h2 class="section-title">8. KYC & Identity Verification</h2>
            <div class="section-content">
                <p><strong>8.1 Verification Requirements:</strong> To comply with regulatory requirements and prevent fraud, {{ $companyName }} requires Know Your Customer (KYC) verification for certain transactions and account activities.</p>

                <p><strong>8.2 Required Documents:</strong> KYC verification may require submission of National Identification Number (NIN) card, utility bill showing your name and address, selfie photograph for facial verification, and other documents as required by regulatory authorities or platform policies.</p>

                <p><strong>8.3 Automated Verification:</strong> The platform utilizes Google Cloud Vision API and other AI-powered systems to automatically validate submitted documents. Auto-verification occurs when confidence thresholds are met. Documents that cannot be auto-verified are manually reviewed by administrators.</p>

                <p><strong>8.4 Verification Triggers:</strong> KYC verification is required for withdrawals exceeding {{ \App\Helpers\CountryHelper::formatMoney($settings->kyc_withdrawal_threshold ?? 50000, $settings->country_of_operation) }}, when requested by administrators for fraud prevention, for high-value transactions or suspicious activities, and as required by applicable laws and regulations.</p>

                <p><strong>8.5 Data Privacy:</strong> All KYC documents are stored securely and used solely for verification purposes. We comply with applicable data protection laws and do not share your personal information with third parties except as required by law.</p>

                <p><strong>8.6 False Information:</strong> Submitting false, misleading, or fraudulent KYC information is strictly prohibited and will result in immediate account termination and forfeiture of all balances.</p>
            </div>
        </div>

        <!-- Prohibited Activities -->
        <div class="section page-break">
            <h2 class="section-title">9. Prohibited Activities & Fraud Prevention</h2>
            <div class="section-content">
                <p>The following activities are strictly prohibited and will result in immediate account suspension, termination, and forfeiture of all balances:</p>

                <p><strong>Account Abuse:</strong></p>
                <ul>
                    <li>Creating multiple accounts (multi-accounting)</li>
                    <li>Sharing account credentials or using another person's account</li>
                    <li>Selling, transferring, or renting your account</li>
                    <li>Circumventing account suspension or ban</li>
                </ul>

                <p><strong>Task Fraud:</strong></p>
                <ul>
                    <li>Using bots, scripts, or automation to complete tasks</li>
                    <li>Completing tasks abnormally fast (bot-like behavior)</li>
                    <li>Submitting identical or patterned responses repeatedly</li>
                    <li>Sharing task content or delegating tasks to third parties</li>
                </ul>

                <p><strong>Referral Fraud:</strong></p>
                <ul>
                    <li>Creating fake referral accounts or circular referral chains</li>
                    <li>Referring accounts from the same device or IP address</li>
                    <li>Paying individuals to sign up or manipulating referral networks</li>
                </ul>

                <p><strong>Technical Abuse:</strong></p>
                <ul>
                    <li>Using VPNs or proxies to bypass geographic restrictions</li>
                    <li>Attempting to hack, reverse engineer, or exploit platform vulnerabilities</li>
                    <li>Interfering with platform operations, data mining, or scraping</li>
                    <li>Distributed denial-of-service (DDoS) attacks</li>
                </ul>

                <p><strong>Payment Fraud:</strong></p>
                <ul>
                    <li>Using fraudulent payment methods or stolen payment instruments</li>
                    <li>Initiating chargebacks or payment disputes in bad faith</li>
                    <li>Money laundering or providing false bank/wallet information</li>
                </ul>

                <p><strong>Platform Abuse:</strong></p>
                <ul>
                    <li>Impersonating {{ $companyName }} staff, administrators, or other users</li>
                    <li>Spreading false information, harassment, or abuse</li>
                    <li>Posting illegal, harmful, or offensive content</li>
                    <li>Soliciting users for competing platforms or schemes</li>
                </ul>

                <div class="highlight-box">
                    <strong>Zero Tolerance Policy:</strong> {{ $companyName }} maintains a zero-tolerance policy for fraud and abuse. We employ sophisticated fraud detection systems including VPN/proxy detection, IP tracking, device fingerprinting, behavioral analysis, pattern recognition, and referral chain analysis. Violations are tracked, and repeated offenses result in permanent bans potentially affecting your entire referral network.
                </div>
            </div>
        </div>

        <!-- Account Termination -->
        <div class="section">
            <h2 class="section-title">10. Account Suspension & Termination</h2>
            <div class="section-content">
                <p><strong>10.1 Suspension:</strong> Your account may be temporarily suspended for investigation of suspected fraud or Terms violations, verification of suspicious activities or transactions, non-compliance with KYC requirements, excessive failed login attempts, or user-reported security concerns. During suspension, you cannot access tasks, request withdrawals, or earn commissions.</p>

                <p><strong>10.2 Termination:</strong> We reserve the right to permanently terminate accounts that violate these Terms, engage in fraudulent activities, provide false information or documentation, abuse the platform or other users, or fail to comply with legal or regulatory requirements.</p>

                <p><strong>10.3 Effects of Termination:</strong> Upon account termination:</p>
                <ul>
                    <li>Access to your account will be permanently revoked</li>
                    <li>Pending balances may be forfeited if fraud is confirmed</li>
                    <li>Withdrawable balances from legitimate activity will be paid out after investigation</li>
                    <li>Your referral network may be affected if fraud involves coordinated abuse</li>
                    <li>You are permanently banned from creating new accounts</li>
                </ul>

                <p><strong>10.4 Appeal Process:</strong> If you believe your account was wrongly suspended or terminated, you may submit an appeal to our support team within 14 days including your account information, explanation of circumstances, supporting evidence, and commitment to comply with Terms. Appeal decisions are final and made at {{ $companyName }}'s sole discretion.</p>

                <p><strong>10.5 Voluntary Termination:</strong> You may request account termination at any time by contacting support. Withdrawable balances will be paid out according to standard withdrawal procedures. Pending balances must mature before withdrawal.</p>
            </div>
        </div>

        <!-- Fraud Detection -->
        <div class="section">
            <h2 class="section-title">11. Fraud Detection & Security Measures</h2>
            <div class="section-content">
                <p><strong>11.1 Detection Systems:</strong> {{ $companyName }} employs comprehensive fraud detection systems to protect the platform and legitimate users, including:</p>
                <ul>
                    <li><strong>Registration Fraud:</strong> VPN/proxy detection, IP reuse tracking, device fingerprinting, and referral chain analysis</li>
                    <li><strong>Task Fraud:</strong> Speed analysis, pattern recognition, velocity limits (max {{ $settings->max_tasks_per_hour ?? 15 }} tasks/hour), behavioral analysis, and CAPTCHA integration</li>
                </ul>

                <p><strong>11.2 Monitoring Consent:</strong> By using {{ $companyName }}, you consent to monitoring of your activities, including IP address tracking and geolocation, device fingerprinting and hardware identification, login history and session tracking, task completion patterns and timing, referral network relationships and activities, and transaction history and withdrawal patterns.</p>

                <p><strong>11.3 Progressive Penalties:</strong> Fraud violations result in escalating penalties:</p>
                <ul>
                    <li><strong>First Offense:</strong> Warning notification and temporary task ban (typically 48 hours)</li>
                    <li><strong>Second Offense:</strong> Extended suspension (typically 7 days) and account review</li>
                    <li><strong>Third Offense:</strong> Permanent account termination and balance forfeiture</li>
                    <li><strong>Severe Fraud:</strong> Immediate permanent ban, referral network freeze, and legal action if applicable</li>
                </ul>

                <p><strong>11.4 Network Effects:</strong> Referral chain fraud may result in freezing of entire referral networks if coordinated abuse is detected. Innocent members may be affected and must prove legitimacy to restore access.</p>

                <p><strong>11.5 Data Security:</strong> We implement industry-standard security measures to protect your data, but {{ $companyName }} does not guarantee absolute security. You acknowledge that internet-based platforms inherently carry security risks.</p>
            </div>
        </div>

        <!-- Platform Operations -->
        <div class="section page-break">
            <h2 class="section-title">12. Platform Operations & Availability</h2>
            <div class="section-content">
                <p><strong>12.1 Service Availability:</strong> {{ $companyName }} does not guarantee uninterrupted or error-free service. We may remove or modify services for indefinite periods without notice, implement scheduled or emergency maintenance, adjust task availability based on platform capacity, modify features or functionality, or cease operations entirely at our discretion.</p>

                <p><strong>12.2 Maintenance Mode:</strong> During maintenance periods, platform access may be restricted or unavailable, and task assignments and withdrawals may be paused. Scheduled maintenance is announced in advance when possible. Emergency maintenance may occur without notice.</p>

                <p><strong>12.3 System Requirements:</strong> Use of {{ $companyName }} requires compatible device (computer, tablet, or smartphone), internet connection (high-speed recommended), modern web browser (Chrome, Firefox, Safari, Edge), JavaScript enabled, and cookies and local storage enabled. You are responsible for ensuring compatibility.</p>

                <p><strong>12.4 Liquidity Management:</strong> {{ $companyName }} monitors platform liquidity and burn rates to ensure sustainability. During periods of high withdrawal demand, we may extend withdrawal processing times, prioritize higher-tier rank withdrawals, require additional verification for large withdrawals, or temporarily limit withdrawal amounts to protect all users and maintain platform stability.</p>
            </div>
        </div>

        <!-- Intellectual Property -->
        <div class="section">
            <h2 class="section-title">13. Intellectual Property Rights</h2>
            <div class="section-content">
                <p><strong>13.1 Ownership:</strong> All content, features, functionality, software, designs, graphics, text, and other materials on {{ $companyName }} are owned by or licensed to {{ $companyName }} and are protected by copyright, trademark, patent, and other intellectual property laws.</p>

                <p><strong>13.2 Limited License:</strong> Subject to these Terms, {{ $companyName }} grants you a limited, non-exclusive, non-transferable, revocable license to access and use the platform for personal, non-commercial purposes only.</p>

                <p><strong>13.3 Restrictions:</strong> You may not reproduce, distribute, modify, or create derivative works from platform content; reverse engineer, decompile, or disassemble platform software; remove or alter copyright, trademark, or proprietary notices; use {{ $companyName }} trademarks or branding without written permission; frame or mirror platform content on other websites; or use automated systems to access or scrape platform data.</p>

                <p><strong>13.4 User Content:</strong> By submitting content to {{ $companyName }} (including testimonials, reviews, profile information), you grant us a worldwide, non-exclusive, royalty-free license to use, reproduce, modify, publish, and distribute such content for platform operations and marketing purposes.</p>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="section">
            <h2 class="section-title">14. Disclaimer of Warranties</h2>
            <div class="section-content">
                <p><strong>14.1 "AS IS" BASIS:</strong> {{ strtoupper($companyName) }} IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, TITLE, NON-INFRINGEMENT, ACCURACY, RELIABILITY, COMPLETENESS, UNINTERRUPTED OR ERROR-FREE OPERATION, OR SECURITY OR FREEDOM FROM VIRUSES.</p>

                <p><strong>14.2 No Guarantees:</strong> {{ $companyName }} does not guarantee specific earnings or income levels, availability of tasks at all times, success in building referral networks, withdrawal processing within specific timeframes, continued operation of the platform indefinitely, or compatibility with all devices or browsers.</p>

                <p><strong>14.3 Third-Party Services:</strong> The platform may integrate with third-party services (payment processors, AI providers, verification services). {{ $companyName }} is not responsible for the performance, availability, or policies of third-party services.</p>

                <p><strong>14.4 Investment Disclaimer:</strong> {{ $companyName }} is not an investment platform. Activation payments are subscription fees for platform access, not investments. There is no guarantee of returns, and past performance is not indicative of future results.</p>

                <p><strong>14.5 Regulatory Disclaimer:</strong> Users are responsible for compliance with local laws and regulations regarding online earning platforms, multi-level marketing, taxation, and financial reporting. {{ $companyName }} does not provide legal or tax advice.</p>
            </div>
        </div>

        <!-- Limitation of Liability -->
        <div class="section">
            <h2 class="section-title">15. Limitation of Liability</h2>
            <div class="section-content">
                <p><strong>15.1 MAXIMUM LIABILITY:</strong> TO THE MAXIMUM EXTENT PERMITTED BY LAW, {{ strtoupper($companyName) }} AND ITS DIRECTORS, OFFICERS, EMPLOYEES, AFFILIATES, AGENTS, CONTRACTORS, AND LICENSORS SHALL NOT BE LIABLE FOR:</p>
                <ul>
                    <li>Direct, indirect, incidental, punitive, special, or consequential damages</li>
                    <li>Loss of earnings, expected profits, or business opportunities</li>
                    <li>Business interruption or loss of data</li>
                    <li>Damages arising from unauthorized access or use of your account</li>
                    <li>Damages arising from third-party actions, payment processors, platform errors, bugs, technical issues, suspension, termination, or loss of access</li>
                </ul>

                <p><strong>15.2 Aggregate Liability Cap:</strong> In no event shall {{ $companyName }}'s total aggregate liability exceed the amount you paid to {{ $companyName }} in activation fees during the 12 months preceding the claim.</p>

                <p><strong>15.3 Jurisdictional Limitations:</strong> Some jurisdictions do not allow exclusion or limitation of implied warranties or liability for incidental or consequential damages. In such jurisdictions, {{ $companyName }}'s liability is limited to the greatest extent permitted by law.</p>
            </div>
        </div>

        <!-- Indemnification -->
        <div class="section">
            <h2 class="section-title">16. Indemnification</h2>
            <div class="section-content">
                <p>You agree to indemnify, defend, and hold harmless {{ $companyName }}, its affiliates, and their respective directors, officers, employees, agents, contractors, and licensors from and against any claims, liabilities, damages, losses, costs, and expenses (including reasonable attorneys' fees) arising out of or relating to your breach of these Terms, violation of any law or third-party rights, use or misuse of the platform, fraudulent or illegal activities, content you submit, your referral activities and network, or disputes between you and other users.</p>

                <p>{{ $companyName }} reserves the right to assume exclusive defense and control of any matter subject to indemnification by you, in which case you will cooperate with {{ $companyName }} in asserting any available defenses. This indemnification obligation survives termination of your account and these Terms.</p>
            </div>
        </div>

        <!-- Governing Law -->
        <div class="section page-break">
            <h2 class="section-title">17. Governing Law & Dispute Resolution</h2>
            <div class="section-content">
                <p><strong>17.1 Governing Law:</strong> These Terms of Service shall be governed by and construed in accordance with the laws of {{ $settings->country_of_operation ?? 'Nigeria' }}, without regard to its conflict of law provisions.</p>

                <p><strong>17.2 Jurisdiction:</strong> You agree that any legal action or proceeding arising out of or relating to these Terms or the platform shall be brought exclusively in the courts located in {{ $settings->country_of_operation ?? 'Nigeria' }}. You hereby irrevocably consent to the personal jurisdiction and venue of such courts.</p>

                <p><strong>17.3 Dispute Resolution:</strong> Before initiating legal action, you agree to first attempt to resolve disputes by contacting {{ $companyName }} support, allow 30 days for good-faith negotiation and resolution, and consider non-binding mediation before litigation.</p>

                <p><strong>17.4 Class Action Waiver:</strong> TO THE EXTENT PERMITTED BY LAW, YOU AGREE THAT ANY DISPUTE RESOLUTION PROCEEDINGS WILL BE CONDUCTED ONLY ON AN INDIVIDUAL BASIS AND NOT IN A CLASS, CONSOLIDATED, OR REPRESENTATIVE ACTION.</p>

                <p><strong>17.5 Time Limitation:</strong> Any claim arising from these Terms or the platform must be filed within one (1) year after the cause of action arises, or be forever barred.</p>
            </div>
        </div>

        <!-- Changes to Terms -->
        <div class="section">
            <h2 class="section-title">18. Changes to Terms of Service</h2>
            <div class="section-content">
                <p><strong>18.1 Modification Rights:</strong> {{ $companyName }} reserves the right to modify, amend, or update these Terms at any time without prior notice. Changes become effective immediately upon posting to the platform.</p>

                <p><strong>18.2 Notification:</strong> Significant changes will be communicated via email notification to your registered email address, in-app notification or dashboard announcement, or prominent notice on the platform homepage.</p>

                <p><strong>18.3 Continued Use:</strong> Your continued use of {{ $companyName }} after modifications constitutes acceptance of the revised Terms. If you do not agree to modified Terms, you must immediately cease using the platform and may request account termination.</p>
            </div>
        </div>

        <!-- General Provisions -->
        <div class="section">
            <h2 class="section-title">19. General Provisions</h2>
            <div class="section-content">
                <p><strong>19.1 Entire Agreement:</strong> These Terms, together with our Privacy Policy and any other policies referenced herein, constitute the entire agreement between you and {{ $companyName }} regarding use of the platform and supersede all prior agreements and understandings.</p>

                <p><strong>19.2 Severability:</strong> If any provision of these Terms is found to be invalid, illegal, or unenforceable, that provision shall be construed in a manner consistent with applicable law to reflect the parties' original intent, and the remaining provisions shall remain in full force and effect.</p>

                <p><strong>19.3 Waiver:</strong> {{ $companyName }}'s failure to enforce any right or provision of these Terms shall not constitute a waiver of such right or provision. No waiver shall be effective unless in writing and signed by {{ $companyName }}.</p>

                <p><strong>19.4 Assignment:</strong> You may not assign or transfer your rights or obligations under these Terms without {{ $companyName }}'s prior written consent. {{ $companyName }} may assign these Terms at any time without notice or consent.</p>

                <p><strong>19.5 Force Majeure:</strong> {{ $companyName }} shall not be liable for failure to perform obligations due to causes beyond its reasonable control, including acts of God, war, terrorism, riots, embargoes, acts of civil or military authorities, fires, floods, accidents, pandemics, strikes, or shortages of transportation, facilities, fuel, energy, labor, or materials.</p>

                <p><strong>19.6 Survival:</strong> Provisions that by their nature should survive termination shall survive, including but not limited to: intellectual property rights, disclaimers, limitations of liability, indemnification, and dispute resolution.</p>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="section">
            <h2 class="section-title">20. Contact Information & Support</h2>
            <div class="section-content">
                <p>For questions, concerns, technical support, or inquiries regarding these Terms of Service, contact us at:</p>
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

                <p><strong>Support Hours:</strong> Our support team operates during business hours. Response times vary based on inquiry volume and your rank tier. Higher-tier ranks receive priority support.</p>
            </div>
        </div>

        <!-- Acknowledgment -->
        <div class="section">
            <div class="success-box">
                <p style="margin-bottom: 10px;"><strong>ACKNOWLEDGMENT OF ACCEPTANCE</strong></p>
                <p>BY CLICKING "I ACCEPT" OR BY CREATING AN ACCOUNT, COMPLETING REGISTRATION, OR USING {{ strtoupper($companyName) }}, YOU ACKNOWLEDGE THAT:</p>
                <ol style="margin: 10px 0 10px 20px;">
                    <li>You have read and understood these Terms of Service in their entirety</li>
                    <li>You agree to be legally bound by these Terms and all policies referenced herein</li>
                    <li>You meet all eligibility requirements to use the platform</li>
                    <li>You consent to monitoring, fraud detection, and data processing as described</li>
                    <li>You understand the risks associated with platform use and online earning</li>
                    <li>You will comply with all applicable laws and regulations</li>
                    <li>You accept all disclaimers and limitations of liability</li>
                </ol>
                <p style="margin-top: 10px; font-weight: 600;">If you do not agree to these Terms, you must immediately cease using {{ $companyName }} and may not create an account or access the platform.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="pdf-footer">
        <div class="footer-content">
            <span>© {{ now()->year }} {{ $companyName }}. All rights reserved.</span>
            <span>Document ID: TOS-{{ now()->format('Ymd') }}-{{ substr(md5($user->id), 0, 8) }}</span>
        </div>
    </div>
</body>
</html>
