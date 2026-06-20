<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'nik' => fake()->numerify('################'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
        ];
    }
}
