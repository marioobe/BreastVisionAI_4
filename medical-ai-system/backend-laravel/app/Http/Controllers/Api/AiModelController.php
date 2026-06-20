<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiModelController extends Controller
{
    public function index(): JsonResponse
    {
        $models = AiModel::with('uploader:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $models]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'model_file' => 'required|file|mimes:keras,h5|max:204800',
            'metrics' => 'nullable|json',
        ]);

        $path = $request->file('model_file')->store('ai-models', 'local');

        $model = AiModel::create([
            'name' => $validated['name'],
            'version' => $validated['version'],
            'path' => $path,
            'metrics' => $validated['metrics'] ? json_decode($validated['metrics'], true) : null,
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Model berhasil ditambahkan',
            'data' => $model->load('uploader:id,name'),
        ], 201);
    }

    public function update(Request $request, AiModel $aiModel): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'version' => 'sometimes|string|max:50',
            'metrics' => 'nullable|json',
        ]);

        $data = collect($validated)->filter(fn($v) => !is_null($v))->toArray();

        if (isset($data['metrics'])) {
            $data['metrics'] = json_decode($data['metrics'], true);
        }

        $aiModel->update($data);

        return response()->json([
            'message' => 'Model berhasil diperbarui',
            'data' => $aiModel->fresh()->load('uploader:id,name'),
        ]);
    }

    public function destroy(AiModel $aiModel): JsonResponse
    {
        $aiModel->delete();

        return response()->json(['message' => 'Model berhasil dihapus']);
    }

    public function activate(AiModel $aiModel): JsonResponse
    {
        AiModel::where('is_active', true)->update(['is_active' => false]);

        $aiModel->update(['is_active' => true]);

        return response()->json([
            'message' => 'Model berhasil diaktifkan',
            'data' => $aiModel->fresh(),
        ]);
    }
}
