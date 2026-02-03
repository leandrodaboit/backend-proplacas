<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'integration_token_id',
        'endpoint',
        'method',
        'ip_address',
        'response_status',
        'response_time_ms',
        'request_headers',
        'request_body',
        'response_body',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'request_headers' => 'array',
            'request_body' => 'array',
            'response_body' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function integrationToken(): BelongsTo
    {
        return $this->belongsTo(IntegrationToken::class);
    }
}
