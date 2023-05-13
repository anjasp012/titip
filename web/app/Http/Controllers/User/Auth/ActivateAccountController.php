<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserActivateAccount;
use App\Http\Controllers\Controller;

class ActivateAccountController extends Controller {
    public function index(UserActivateAccount $target) {
        $user = User::where('email', $target->email)->update([
            'is_verified' => '1'
        ]);
        $target->delete();
        return redirect('auth/login')->with('result', [
            'alert'   => 'success', 
            'title'   => 'Berhasil', 
            'message' => 'Akun berhasil diaktifkan.'
        ]);
    }
}
