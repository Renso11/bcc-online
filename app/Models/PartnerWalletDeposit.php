<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerWalletDeposit extends Model
{
    use HasFactory;
    protected $guarded = [];public $incrementing = false; 
    
    public function partenaire(){
        return $this->belongsTo('App\Models\Partenaire');
    }

    public function userPartenaire(){
        return $this->belongsTo('App\Models\UserPartenaire');
    }
    
    public function wallet(){
        return $this->belongsTo('App\Models\PartnerWallet');
    }
}
