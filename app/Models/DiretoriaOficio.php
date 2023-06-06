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

    public function oficio()
    {
        return $this->hasOne(Oficio::class, 'id', 'oficio_id');
    }

    public function diretoria()
    {
        return $this->hasOne(Diretoria::class, 'id', 'diretoria_id');
    }
}
