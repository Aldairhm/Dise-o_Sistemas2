<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationDelivery extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = ['read_at' => 'datetime', 'archived_at' => 'datetime'];

    public function event()
    {
        return $this->belongsTo(NotificationEvent::class, 'event_id');
    }
}
