<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destinatario extends BaseModel
{
    public function oficios(): HasMany
    {
        return $this->hasMany(Oficio::class, 'destinatario_id', 'id');
    }
}
