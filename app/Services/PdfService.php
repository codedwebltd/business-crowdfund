<?php

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\User;
use App\Models\Withdrawal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    protected GlobalSetting $settings;

    public function __construct()
    {
        $this->settings = GlobalSetting::first();
    }

    /**
     * Generate withdrawal receipt PDF
     *
     * @param Withdrawal $withdrawal
     * @return string Path to generated PDF
     */
    public function generateWithdrawalReceipt(Withdrawal $withdrawal): string
    {
        $user = $withdrawal->user;

        $data = [
            'withdrawal' => $withdrawal,
            'user' => $user,
            'settings' => $this->settings,
            'generatedAt' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('pdfs.withdrawal-receipt', $data);

        // Add watermark
        $pdf->setPaper('a4', 'portrait');

        // Generate filename
        $filename = 'withdrawal-receipt-' . $withdrawal->id . '-' . now()->timestamp . '.pdf';
        $path = 'pdfs/withdrawals/' . $filename;

        // Save to storage
        Storage::put($path, $pdf->output());

        return storage_path('app/' . $path);
    }

    /**
     * Generate multiple PDFs for a user (e.g., terms, contract, etc.)
     *
     * @param User $user
     * @param array $types ['terms', 'contract', 'welcome']
     * @return array Paths to generated PDFs
     */
    public function generateUserDocuments(User $user, array $types = ['terms', 'contract']): array
    {
        $paths = [];

        foreach ($types as $type) {
            $paths[$type] = $this->{"generate" . ucfirst($type) . "Pdf"}($user);
        }

        return $paths;
    }

    /**
     * Generate Terms & Conditions PDF
     */
    protected function generateTermsPdf(User $user): string
    {
        $data = [
            'user' => $user,
            'settings' => $this->settings,
            'generatedAt' => now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('pdfs.terms', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'terms-conditions-' . $user->id . '.pdf';
        $path = 'pdfs/terms/' . $filename;

        Storage::put($path, $pdf->output());

        return storage_path('app/' . $path);
    }

    /**
     * Generate Contract PDF
     */
    protected function generateContractPdf(User $user): string
    {
        $data = [
            'user' => $user,
            'settings' => $this->settings,
            'generatedAt' => now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('pdfs.contract', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'contract-' . $user->id . '.pdf';
        $path = 'pdfs/contracts/' . $filename;

        Storage::put($path, $pdf->output());

        return storage_path('app/' . $path);
    }
}
