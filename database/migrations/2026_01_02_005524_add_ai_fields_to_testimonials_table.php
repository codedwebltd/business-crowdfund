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
        Schema::table('testimonials', function (Blueprint $table) {
            $table->text('ai_corrected_message')->nullable()->after('message');
            $table->json('ai_analysis')->nullable()->after('ai_corrected_message');
            $table->boolean('auto_approved')->default(false)->after('ai_analysis');
            $table->boolean('trash_testimonial')->default(false)->after('auto_approved');
            $table->boolean('negative_testimonial')->default(false)->after('trash_testimonial');
            $table->boolean('complaint_testimonial')->default(false)->after('negative_testimonial');
            $table->timestamp('ai_processed_at')->nullable()->after('complaint_testimonial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn([
                'ai_corrected_message',
                'ai_analysis',
                'auto_approved',
                'trash_testimonial',
                'negative_testimonial',
                'complaint_testimonial',
                'ai_processed_at'
            ]);
        });
    }
};
