<?php

namespace Database\Factories;

use App\Models\Prediction;
use App\Models\PredictionProbability;
use Illuminate\Database\Eloquent\Factories\Factory;

class PredictionProbabilityFactory extends Factory
{
    protected $model = PredictionProbability::class;

    public function definition(): array
    {
        return [
            'prediction_id' => Prediction::factory(),
            'class_name' => fake()->randomElement(['Normal', 'Osteopenia', 'Osteoporosis']),
            'probability' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
