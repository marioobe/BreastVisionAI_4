<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    use HasFactory;
    protected $table = 'ai_models';

    protected $fillable = [
        'name',
        'version',
        'path',
        'metrics',
        'is_active',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'metrics' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class, 'ai_model_id');
    }
}
