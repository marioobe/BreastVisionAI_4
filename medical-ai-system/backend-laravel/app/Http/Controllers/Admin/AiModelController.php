<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiModelController extends Controller
{
    public function index(): View
    {
        $models = AiModel::with('uploader:id,name')
            ->withCount('predictions')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.models.index', compact('models'));
    }

    public function edit(AiModel $aiModel): View
    {
        $model = $aiModel;
        return view('admin.models.edit', compact('model'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'model_file' => 'required|file|mimes:keras,h5|max:204800',
            'accuracy' => 'nullable|numeric|min:0|max:100',
            'loss' => 'nullable|numeric|min:0',
        ]);

        $path = $request->file('model_file')->store('ai-models', 'local');

        $metrics = [];
        if ($request->filled('accuracy')) {
            $metrics['accuracy'] = (float) $request->accuracy;
        }
        if ($request->filled('loss')) {
            $metrics['loss'] = (float) $request->loss;
        }

        AiModel::create([
            'name' => $validated['name'],
            'version' => $validated['version'],
            'path' => $path,
            'metrics' => !empty($metrics) ? $metrics : null,
            'uploaded_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.models')->with('success', 'Model berhasil ditambahkan.');
    }

    public function update(Request $request, AiModel $aiModel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'accuracy' => 'nullable|numeric|min:0|max:100',
            'loss' => 'nullable|numeric|min:0',
        ]);

        $metrics = $aiModel->metrics ?? [];
        if ($request->filled('accuracy')) {
            $metrics['accuracy'] = (float) $request->accuracy;
        }
        if ($request->filled('loss')) {
            $metrics['loss'] = (float) $request->loss;
        }

        $aiModel->update([
            'name' => $validated['name'],
            'version' => $validated['version'],
            'metrics' => $metrics,
        ]);

        return redirect()->route('admin.models')->with('success', 'Model berhasil diperbarui.');
    }

    public function destroy(AiModel $aiModel): RedirectResponse
    {
        $aiModel->delete();

        return redirect()->route('admin.models')->with('success', 'Model berhasil dihapus.');
    }

    public function activate(AiModel $aiModel): RedirectResponse
    {
        AiModel::where('is_active', true)->update(['is_active' => false]);
        $aiModel->update(['is_active' => true]);

        return redirect()->route('admin.models')->with('success', 'Model berhasil diaktifkan.');
    }
}
