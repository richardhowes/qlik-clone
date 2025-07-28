<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dashboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'thumbnail',
        'is_favorite',
        'config',
        'user_id',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'config' => 'array',
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
