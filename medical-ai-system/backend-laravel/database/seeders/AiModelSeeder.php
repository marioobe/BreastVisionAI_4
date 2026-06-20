<?php

namespace Database\Seeders;

use App\Models\AiModel;
use Illuminate\Database\Seeder;

class AiModelSeeder extends Seeder
{
    public function run(): void
    {
        AiModel::create([
            'name' => 'MobileNetV2 - BUSI',
            'version' => '1.0.0',
            'path' => 'ai-models/model.keras',
            'metrics' => [
                'accuracy' => 0.95,
                'val_accuracy' => 0.93,
                'loss' => 0.15,
                'val_loss' => 0.20,
            ],
            'is_active' => true,
            'uploaded_by' => 1,
        ]);
    }
}
