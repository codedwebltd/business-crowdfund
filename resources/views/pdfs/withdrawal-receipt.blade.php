<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Withdrawal Receipt - {{ $receiptNumber }}</title>
    {!! $baseCSS !!}
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">{{ $watermark }}</div>

    <div class="pdf-container">
        <!-- Header -->
        <div class="pdf-header">
            <div class="company-name">{{ $companyName }}</div>
            <div class="company-tagline">Official Withdrawal Receipt</div>
        </div>

        <!-- Receipt Title -->
        <h1 class="receipt-title">WITHDRAWAL RECEIPT</h1>
        <div class="receipt-number">Receipt #{{ $receiptNumber }}</div>

        <!-- Amount Box -->
        <div class="section">
            <div class="amount-box">
                <div class="amount-label">Total Amount</div>
                <div class="amount-value">{{ $currencySymbol }}{{ number_format($withdrawal->amount_requested, 2) }}</div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="section">
            <h3 style="color: #7c3aed; margin-bottom: 15px; font-size: 18px;">Transaction Details</h3>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Receipt Number</div>
                    <div class="info-value">{{ $receiptNumber }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date & Time</div>
                    <div class="info-value">{{ $receiptDate->format('F d, Y - h:i A') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Transaction Status</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower($withdrawal->status) }}">
                            {{ $withdrawal->status }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{{ ucfirst(strtolower($withdrawal->payment_method)) }}</div>
                </div>
                @if($withdrawal->transaction_reference)
                <div class="info-row">
                    <div class="info-label">Transaction Reference</div>
                    <div class="info-value">{{ $withdrawal->transaction_reference }}</div>
                </div>
                @endif
                @if($withdrawal->processing_hours)
                <div class="info-row">
                    <div class="info-label">Processing Time</div>
                    <div class="info-value">{{ $withdrawal->processing_hours }} hours</div>
                </div>
                @endif
            </div>
        </div>

        <!-- User Details -->
        <div class="section">
            <h3 style="color: #7c3aed; margin-bottom: 15px; font-size: 18px;">Beneficiary Details</h3>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $user->full_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $user->phone_number }}</div>
                </div>
                @if($user->email)
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">User ID</div>
                    <div class="info-value">{{ substr($user->id, 0, 8) }}</div>
                </div>
            </div>
        </div>

        <!-- Payment Destination -->
        <div class="section">
            <h3 style="color: #7c3aed; margin-bottom: 15px; font-size: 18px;">Payment Destination</h3>
            <div class="info-grid">
                @if($withdrawal->payment_method === 'BANK')
                    <div class="info-row">
                        <div class="info-label">Bank Name</div>
                        <div class="info-value">{{ $withdrawal->bank_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Account Number</div>
                        <div class="info-value">{{ $withdrawal->account_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Account Name</div>
                        <div class="info-value">{{ $withdrawal->account_name }}</div>
                    </div>
                @else
                    <div class="info-row">
                        <div class="info-label">Coin</div>
                        <div class="info-value">{{ $withdrawal->wallet_details['coin_name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Network</div>
                        <div class="info-value">{{ $withdrawal->wallet_details['coin_network'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Wallet Address</div>
                        <div class="info-value" style="word-break: break-all; font-size: 11px;">{{ $withdrawal->wallet_details['wallet_address'] ?? 'N/A' }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Amount Breakdown -->
        @if($withdrawal->meta_data)
        <div class="section">
            <h3 style="color: #7c3aed; margin-bottom: 15px; font-size: 18px;">Amount Breakdown</h3>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Original Request</div>
                    <div class="info-value">{{ $currencySymbol }}{{ number_format($withdrawal->meta_data['original_amount'] ?? 0, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Withdrawal Rate</div>
                    <div class="info-value">{{ ($withdrawal->meta_data['withdrawal_rate'] ?? 1) * 100 }}%</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Token Price</div>
                    <div class="info-value">{{ $currencySymbol }}{{ number_format($withdrawal->meta_data['token_price'] ?? 0, 2) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tokens Deducted</div>
                    <div class="info-value">{{ number_format($withdrawal->meta_data['tokens_deducted'] ?? 0, 4) }} CROW</div>
                </div>
                <div class="info-row" style="background: #f3f4f6; font-weight: bold;">
                    <div class="info-label" style="background: #e5e7eb;">Final Amount</div>
                    <div class="info-value" style="color: #7c3aed; font-size: 18px;">{{ $currencySymbol }}{{ number_format($withdrawal->amount_requested, 2) }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <p style="text-align: center; color: #6b7280; margin-bottom: 30px; font-style: italic;">
                This is a computer-generated receipt and does not require a physical signature.
            </p>
            <div style="text-align: center;">
                <div class="signature-line"></div>
                <p style="margin-top: 10px; font-weight: 600; color: #1f2937;">{{ $companyName }}</p>
                <p style="font-size: 12px; color: #6b7280;">Authorized System</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin-bottom: 10px; font-weight: 600; color: #1f2937;">{{ $companyName }}</p>
            <p>{{ $companyAddress }}</p>
            <p>Email: {{ $companyEmail }} | Phone: {{ $companyPhone }}</p>
            <p style="margin-top: 15px; font-size: 11px;">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
            <p style="margin-top: 10px; font-size: 10px; color: #9ca3af;">
                © {{ now()->year }} {{ $companyName }}. All rights reserved.<br>
                This receipt is proof of transaction and should be kept for your records.
            </p>
        </div>
    </div>
</body>
</html>
