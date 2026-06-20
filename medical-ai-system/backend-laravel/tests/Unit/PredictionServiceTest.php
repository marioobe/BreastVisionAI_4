<?php

namespace Tests\Unit;

use App\Models\AiModel;
use App\Services\AiService;
use App\Services\PredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class PredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_predict_creates_patient_and_prediction(): void
    {
        $mockAiService = Mockery::mock(AiService::class);
        $mockAiService->shouldReceive('predict')
            ->once()
            ->andReturn([
                'prediction' => 'Normal',
                'confidence' => 95.50,
                'probabilities' => [
                    'Normal' => 95.50,
                    'Benign' => 2.25,
                    'Malignant' => 2.25,
                ],
            ]);

        $service = new PredictionService($mockAiService);
        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        $result = $service->predict([
            'nik' => '1234567890123456',
            'name' => 'Test Patient',
            'gender' => 'L',
            'date_of_birth' => '1990-01-15',
        ], $file, true);

        $this->assertDatabaseHas('patients', ['nik' => '1234567890123456']);
        $this->assertDatabaseHas('predictions', ['prediction' => 'Normal']);
        $this->assertEquals(95.50, $result->confidence);
        $this->assertEquals(3, $result->probabilities->count());
    }

    public function test_predict_uses_active_model(): void
    {
        $activeModel = AiModel::factory()->create(['is_active' => true]);

        $mockAiService = Mockery::mock(AiService::class);
        $mockAiService->shouldReceive('predict')
            ->once()
            ->andReturn([
                'prediction' => 'Osteopenia',
                'confidence' => 82.10,
                'probabilities' => [
                    'Normal' => 10.0,
                    'Osteopenia' => 82.10,
                    'Osteoporosis' => 7.90,
                ],
            ]);

        $service = new PredictionService($mockAiService);
        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        $result = $service->predict([
            'nik' => '9876543210987654',
            'name' => 'Another Patient',
            'gender' => 'P',
            'date_of_birth' => '1985-05-20',
        ], $file, true);

        $this->assertEquals($activeModel->id, $result->ai_model_id);
    }
}
