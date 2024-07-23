<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationUserClient extends Model
{
    use HasFactory;
    protected $table = 'notification_user_clients';

    protected $fillable = ['user_client_id', 'notification_id', 'status'];
}
