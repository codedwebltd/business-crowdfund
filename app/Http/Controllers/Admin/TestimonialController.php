<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\GlobalSetting;
use App\Services\NotificationService;
use App\Services\GroqService;
use App\Helpers\CountryHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with('user')
            ->latest()
            ->get();

        $stats = [
            'pending' => Testimonial::where('status', 'PENDING')->count(),
            'approved' => Testimonial::where('status', 'APPROVED')->count(),
            'rejected' => Testimonial::where('status', 'REJECTED')->count(),
            'auto_approved' => Testimonial::where('auto_approved', true)->count(),
            'trash' => Testimonial::where('trash_testimonial', true)->count(),
            'negative' => Testimonial::where('negative_testimonial', true)->count(),
            'complaint' => Testimonial::where('complaint_testimonial', true)->count(),
        ];

        return Inertia::render('Admin/Testimonials', [
            'testimonials' => $testimonials,
            'stats' => $stats,
            'settings' => GlobalSetting::first(),
        ]);
    }

    public function approve($id, NotificationService $notificationService)
    {
        $testimonial = Testimonial::findOrFail($id);

        $testimonial->update([
            'status' => 'APPROVED',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'admin_notes' => 'Manually approved by admin',
        ]);

        // Send notification to user
        $notificationService->send(
            $testimonial->user,
            'testimonial_approved',
            [
                'testimonial_id' => $testimonial->id,
                'auto_approved' => false,
            ]
        );

        return back()->with('success', 'Testimonial approved successfully!');
    }

    public function reject(Request $request, $id, NotificationService $notificationService)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $testimonial = Testimonial::findOrFail($id);

        $testimonial->update([
            'status' => 'REJECTED',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'admin_notes' => $request->reason,
        ]);

        // Send notification to user
        $notificationService->send(
            $testimonial->user,
            'testimonial_rejected',
            [
                'testimonial_id' => $testimonial->id,
                'reason' => $request->reason,
            ]
        );

        return back()->with('success', 'Testimonial rejected successfully!');
    }

    public function generateWithAI(Request $request, GroqService $groq)
    {
        $validated = $request->validate([
            'count' => 'required|integer|min:1|max:20',
        ]);

        $settings = GlobalSetting::first();
        $country = CountryHelper::getByAlpha3($settings->country_of_operation ?? 'NGA');
        $countryName = $country['name'] ?? 'Nigeria';

        // Get common names for the country
        $names = $this->getCountryNames($settings->country_of_operation ?? 'NGA');

        $systemPrompt = "You are a testimonial generator for a task-earning platform. Generate realistic, authentic testimonials from users in {$countryName}. Use natural, conversational language with local expressions and currency (₦). Each testimonial should feel genuine and personal.";

        $userPrompt = "Generate {$validated['count']} unique testimonials from users in {$countryName} who have earned money on a task/gig platform.

Requirements:
- Use natural, friendly, conversational tone
- Include specific earnings amounts (₦2,000 - ₦100,000 range)
- Mention realistic timeframes (days, weeks, months)
- Use local expressions and natural language
- Mix different user experiences (students, workers, housewives, etc.)
- Keep each testimonial 2-4 sentences
- Make them feel authentic and human
- Vary the enthusiasm levels (some excited, some calm and factual)

Format each testimonial as:
[NAME]|||[MESSAGE]

Use common {$countryName} names. Separate each testimonial with a blank line.";

        $response = $groq->generate($systemPrompt, $userPrompt, 4000, 0.9);

        if (!$response) {
            return response()->json(['error' => 'Failed to generate testimonials'], 500);
        }

        // Parse testimonials
        $testimonials = [];
        $lines = explode("\n\n", trim($response));

        foreach ($lines as $line) {
            if (strpos($line, '|||') !== false) {
                list($name, $message) = explode('|||', $line, 2);
                $testimonials[] = [
                    'name' => trim($name),
                    'message' => trim($message),
                ];
            }
        }

        // Fallback: use country names if parsing failed
        if (empty($testimonials) && count($names) > 0) {
            $parts = preg_split('/\n+/', trim($response));
            foreach (array_slice($parts, 0, $validated['count']) as $idx => $message) {
                if (!empty(trim($message))) {
                    $testimonials[] = [
                        'name' => $names[$idx % count($names)],
                        'message' => trim($message),
                    ];
                }
            }
        }

        return response()->json([
            'testimonials' => array_slice($testimonials, 0, $validated['count']),
        ]);
    }

    public function bulkPublish(Request $request)
    {
        $validated = $request->validate([
            'testimonials' => 'required|array|min:1',
            'testimonials.*.name' => 'required|string|max:255',
            'testimonials.*.message' => 'required|string|min:20',
        ]);

        $created = [];

        foreach ($validated['testimonials'] as $data) {
            $created[] = Testimonial::create([
                'user_id' => null,
                'name' => $data['name'],
                'message' => $data['message'],
                'ai_corrected_message' => $data['message'],
                'status' => 'APPROVED',
                'auto_approved' => true,
                'trash_testimonial' => false,
                'negative_testimonial' => false,
                'complaint_testimonial' => false,
            ]);
        }

        return back()->with('success', count($created) . ' testimonials published successfully!');
    }

    private function getCountryNames($countryCode)
    {
        $names = [
            'NGA' => ['Chinedu Okafor', 'Blessing Adeyemi', 'Emmanuel Eze', 'Fatima Ibrahim', 'David Okonkwo',
                     'Ngozi Nwankwo', 'Ibrahim Musa', 'Grace Oluwaseun', 'Ahmed Bello', 'Chioma Okoro',
                     'Oluwaseun Adebayo', 'Aisha Mohammed', 'Tunde Bakare', 'Amara Nwosu', 'Yusuf Abdullahi'],
            'KEN' => ['James Mwangi', 'Mary Wanjiku', 'Peter Ochieng', 'Faith Njeri', 'David Kamau',
                     'Grace Akinyi', 'John Kipchoge', 'Lucy Wambui', 'Michael Otieno', 'Sarah Muthoni'],
            'GHA' => ['Kwame Mensah', 'Abena Owusu', 'Kofi Asante', 'Ama Boateng', 'Kwesi Osei',
                     'Akua Darko', 'Yaw Agyeman', 'Efua Appiah', 'Fiifi Quartey', 'Adjoa Kuffour'],
            'ZAF' => ['Thabo Ndlovu', 'Nomsa Dlamini', 'Sipho Nkosi', 'Zanele Mthembu', 'Bongani Khumalo',
                     'Lindiwe Zulu', 'Mandla Shabalala', 'Thandiwe Mokoena', 'Jabu Radebe', 'Ayanda Ngubane'],
        ];

        return $names[$countryCode] ?? $names['NGA'];
    }
}
