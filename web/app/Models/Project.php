<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Project extends Model
{
	use HasFactory;
	public $timestamps = false;
	protected $guarded = [];
	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id');
	}
	public function category()
	{
		return $this->belongsTo('App\Models\ProjectCategory', 'category_id');
	}
	public function projectbid()
	{
		return $this->hasOne('App\Models\ProjectBid', 'id', 'projectbid_id')->withDefault([
			'status' => 'Active'
		]);
	}
}
