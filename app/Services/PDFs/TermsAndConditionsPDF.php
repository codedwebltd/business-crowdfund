<?php

namespace App\Services\PDFs;

class TermsAndConditionsPDF extends BasePDF
{
    protected string $watermarkText = 'CROWDPOWER TERMS';

    protected function getViewName(): string
    {
        return 'pdfs.terms-and-conditions';
    }

    protected function getData(): array
    {
        return [
            'documentTitle' => 'Terms and Conditions',
            'effectiveDate' => now()->format('F d, Y'),
            'companyName' => $this->settings->app_name ?? 'CrowdPower',
        ];
    }

    public function generate(): \Illuminate\Http\Response
    {
        $pdf = $this->createPDF($this->getViewName(), $this->getData());

        return $pdf->download('Terms-and-Conditions-' . $this->user->referral_code . '.pdf');
    }

    public function stream()
    {
        $pdf = $this->createPDF($this->getViewName(), $this->getData());

        return $pdf->stream();
    }

    public function output(): string
    {
        $pdf = $this->createPDF($this->getViewName(), $this->getData());

        return $pdf->output();
    }
}
