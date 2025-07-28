<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'connection_config',
        'status',
        'last_sync_at',
        'user_id',
    ];

    protected $casts = [
        'connection_config' => 'encrypted:array',
        'last_sync_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function charts(): HasMany
    {
        return $this->hasMany(Chart::class);
    }
}
