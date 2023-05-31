<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CienteOficio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'oficio_id',
    ];
}
