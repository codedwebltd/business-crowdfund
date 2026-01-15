<?php

namespace App\Services\PDFs;

use App\Models\GlobalSetting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

abstract class BasePDF
{
    protected GlobalSetting $settings;
    protected User $user;
    protected string $watermarkText = '';

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->settings = GlobalSetting::first();
    }

    /**
     * Generate PDF and return as download or stream
     */
    abstract public function generate(): \Illuminate\Http\Response;

    /**
     * Get PDF view name
     */
    abstract protected function getViewName(): string;

    /**
     * Get PDF data for view
     */
    abstract protected function getData(): array;

    /**
     * Get dynamic logo (SVG if no image uploaded)
     */
    protected function getLogo(): string
    {
        if ($this->settings->site_logo) {
            // Use uploaded logo
            return $this->settings->site_logo;
        }

        // Generate beautiful SVG logo dynamically
        $appName = $this->settings->app_name ?? 'CrowdPower';
        $initials = $this->getInitials($appName);

        return $this->generateSVGLogo($initials, $appName);
    }

    /**
     * Get initials from app name (only 1 character to avoid overflow)
     */
    private function getInitials(string $name): string
    {
        // Just use first letter to avoid text overflow in circle
        return strtoupper(substr($name, 0, 1));
    }

    /**
     * Generate beautiful SVG logo matching our purple glassmorphism theme
     */
    private function generateSVGLogo(string $initials, string $fullName): string
    {
        $svg = <<<SVG
<svg width="120" height="120" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#7c3aed;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:1" />
        </linearGradient>
        <filter id="glow">
            <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
            <feMerge>
                <feMergeNode in="coloredBlur"/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
    </defs>

    <!-- Background circle with gradient -->
    <circle cx="60" cy="60" r="55" fill="url(#grad1)" filter="url(#glow)" opacity="0.9"/>

    <!-- Inner circle for depth -->
    <circle cx="60" cy="60" r="45" fill="none" stroke="white" stroke-width="2" opacity="0.3"/>

    <!-- Initials -->
    <text x="60" y="75" font-family="Arial, sans-serif" font-size="36" font-weight="bold"
          fill="white" text-anchor="middle" letter-spacing="2">
        {$initials}
    </text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get base CSS for all PDFs (matches our purple glassmorphism UI)
     */
    protected function getBaseCSS(): string
    {
        return <<<CSS
        <style>
            @page {
                margin: 0;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                color: #1e293b;
                line-height: 1.6;
                position: relative;
            }

            .pdf-container {
                padding: 40px 60px;
                position: relative;
                min-height: 100vh;
            }

            /* Watermark */
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 90px;
                font-weight: 900;
                color: rgba(124, 58, 237, 0.15);
                text-transform: uppercase;
                letter-spacing: 8px;
                z-index: -1;
                white-space: nowrap;
                font-family: 'Arial Black', sans-serif;
            }

            /* Header */
            .pdf-header {
                border-bottom: 3px solid #7c3aed;
                padding-bottom: 20px;
                margin-bottom: 30px;
                background: linear-gradient(135deg, rgba(124, 58, 237, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
                padding: 20px;
                border-radius: 8px;
            }

            .logo-container {
                text-align: center;
                margin-bottom: 15px;
            }

            .logo-container img {
                max-width: 120px;
                height: auto;
            }

            .company-name {
                font-size: 28px;
                font-weight: bold;
                color: #7c3aed;
                text-align: center;
                margin-bottom: 5px;
            }

            .company-tagline {
                text-align: center;
                color: #64748b;
                font-size: 14px;
            }

            /* Document Title */
            .doc-title {
                font-size: 24px;
                font-weight: bold;
                color: #1e293b;
                margin: 30px 0 20px;
                border-left: 4px solid #7c3aed;
                padding-left: 15px;
            }

            /* Content sections */
            .section {
                margin-bottom: 25px;
                page-break-inside: avoid;
            }

            .section-title {
                font-size: 18px;
                font-weight: 600;
                color: #7c3aed;
                margin-bottom: 10px;
                border-bottom: 2px solid rgba(124, 58, 237, 0.2);
                padding-bottom: 5px;
            }

            .section-content {
                font-size: 12px;
                color: #475569;
                text-align: justify;
            }

            /* Info box (for user details) */
            .info-box {
                background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
                border-left: 4px solid #7c3aed;
                padding: 15px;
                margin: 20px 0;
                border-radius: 4px;
            }

            .info-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
                font-size: 12px;
            }

            .info-label {
                font-weight: 600;
                color: #1e293b;
            }

            .info-value {
                color: #64748b;
            }

            /* Lists */
            ul, ol {
                margin-left: 20px;
                margin-bottom: 15px;
            }

            li {
                margin-bottom: 8px;
                font-size: 12px;
                color: #475569;
            }

            /* Highlight boxes */
            .highlight-box {
                background: #fef3c7;
                border-left: 4px solid #f59e0b;
                padding: 12px;
                margin: 15px 0;
                border-radius: 4px;
                font-size: 12px;
            }

            .success-box {
                background: #d1fae5;
                border-left: 4px solid #10b981;
                padding: 12px;
                margin: 15px 0;
                border-radius: 4px;
                font-size: 12px;
            }

            /* Footer */
            .pdf-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                border-top: 2px solid rgba(124, 58, 237, 0.2);
                padding: 15px 60px;
                background: white;
                font-size: 10px;
                color: #94a3b8;
            }

            .footer-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            /* Signature section */
            .signature-section {
                margin-top: 50px;
                display: flex;
                justify-content: space-between;
            }

            .signature-box {
                width: 45%;
            }

            .signature-line {
                border-top: 1px solid #cbd5e1;
                margin-top: 40px;
                padding-top: 8px;
                text-align: center;
                font-size: 11px;
                color: #64748b;
            }

            /* Table styling */
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }

            th {
                background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
                color: white;
                padding: 12px;
                text-align: left;
                font-size: 12px;
                font-weight: 600;
            }

            td {
                padding: 10px 12px;
                border-bottom: 1px solid #e2e8f0;
                font-size: 11px;
                color: #475569;
            }

            tr:hover {
                background: rgba(124, 58, 237, 0.05);
            }

            /* Strong emphasis */
            strong {
                color: #7c3aed;
            }

            /* Page break utilities */
            .page-break {
                page-break-after: always;
            }
        </style>
CSS;
    }

    /**
     * Create PDF instance with settings
     */
    protected function createPDF(string $view, array $data): \Barryvdh\DomPDF\PDF
    {
        $data['logo'] = $this->getLogo();
        $data['settings'] = $this->settings;
        $data['user'] = $this->user;
        $data['watermark'] = $this->watermarkText;
        $data['baseCSS'] = $this->getBaseCSS();

        return Pdf::loadView($view, array_merge($data, $this->getData()))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
    }
}
