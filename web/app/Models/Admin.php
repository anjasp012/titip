<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticable {
    use HasFactory, Notifiable;
    protected $guard = 'admin';
    public $timestamps = false;
	protected $guarded = [];
    public function admin_login_log() {
    	return $this->hasMany('App\Models\AdminLoginLog'); 
    }
}
