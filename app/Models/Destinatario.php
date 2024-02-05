<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinatario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'status',
        'user_created',
        'user_updated'
    ];

    public function oficios()
    {
        return $this->hasMany(Oficio::class, 'destinatario_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ATIVO);
    }
}
