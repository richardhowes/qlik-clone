<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidget extends Model
{
    protected $fillable = [
        'dashboard_id',
        'query_id',
        'type',
        'title',
        'config',
        'layout',
        'order',
    ];

    protected $casts = [
        'config' => 'array',
        'layout' => 'array',
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }

    public function savedQuery(): BelongsTo
    {
        return $this->belongsTo(Query::class, 'query_id');
    }
}