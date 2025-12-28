<?php

namespace App\Services\PDFs;

use App\Helpers\CountryHelper;

class WelcomeContractPDF extends BasePDF
{
    protected string $watermarkText = 'OFFICIAL CONTRACT';

    protected function getViewName(): string
    {
        return 'pdfs.welcome-contract';
    }

    protected function getData(): array
    {
        $plan = $this->user->plan;
        $rank = $this->user->rank;

        return [
            'documentTitle' => 'Partnership Agreement',
            'contractDate' => now()->format('F d, Y'),
            'companyName' => $this->settings->app_name ?? 'CrowdPower',
            'userName' => $this->user->full_name,
            'userEmail' => $this->user->email,
            'userPhone' => $this->user->phone_number,
            'referralCode' => $this->user->referral_code,
            'planName' => $plan?->name ?? 'Not Yet Activated',
            'rankName' => $rank?->name ?? 'Bronze (After Activation)',
            'maxDailyTasks' => $plan?->max_daily_tasks ?? 'N/A',
            'taskMultiplier' => $plan?->task_earning_multiplier ?? '1.0',
            'activationAmount' => $plan ? CountryHelper::formatMoney($plan->price, $this->settings->country_of_operation) : 'N/A',
        ];
    }

    public function generate(): \Illuminate\Http\Response
    {
        $pdf = $this->createPDF($this->getViewName(), $this->getData());

        return $pdf->download('Welcome-Contract-' . $this->user->referral_code . '.pdf');
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
