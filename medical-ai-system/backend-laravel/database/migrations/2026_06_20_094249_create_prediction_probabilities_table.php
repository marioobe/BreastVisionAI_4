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
        Schema::create('prediction_probabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prediction_id')->constrained()->cascadeOnDelete();
            $table->string('class_name');
            $table->decimal('probability', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediction_probabilities');
    }
};
