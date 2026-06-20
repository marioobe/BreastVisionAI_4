<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE predictions DROP FOREIGN KEY predictions_ai_model_id_foreign');
            DB::statement('ALTER TABLE predictions MODIFY ai_model_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE predictions ADD CONSTRAINT predictions_ai_model_id_foreign FOREIGN KEY (ai_model_id) REFERENCES ai_models(id) ON DELETE CASCADE');
        } elseif (DB::getDriverName() === 'sqlite') {
            Schema::table('predictions', function ($table) {
                $table->unsignedBigInteger('ai_model_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE predictions DROP FOREIGN KEY predictions_ai_model_id_foreign');
            DB::statement('ALTER TABLE predictions MODIFY ai_model_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE predictions ADD CONSTRAINT predictions_ai_model_id_foreign FOREIGN KEY (ai_model_id) REFERENCES ai_models(id) ON DELETE CASCADE');
        }
    }
};
