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

    public function oficio_pai()
    {
        return $this->hasOne(Oficio::class, 'id', 'oficio_pai');
    }

    public function oficio_filho()
    {
        return $this->hasOne(Oficio::class, 'id', 'oficio_filho');
    }
}
