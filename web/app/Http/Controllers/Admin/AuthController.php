<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\AdminLoginLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController extends Controller {
    public function __construct() {
        $this->middleware('guest:admin')->only('getLogin', 'postLogin');
    }
    public function index() {
        $components = [
            'users'    => User::get(),
            'orders'   => Order::get(),
            'deposits' => Deposit::get()
        ];
        return view('admin.auth.index', $components);
    }
    public function getLogin() {
        return view('admin.auth.login');
    }
    public function postLogin(PostRequest $request) {
		if ($request->ajax() == false) abort('404');
        $input_data = [
            'username' => escape_input($request->username),
            'password' => escape_input($request->password),
        ];
        if (Auth::guard('admin')->attempt($input_data) == true) {
            if (Auth::guard('admin')->user()->status == 0) {
                Auth::guard('admin')->logout();
                return response()->json([
                    'status'  => false, 
                    'type'    => 'alert',
                    'message' => 'Akun dinonaktifkan.'
                ]);
            }
            AdminLoginLog::create([
                'admin_id'   => Auth::guard('admin')->user()->id,
                'ip_address' => $request->ip()
            ]);
            session()->flash('result', [
                'alert'   => 'success', 
                'title'   => 'Berhasil', 
                'message' => 'Selamat datang <b>'.Auth::guard('admin')->user()->full_name.'</b>.'
            ]);
            return response()->json([
                'status'  => true, 
                'message' => 'Login Berhasil.'
            ]);
        } else {
            return response()->json([
                'status'  => false, 
                'type'    => 'alert',
                'message' => 'Username atau Password salah.'
            ]);
        }
    }
    public function logout(){
		if (Auth::guard('admin')->check() == false) return redirect('admin/auth/login');
        Auth::guard('admin')->logout();
        return redirect('admin/auth/login');
    }
}

class PostRequest extends FormRequest {
    protected function getValidatorInstance() {
		$instance = parent::getValidatorInstance();
        if ($instance->fails() == true) {
			throw new HttpResponseException(response()->json([
				'status'  => false, 
				'type'    => 'validation',
				'message' => parent::getValidatorInstance()->errors()
			]));
		}
        return parent::getValidatorInstance();
    }
    public function rules() {
        return [
            'username' => 'required|string',
            'password' => 'required'
        ];
    }
    public function attributes() {
        return [
            'username' => 'Username',
            'password' => 'Password',
        ];
    }
}
