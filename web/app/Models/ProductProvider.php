<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProvider extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function product() {
    	return $this->hasOne('App\Models\Product');
	}
}
