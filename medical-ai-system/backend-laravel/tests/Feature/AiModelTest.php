<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AiModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AiModelTest extends TestCase
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

    public function test_index_returns_all_models(): void
    {
        AiModel::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson('/api/models');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_store_validates_model_file_required(): void
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson('/api/models', [
                'name' => 'Test Model',
                'version' => '1.0.0',
            ]);

        $response->assertStatus(422);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson('/api/models', []);

        $response->assertStatus(422);
    }

    public function test_update_model(): void
    {
        $model = AiModel::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->putJson("/api/models/{$model->id}", [
                'name' => 'Updated Model',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Model berhasil diperbarui']);
    }

    public function test_delete_model(): void
    {
        $model = AiModel::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->deleteJson("/api/models/{$model->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('ai_models', ['id' => $model->id]);
    }

    public function test_activate_model(): void
    {
        $model = AiModel::factory()->create(['is_active' => false]);
        $otherModel = AiModel::factory()->create(['is_active' => true]);

        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->postJson("/api/models/{$model->id}/activate");

        $response->assertStatus(200);
        $this->assertDatabaseHas('ai_models', ['id' => $model->id, 'is_active' => true]);
        $this->assertDatabaseHas('ai_models', ['id' => $otherModel->id, 'is_active' => false]);
    }
}
