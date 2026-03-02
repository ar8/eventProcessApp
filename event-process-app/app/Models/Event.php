<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\NormalizeEventJob;
use App\Services\Sources\SourceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Event extends Model
{
    protected $table = 'events';
    
    protected $fillable = [
        'uuid',
        'external_id',
        'source',
        'raw_payload',
        'normalized_payload',
        'status',
        'score',
        'error',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'normalized_payload' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

}
