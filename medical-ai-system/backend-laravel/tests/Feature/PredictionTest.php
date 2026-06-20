<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Patient;
use App\Models\Prediction;
use App\Models\PredictionProbability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PredictionTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_index_returns_predictions(): void
    {
        Prediction::factory()
            ->has(PredictionProbability::factory()->count(3), 'probabilities')
            ->count(5)
            ->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson('/api/predictions');

        $response->assertStatus(200);
    }

    public function test_show_returns_prediction_detail(): void
    {
        $prediction = Prediction::factory()
            ->has(PredictionProbability::factory()->count(3), 'probabilities')
            ->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/predictions/{$prediction->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['patient', 'probabilities']]);
    }

    public function test_destroy_deletes_prediction(): void
    {
        $prediction = Prediction::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->deleteJson("/api/predictions/{$prediction->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('predictions', ['id' => $prediction->id]);
    }

    public function test_dashboard_returns_statistics(): void
    {
        Prediction::factory()->count(10)->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'total_predictions',
                'total_patients',
                'total_models',
                'today_predictions',
            ]]);
    }

    public function test_web_form_validates_required_fields(): void
    {
        $response = $this->post('/pemeriksaan', []);

        $response->assertSessionHasErrors(['nik', 'name', 'gender', 'date_of_birth', 'image']);
    }
}
