<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OficioExterno extends Model
{
    protected $fillable = [
        'oficio_id',
        'descricao'
    ];

    public function oficio(): BelongsTo
    {
        return $this->belongsTo(Oficio::class);
    }
}
