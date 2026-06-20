<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\Patient;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPredictions = Prediction::count();
        $totalPatients = Patient::count();
        $totalModels = AiModel::count();
        $activeModel = AiModel::where('is_active', true)->first();
        $todayPredictions = Prediction::whereDate('created_at', today())->count();

        $predictionsByClass = Prediction::selectRaw('prediction, COUNT(*) as total')
            ->groupBy('prediction')
            ->pluck('total', 'prediction');

        $recentPredictions = Prediction::with('patient')
            ->latest()
            ->take(10)
            ->get();

        $models = AiModel::with('uploader:id,name')
            ->withCount('predictions')
            ->orderBy('created_at', 'desc')
            ->get();

        $modelUsage = AiModel::where('is_active', true)
            ->withCount('predictions')
            ->first();

        return view('dashboard.index', compact(
            'totalPredictions',
            'totalPatients',
            'totalModels',
            'activeModel',
            'todayPredictions',
            'predictionsByClass',
            'recentPredictions',
            'models',
            'modelUsage'
        ));
    }
}
