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

    public function oficio()
    {
        return $this->hasOne(Oficio::class, 'id', 'oficio_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
