<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $guarded = [];public $incrementing = false; 

    public function rolePermissions(){
        return $this->hasMany('App\Models\RolePermission')->where('deleted', 0);
    }
}
