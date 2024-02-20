<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiretoriaOficio extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'diretoria_id',
        'oficio_id',
    ];

    public function oficio(): HasOne
    {
        return $this->hasOne(Oficio::class);
    }

    public function diretoria(): HasOne
    {
        return $this->hasOne(Diretoria::class);
    }
}
