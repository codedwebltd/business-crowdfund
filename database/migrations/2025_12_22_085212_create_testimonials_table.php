<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable(); // Name if anonymous
            $table->text('message'); // Minimum 50 words
            $table->text('ai_corrected_message')->nullable(); // AI grammar-corrected version
            $table->json('ai_analysis')->nullable(); // Full AI analysis result
            $table->boolean('auto_approved')->default(false); // AI auto-approved
            $table->boolean('trash_testimonial')->default(false); // AI detected trash
            $table->boolean('negative_testimonial')->default(false); // AI detected negative
            $table->boolean('complaint_testimonial')->default(false); // AI detected complaint
            $table->timestamp('ai_processed_at')->nullable(); // When AI analyzed it
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->string('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
