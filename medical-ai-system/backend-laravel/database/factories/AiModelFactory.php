<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiModelFactory extends Factory
{
    protected $model = AiModel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'version' => fake()->semver(),
            'path' => 'ai-models/test-model.keras',
            'is_active' => false,
            'uploaded_by' => Admin::factory(),
        ];
    }
}
