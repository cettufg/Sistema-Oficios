<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diretoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'nome',
        'status',
        'user_created',
        'user_updated'
    ];
}
