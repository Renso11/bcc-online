<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDistribution extends Model
{
    use HasFactory;
    protected $guarded = [];public $incrementing = false; 
    public function accountDistributionOperations(){
        return $this->hasMany('App\Models\AccountDistributionOperation');
    }

    public function partenaire(){
        return $this->belongsTo('App\Models\Partenaire');
    }
}
