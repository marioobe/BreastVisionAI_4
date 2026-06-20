<?php

namespace App\Services;

use App\Models\AiModel;
use App\Models\Patient;
use App\Models\Prediction;
use App\Models\PredictionProbability;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PredictionService
{
    public function __construct(
        private AiService $aiService
    ) {}

    public function predict(array $patientData, $imageFile, bool $consentApproved = false): Prediction
    {
        return DB::transaction(function () use ($patientData, $imageFile, $consentApproved) {
            $patient = Patient::firstOrCreate(
                ['nik' => $patientData['nik']],
                [
                    'name' => $patientData['name'],
                    'gender' => $patientData['gender'],
                    'date_of_birth' => $patientData['date_of_birth'],
                    'address' => $patientData['address'] ?? null,
                    'phone' => $patientData['phone'] ?? null,
                    'email' => $patientData['email'] ?? null,
                ]
            );

            $imagePath = $imageFile->store('predictions/' . $patient->id, 'public');

            $storagePath = Storage::disk('public')->path($imagePath);

            $activeModel = AiModel::where('is_active', true)->first();

            $startTime = microtime(true);
            $aiResult = $this->aiService->predict($storagePath);
            $analysisTime = round((microtime(true) - $startTime) * 1000, 2);

            $predictionData = [
                'patient_id' => $patient->id,
                'image_path' => $imagePath,
                'prediction' => $aiResult['prediction'],
                'confidence' => $aiResult['confidence'],
                'consent_approved' => $consentApproved,
                'analysis_time' => $analysisTime,
                'metadata' => [
                    'request' => $patientData,
                    'ai_response' => $aiResult,
                ],
            ];

            if ($activeModel) {
                $predictionData['ai_model_id'] = $activeModel->id;
            }

            $prediction = Prediction::create($predictionData);

            if (isset($aiResult['probabilities'])) {
                foreach ($aiResult['probabilities'] as $class => $prob) {
                    PredictionProbability::create([
                        'prediction_id' => $prediction->id,
                        'class_name' => $class,
                        'probability' => $prob,
                    ]);
                }
            }

            return $prediction->load(['patient', 'probabilities', 'aiModel']);
        });
    }
}
