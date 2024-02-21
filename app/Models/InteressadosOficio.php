<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteressadosOficio extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'oficio_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function oficio(): BelongsTo
    {
        return $this->belongsTo(Oficio::class, 'oficio_id');
    }
}
