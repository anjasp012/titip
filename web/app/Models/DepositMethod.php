<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositMethod extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
    public function deposit() {
    	return $this->hasMany('App\Models\Deposit'); 
    }
}
