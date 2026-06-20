<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        'nik',
        'name',
        'gender',
        'date_of_birth',
        'address',
        'phone',
        'email',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }
}
