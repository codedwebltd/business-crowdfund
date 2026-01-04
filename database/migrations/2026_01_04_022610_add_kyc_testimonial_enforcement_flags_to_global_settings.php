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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->boolean('enable_kyc_on_first_withdrawal')->default(false)->after('kyc_requirements');
            $table->boolean('testimonial_required_for_withdrawal')->default(false)->after('require_testimonial_first_withdrawal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['enable_kyc_on_first_withdrawal', 'testimonial_required_for_withdrawal']);
        });
    }
};
