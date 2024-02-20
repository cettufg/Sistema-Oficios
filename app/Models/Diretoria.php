<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diretoria extends BaseModel
{
    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupUsers::class);
    }
}
