<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Jobs\ProcessTestimonialReview;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => [
                'required',
                'string',
                'min:15', // Minimum 15 words requirement
                function ($attribute, $value, $fail) {
                    $wordCount = str_word_count($value);
                    if ($wordCount < 15) {
                        $fail("Your testimonial must be at least 15 words. You currently have {$wordCount} words.");
                    }

                    // Check for actual sentences (not just random words)
                    if (strlen(trim($value)) < 60) {
                        $fail('Please provide a meaningful testimonial with complete sentences.');
                    }

                    // Check if it's just repeated words
                    $words = explode(' ', strtolower(preg_replace('/[^a-z0-9\s]/i', '', $value)));
                    $uniqueWords = array_unique($words);
                    if (count($uniqueWords) < (count($words) * 0.5)) {
                        $fail('Please write a genuine testimonial, not repeated words.');
                    }
                },
            ],
        ]);

        $user = auth()->user();

        // Check if user already has a pending testimonial
        $existingPending = $user->testimonials()->where('status', 'PENDING')->first();
        if ($existingPending) {
            return back()->withErrors(['message' => 'You already have a pending testimonial under review.']);
        }

        // Create testimonial
        $testimonial = Testimonial::create([
            'user_id' => $user->id,
            'name' => $user->full_name,
            'message' => $request->message,
            'status' => 'PENDING',
        ]);

        // Dispatch AI review job immediately
        ProcessTestimonialReview::dispatch($testimonial);

        return back()->with('success', 'Thank you! Your testimonial is being processed by our AI system. You\'ll receive a notification shortly.');
    }
}
