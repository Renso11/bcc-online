<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCardBuy extends Model
{
    use HasFactory;
    protected $guarded = [];public $incrementing = false; 
    public function userClient(){
        return $this->belongsTo('App\Models\UserClient');
    }
    public function userPartenaire(){
        return $this->belongsTo('App\Models\UserPartenaire');
    }
    public function userCard(){
        return $this->belongsTo('App\Models\UserCard');
    }
    public function partenaire(){
        return $this->belongsTo('App\Models\Partenaire');
    }
    public function apporteur(){
        return $this->belongsTo('App\Models\Apporteur');
    }
}
