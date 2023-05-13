<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function product() {
    	return $this->hasOne('App\Models\Product');
	}
	public function sub_category() {
    	return $this->hasOne('App\Models\ProductSubCategory');
	}
}
