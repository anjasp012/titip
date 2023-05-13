<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRegisterLog extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
    public function user() {
    	return $this->belongsTo('App\Models\User'); 
    }
	public function upline() {
    	return $this->belongsTo('App\Models\User', 'upline_user_id');
	}
}
