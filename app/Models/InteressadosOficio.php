<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InteressadosOficio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'oficio_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oficio()
    {
        return $this->belongsTo(Oficio::class);
    }
}
