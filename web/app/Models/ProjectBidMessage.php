<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProjectBidMessage extends Model
{
	use HasFactory;
	public $timestamps = false;
	protected $guarded = [];
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
	public function project()
	{
		return $this->belongsTo('App\Models\Project', 'project_id');
	}
}
