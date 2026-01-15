<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DocumentationController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Documentation/Index', [
            'docs' => $this->getAllDocs(false) // Don't load content for index
        ]);
    }

    public function show($slug)
    {
        // Global Settings uses a dedicated Vue component
        if ($slug === 'global-settings') {
            return Inertia::render('Admin/Documentation/GlobalSettings');
        }

        $allDocs = $this->getAllDocs(false); // For sidebar, no content needed
        $doc = $this->getDocBySlug($slug); // Get single doc with content

        if (!$doc) abort(404);

        return Inertia::render('Admin/Documentation/Show', [
            'doc' => $doc,
            'allDocs' => $allDocs
        ]);
    }

    protected function getAllDocs($includeContent = false)
    {
        $docs = [
            [
                'title' => 'Global Settings - Complete Reference',
                'slug' => 'global-settings',
                'category' => 'Configuration',
                'icon' => '⚙️',
                'description' => 'Complete guide to all 15 settings tabs including AI, financial, fraud detection, KYC, notifications, and more',
            ],
            [
                'title' => 'Groq AI Task Generator - Complete Setup',
                'slug' => 'groq-task-generator',
                'category' => 'AI & Automation',
                'icon' => '🤖',
                'description' => 'Automatically generate tasks using Groq AI - complete beginner-friendly guide',
            ],
            [
                'title' => 'Google Vision API - KYC Auto-Verification',
                'slug' => 'google-vision-kyc',
                'category' => 'Integrations',
                'icon' => '👁️',
                'description' => 'Automatic KYC document verification using Google Cloud Vision API',
            ],
            [
                'title' => 'Queue System & Job Processing',
                'slug' => 'queue-system',
                'category' => 'System Architecture',
                'icon' => '⚙️',
                'description' => 'Understanding job queues, workers, and commission processing system',
            ],
        ];

        // Only load content if requested
        if ($includeContent) {
            foreach ($docs as &$doc) {
                $doc['content'] = $this->loadDocContent($doc['slug']);
            }
        }

        return $docs;
    }

    protected function getDocBySlug($slug)
    {
        $allDocs = $this->getAllDocs(false);
        $doc = collect($allDocs)->firstWhere('slug', $slug);

        if (!$doc) {
            return null;
        }

        // Load content for this specific doc
        $doc['content'] = $this->loadDocContent($slug);

        return $doc;
    }

    protected function loadDocContent($slug)
    {
        $filePath = resource_path('docs/' . $slug . '.md');

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            // Ensure it's a valid string
            return $content !== false ? $content : '# Error\n\nCould not read documentation file.';
        }

        return '# Content Not Found\n\nThe documentation file could not be found.';
    }
}
