<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function ticket() {
    	return $this->belongsTo('App\Models\User');
	}
	public function user() {
    	return $this->belongsTo('App\Models\User');
	}
}
