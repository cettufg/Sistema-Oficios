<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUsers extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'status',
    ];
}
