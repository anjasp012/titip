<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PostingLapor extends Model
{
	use HasFactory;
	public $timestamps = false;
	protected $guarded = [];
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
	public function posting()
	{
		return $this->belongsTo('App\Models\Posting', 'posting_id');
	}
}
