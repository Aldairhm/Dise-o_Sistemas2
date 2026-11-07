<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationEvent extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = ['context' => 'array', 'created_at' => 'datetime'];
}
