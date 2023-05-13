<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $guard = 'user';
    public $timestamps = false;
    protected $guarded = [];
    public function user_register_log()
    {
        return $this->hasMany('App\Models\UserRegisterLog');
    }
    public function user_login_log()
    {
        return $this->hasMany('App\Models\UserLoginLog');
    }
    public function user_balance_log()
    {
        return $this->hasMany('App\Models\UserBalanceLog');
    }
    public function order()
    {
        return $this->hasMany('App\Models\Order');
    }
    public function order_bonus()
    {
        return $this->hasMany('App\Models\OrderBonus');
    }
    public function deposit()
    {
        return $this->hasMany('App\Models\Deposit');
    }
    public function ticket()
    {
        return $this->hasMany('App\Models\Ticket');
    }
    public function ticket_reply()
    {
        return $this->hasMany('App\Models\TicketReply');
    }
    public function exchange_point()
    {
        return $this->hasMany('App\Models\ExchangePoint');
    }
    public function social()
    {
        return $this->hasMany('App\Models\Social');
    }
}
