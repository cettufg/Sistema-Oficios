<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;

class ResponsavelOficio extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'oficio_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function oficio(): BelongsTo
    {
        return $this->belongsTo(Oficio::class);
    }
}
