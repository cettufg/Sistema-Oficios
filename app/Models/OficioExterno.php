<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioExterno extends Model
{
    use HasFactory;

    protected $fillable = [
        'oficio_id',
        'descricao'
    ];
}
