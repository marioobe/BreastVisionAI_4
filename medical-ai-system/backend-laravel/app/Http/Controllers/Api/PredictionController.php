<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PredictRequest;
use App\Models\Prediction;
use App\Services\PredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function __construct(
        private PredictionService $predictionService
    ) {}

    public function predict(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:10240',
        ]);

        $result = $this->predictionService->predict(
            $validated,
            $request->file('image'),
            $request->boolean('consent_approved')
        );

        return response()->json([
            'message' => 'Prediksi berhasil',
            'data' => $result,
        ]);
    }

    public function predictFromWeb(PredictRequest $request): RedirectResponse
    {
        $result = $this->predictionService->predict(
            $request->validated(),
            $request->file('image'),
            $request->boolean('disclaimer')
        );

        return redirect()->route('pasien.hasil', $result->id);
    }

    public function index(Request $request): JsonResponse
    {
        $predictions = Prediction::with(['patient', 'probabilities', 'aiModel'])
            ->when($request->search, function ($q, $search) {
                $q->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->when($request->prediction, function ($q, $pred) {
                $q->where('prediction', $pred);
            })
            ->when($request->date_from, function ($q, $date) {
                $q->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($q, $date) {
                $q->whereDate('created_at', '<=', $date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($predictions);
    }

    public function show(Prediction $prediction): JsonResponse
    {
        $prediction->load(['patient', 'probabilities', 'aiModel']);

        return response()->json([
            'data' => $prediction,
        ]);
    }

    public function destroy(Prediction $prediction): JsonResponse
    {
        $prediction->delete();

        return response()->json(['message' => 'Data prediksi berhasil dihapus']);
    }
}
