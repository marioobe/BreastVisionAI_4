<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\Patient;
use App\Models\Prediction;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalPredictions = Prediction::count();
        $totalPatients = Patient::count();
        $totalModels = AiModel::count();
        $activeModel = AiModel::where('is_active', true)->first();

        $predictionsByClass = Prediction::selectRaw('prediction, COUNT(*) as total')
            ->groupBy('prediction')
            ->pluck('total', 'prediction');

        $todayPredictions = Prediction::whereDate('created_at', today())->count();

        $recentPredictions = Prediction::with('patient')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'data' => [
                'total_predictions' => $totalPredictions,
                'total_patients' => $totalPatients,
                'total_models' => $totalModels,
                'active_model' => $activeModel ? [
                    'id' => $activeModel->id,
                    'name' => $activeModel->name,
                    'version' => $activeModel->version,
                ] : null,
                'predictions_by_class' => $predictionsByClass,
                'today_predictions' => $todayPredictions,
                'recent_predictions' => $recentPredictions,
            ],
        ]);
    }
}
