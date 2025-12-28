<?php

namespace App\Services\PDFs;

use App\Models\GlobalSetting;
use App\Models\Withdrawal;
use Barryvdh\DomPDF\Facade\Pdf;

class WithdrawalReceiptPDF extends BasePDF
{
    protected Withdrawal $withdrawal;

    public function __construct(Withdrawal $withdrawal)
    {
        parent::__construct($withdrawal->user);
        $this->withdrawal = $withdrawal;
        $this->watermarkText = 'OFFICIAL RECEIPT';
    }

    public function generate(): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView($this->getViewName(), $this->getData());
        return $pdf->download("Withdrawal-Receipt-{$this->withdrawal->transaction_reference}.pdf");
    }

    public function output(): string
    {
        $pdf = Pdf::loadView($this->getViewName(), $this->getData());
        return $pdf->output();
    }

    protected function getViewName(): string
    {
        return 'pdfs.withdrawal-receipt';
    }

    protected function getData(): array
    {
        return [
            'baseCSS' => $this->getBaseCSS(),
            'logo' => $this->getLogo(),
            'watermark' => $this->watermarkText,
            'companyName' => $this->settings->app_name,
            'companyEmail' => $this->settings->support_email,
            'companyPhone' => $this->settings->support_phone,
            'companyAddress' => $this->settings->app_url,
            'withdrawal' => $this->withdrawal,
            'user' => $this->user,
            'receiptNumber' => $this->withdrawal->transaction_reference ?? 'WD-' . strtoupper(substr($this->withdrawal->id, 0, 8)),
            'receiptDate' => $this->withdrawal->processed_at ?? $this->withdrawal->requested_at,
            'currencySymbol' => $this->settings->platform_currency ?? '₦',
        ];
    }

    /**
     * Get base CSS matching app theme
     */
    protected function getBaseCSS(): string
    {
        return <<<CSS
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #1f2937;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        padding: 20px;
    }
    .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 120px;
        font-weight: bold;
        color: rgba(168, 85, 247, 0.05);
        z-index: -1;
        white-space: nowrap;
    }
    .pdf-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .pdf-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        padding: 40px 30px;
        text-align: center;
        color: white;
    }
    .logo-container { margin-bottom: 15px; }
    .logo-container img { max-width: 120px; height: auto; }
    .company-name { font-size: 28px; font-weight: bold; margin-bottom: 5px; }
    .company-tagline { font-size: 14px; opacity: 0.9; }
    .receipt-title {
        text-align: center;
        font-size: 32px;
        font-weight: bold;
        color: #7c3aed;
        margin: 30px 0 20px;
        text-transform: uppercase;
    }
    .receipt-number {
        text-align: center;
        font-size: 16px;
        color: #6b7280;
        margin-bottom: 30px;
    }
    .section {
        padding: 0 30px 20px;
    }
    .info-grid {
        display: table;
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    .info-row {
        display: table-row;
    }
    .info-label {
        display: table-cell;
        padding: 12px 15px;
        background: #f9fafb;
        font-weight: 600;
        color: #4b5563;
        width: 40%;
        border-bottom: 1px solid #e5e7eb;
    }
    .info-value {
        display: table-cell;
        padding: 12px 15px;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
    }
    .amount-box {
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(168, 85, 247, 0.1));
        border: 2px solid #a855f7;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        margin: 30px 0;
    }
    .amount-label {
        font-size: 14px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }
    .amount-value {
        font-size: 48px;
        font-weight: bold;
        color: #7c3aed;
    }
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-pending { background: #fed7aa; color: #92400e; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .footer {
        background: #f9fafb;
        padding: 30px;
        text-align: center;
        font-size: 12px;
        color: #6b7280;
        border-top: 2px solid #e5e7eb;
    }
    .signature-section {
        margin-top: 60px;
        padding: 30px;
        border-top: 2px dashed #e5e7eb;
    }
    .signature-line {
        border-top: 2px solid #1f2937;
        width: 250px;
        margin: 10px auto 5px;
    }
</style>
CSS;
    }
}
