<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function user() {
    	return $this->belongsTo('App\Models\User');
	}
	public function product() {
    	return $this->belongsTo('App\Models\Product');
	}
	public function product_provider() {
    	return $this->belongsTo('App\Models\ProductProvider', 'provider_id');
	}
	public function order_bonus() {
    	return $this->hasOne('App\Models\OrderBonus');
	}
}
