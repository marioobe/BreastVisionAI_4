<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prediction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function index(Request $request): View
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
            ->paginate(15)
            ->withQueryString();

        return view('admin.predictions.index', compact('predictions'));
    }

    public function show(Prediction $prediction): View
    {
        $prediction->load(['patient', 'probabilities', 'aiModel']);

        return view('admin.predictions.show', compact('prediction'));
    }

    public function destroy(Prediction $prediction): RedirectResponse
    {
        $prediction->delete();

        return redirect()->route('admin.predictions')->with('success', 'Data prediksi berhasil dihapus.');
    }
}
