<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnexoOficio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'tipo',
        'tamanho',
        'caminho',
        'oficio_id',
    ];
}
