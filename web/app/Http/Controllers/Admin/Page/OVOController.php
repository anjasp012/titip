<?php

namespace App\Http\Controllers\Admin\Page;

use Exception;
use Stelin\Ovoid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OVOController extends Controller {
    public function __construct() {
        $this->breadcrumb = (object) [
			'first'  => 'Token OVO',
            'second' => website_config('main')->website_name
		];
    }
    public function getLogin() {
        $components['breadcrumb'] = $this->breadcrumb;
        return view('admin.page.ovo.login', $components);
    } 
    public function postLogin(Request $request) {
        if (Auth::guard('admin')->user()->level == 'Admin') {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $validate_input = [
            'phone_number' => 'required|numeric|phone:ID,mobile',
        ];
        $validate_attributes = [
            'phone_number' => 'Nomor Telepon'
        ];
        $validator = Validator::make($request->all(), $validate_input, [], $validate_attributes);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $ovoid = new OVOID();
            $reference_code = $ovoid->login2FA($request->phone_number)->getRefId();
            return redirect('admin/page/ovo/confirm')->with('result', [
                'alert'          => 'success', 
                'title'          => 'Berhasil', 
                'message'        => 'Kode Referensi: '.$reference_code.'',
                'phone_number'   => $request->phone_number,
                'reference_code' => $reference_code,
            ]);
        } catch (Exception $error) {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => $error->getMessage()
            ]);
        }
    } 
    public function getConfirm() {
        if (isset(Session::get('result')['reference_code']) == false) return redirect('admin/page/ovo/login');
        if (isset(Session::get('result')['phone_number']) == false) return redirect('admin/page/ovo/login');
        $components['breadcrumb'] = $this->breadcrumb;
        return view('admin.page.ovo.confirm', $components);
    }
    public function postConfirm(Request $request) {
        if (Auth::guard('admin')->user()->level == 'Admin') {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $validate_input = [
            'reference_code' => 'required',
            'otp_code'       => 'required|numeric',
            'phone_number'   => 'required|numeric|phone:ID,mobile',
        ];
        $validate_attributes = [
            'reference_code' => 'Kode Referensi',
            'otp_code'       => 'Kode OTP',
            'phone_number'   => 'Nomor Telepon'
        ];
        $validator = Validator::make($request->all(), $validate_input, [], $validate_attributes);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $ovoid = new OVOID();
            $unique_token = $ovoid->login2FAVerify($request->reference_code, $request->otp_code, $request->phone_number)->getUpdateAccessToken();
            return redirect('admin/page/ovo/pin')->with('result', [
                'alert'          => 'success', 
                'title'          => 'Berhasil', 
                'message'        => 'Token Unik: '.$unique_token.'',
                'phone_number'   => $request->phone_number,
                'unique_token'   => $unique_token,
            ]);
        } catch (Exception $error) {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => $error->getMessage()
            ]);
        }
    } 
    public function getPin() {
        if (isset(Session::get('result')['unique_token']) == false) return redirect('admin/page/ovo/login');
        $components['breadcrumb'] = $this->breadcrumb;
        return view('admin.page.ovo.pin', $components);
    }
    public function postPin(Request $request) {
        if (Auth::guard('admin')->user()->level == 'Admin') {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $validate_input = [
            'unique_token' => 'required',
            'pin'          => 'required|numeric',
        ];
        $validate_attributes = [
            'unique_token' => 'Token Unik',
            'pin'          => 'PIN',
        ];
        $validator = Validator::make($request->all(), $validate_input, [], $validate_attributes);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $ovoid = new OVOID();
            $access_token =  $ovoid->loginSecurityCode($request->pin, $request->unique_token)->getAuthorizationToken();
            return redirect('admin/settings/bank_account/list')->with('result', [
                'alert'          => 'success', 
                'title'          => 'Berhasil', 
                'message'        => 'Token Akses: '.$access_token.'',
            ]);
        } catch (Exception $error) {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => $error->getMessage()
            ]);
        }
    } 
}
