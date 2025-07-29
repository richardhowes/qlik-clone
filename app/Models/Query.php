<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Query extends Model
{
    protected $fillable = [
        'user_id',
        'data_source_id',
        'name',
        'sql',
        'result_metadata',
        'execution_time',
        'row_count',
    ];

    protected $casts = [
        'result_metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }
}
