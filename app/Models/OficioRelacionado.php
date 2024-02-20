<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OficioRelacionado extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'oficio_pai',
        'oficio_filho',
    ];

    public function oficio_pai(): HasOne
    {
        return $this->hasOne(Oficio::class, 'id', 'oficio_pai');
    }

    public function oficio_filho(): HasOne
    {
        return $this->hasOne(Oficio::class, 'id', 'oficio_filho');
    }
}
