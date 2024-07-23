<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];public $incrementing = false; 
    use HasFactory;

    public function userClients()
    {
        return $this->belongsToMany(UserClient::class, 'notification_user_clients')
                    ->withPivot('read')
                    ->withTimestamps();
    }
}
