<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerAllWalletDetail extends Model
{
    use HasFactory;
    protected $guarded = [];public $incrementing = false; 
    
    public function partenaire(){
        return $this->belongsTo('App\Models\Partenaire');
    }
}
