<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function product() {
    	return $this->hasOne('App\Models\Product');
	}
	public function category() {
    	return $this->belongsTo('App\Models\ProductCategory', 'category_id');
	}
}
