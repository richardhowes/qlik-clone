<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'query',
        'config',
        'position',
        'size',
        'dashboard_id',
        'data_source_id',
    ];

    protected $casts = [
        'config' => 'array',
        'position' => 'array',
        'size' => 'array',
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }
}
