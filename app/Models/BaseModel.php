<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;

class BaseModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->user_created = auth()->user()->id ?? null;
            $model->user_updated = auth()->user()->id ?? null;
        });
        static::updating(function ($model) {
            $model->user_updated = auth()->user()->id ?? null;
        });
    }

    public function created_by(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_created');
    }

    public function updated_by(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_updated');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ATIVO);
    }
}
