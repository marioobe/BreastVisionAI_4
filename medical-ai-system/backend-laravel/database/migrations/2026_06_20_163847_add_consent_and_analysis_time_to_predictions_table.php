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
        Schema::table('predictions', function (Blueprint $table) {
            $table->boolean('consent_approved')->default(false)->after('confidence');
            $table->decimal('analysis_time', 8, 2)->nullable()->after('consent_approved');
        });
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropColumn(['consent_approved', 'analysis_time']);
        });
    }
};
