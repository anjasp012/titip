<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function user() {
    	return $this->belongsTo('App\Models\User');
	}
	public function deposit_method() {
    	return $this->belongsTo('App\Models\DepositMethod');
	}
}
