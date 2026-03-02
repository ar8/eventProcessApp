<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAudit extends Model
{
    protected $table = 'event_audits';
    
    protected $fillable = [
        'event_id',
        'action',
        'details',
    ];
}
