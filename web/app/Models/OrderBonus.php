<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBonus extends Model
{
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function user() {
    	return $this->belongsTo('App\Models\User');
	}
	public function order() {
    	return $this->belongsTo('App\Models\Order');
	}
}
