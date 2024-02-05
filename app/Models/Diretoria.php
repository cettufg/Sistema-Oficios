<?php

namespace App\Models;

use App\Enums\Status;
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

    public function scopeActive($query)
    {
        return $query->where('status', Status::ATIVO);
    }
}
