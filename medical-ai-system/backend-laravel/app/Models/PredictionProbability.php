<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictionProbability extends Model
{
    use HasFactory;
    protected $fillable = [
        'prediction_id',
        'class_name',
        'probability',
    ];

    protected function casts(): array
    {
        return [
            'probability' => 'decimal:2',
        ];
    }

    public function prediction()
    {
        return $this->belongsTo(Prediction::class);
    }
}
