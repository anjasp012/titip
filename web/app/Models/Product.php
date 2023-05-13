<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
    public function category() {
    	return $this->belongsTo('App\Models\ProductCategory', 'category_id'); 
    }
    public function sub_category() {
    	return $this->belongsTo('App\Models\ProductSubCategory', 'sub_category_id'); 
    }
    public function provider() {
    	return $this->belongsTo('App\Models\ProductProvider', 'provider_id'); 
    }
    public function order() {
    	return $this->hasOne('App\Models\Order'); 
    }
}
