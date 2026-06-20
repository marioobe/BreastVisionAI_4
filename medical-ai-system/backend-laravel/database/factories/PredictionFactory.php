<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Prediction;
use Illuminate\Database\Eloquent\Factories\Factory;

class PredictionFactory extends Factory
{
    protected $model = Prediction::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'image_path' => 'predictions/test-image.jpg',
            'prediction' => fake()->randomElement(['Normal', 'Osteopenia', 'Osteoporosis']),
            'confidence' => fake()->randomFloat(2, 70, 99),
            'consent_approved' => true,
        ];
    }
}
