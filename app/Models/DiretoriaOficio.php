<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiretoriaOficio extends Model
{
    use HasFactory;

    protected $fillable = [
        'diretoria_id',
        'oficio_id',
    ];
}
