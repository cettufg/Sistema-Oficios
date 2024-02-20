<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CienteOficio extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'oficio_id',
    ];

    public function oficio(): HasOne
    {
        return $this->hasOne(Oficio::class, 'id', 'oficio_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
