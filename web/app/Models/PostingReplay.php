<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PostingReplay extends Model
{
	use HasFactory;
	public $timestamps = false;
	protected $guarded = [];
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function postingans()
	{
		return $this->belongsTo('App\Models\PostingAnswer', 'answer_id');
	}
}
