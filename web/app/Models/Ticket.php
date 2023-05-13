<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model {
    use HasFactory;
    public $timestamps = false;
	protected $guarded = [];
	public function user() {
    	return $this->belongsTo('App\Models\User');
	}
    public function ticket_reply() {
    	return $this->hasMany('App\Models\TicketReply'); 
    }
}
