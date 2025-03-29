<?php

namespace Boreistudio\SecureDelete\Models;

use Illuminate\Database\Eloquent\Model;

class SecureDeletion extends Model
{
    protected $fillable = [
        'deletable_type',
        'deletable_path',
        'method',
        'file_size',
        'original_checksum',
        'user_id',
    ];

    // Relación con usuario (opcional)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}