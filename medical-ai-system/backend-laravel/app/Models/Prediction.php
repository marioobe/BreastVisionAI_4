<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'ai_model_id',
        'image_path',
        'prediction',
        'confidence',
        'consent_approved',
        'analysis_time',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
            'consent_approved' => 'boolean',
            'analysis_time' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function aiModel()
    {
        return $this->belongsTo(AiModel::class, 'ai_model_id');
    }

    public function probabilities()
    {
        return $this->hasMany(PredictionProbability::class);
    }
}
