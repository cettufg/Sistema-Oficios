<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioRelacionado extends Model
{
    use HasFactory;

    protected $fillable = [
        'oficio_pai',
        'oficio_filho',
    ];
}
