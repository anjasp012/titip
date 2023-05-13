<?php

namespace App\Http\Controllers\User\Auth;

use Exception;
use App\Models\Page;
use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Mail\HelperEmail;
use App\Mail\ActivateEmail;
use App\Models\Information;
use App\Models\RegisterLog;
use Illuminate\Support\Str;
use App\Models\UserLoginLog;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use App\Models\ActivateAccount;
use App\Models\UserRegisterLog;
use App\Models\UserActivateAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Cookie as CookieModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }
    public function getRegister()
    {
        //$this->send_wa("6281333387700", "23425");
        return view('user.auth.register');
    }

    public function regbyref($target)
    {
        $cek = User::where('referral_code', $target)->first();
        if ($cek) {
            Session::put('idref', $cek->id);
            return redirect('/');
        } else {
            return redirect('/');
        }
    }

    public function postRegister(RegisterRequest $request)
    {
        if ($request->ajax() == false) abort('404');
        $otp = $this->randomOtp(5);
        $upline_user_id = Session::get('idref');

        $input_data = [
            'full_name'     => escape_input($request->full_name),
            'email'         => escape_input($request->email),
            'phone_number'  => escape_input($request->phone_number),
            'username'      => escape_input($request->username),
            'password'      => escape_input(Hash::make($request->password)),
            'point'         => "10",
            'balance'       => 0,
            'level'         => 'Member',
            'upline'        => $upline_user_id,
            'otpcode'       => $otp,
            'api_key'       => create_api_key(),
            'referral_code' => sha1(escape_input($request->username)),
            'is_verified'   => '0',
        ];
        if (website_config('main')->is_email_confirmation_enabled == '') {
            $input_data['is_verified'] = '1';
        }
        


        /*$upline_user_id = null;
        if ($referral_code <> null) {
            $check_upline = User::where('referral_code', $referral_code)->first();
            if ($check_upline == true) {
                $input_data['upline'] = $check_upline->username;
                $upline_user_id = $check_upline->id;
            }
        }*/
        /*
        $check_data['ip_address'] = UserRegisterLog::where([['ip_address', $request->ip()], ['upline_user_id', null]])->get();
        if ($check_data['ip_address'] == true) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Anda sudah mendaftarkan akun sebelumnya.'
            ]);
        }*/
        $insert_data = User::create($input_data);
        UserRegisterLog::create([
            'upline_user_id' => $upline_user_id,
            'user_id'        => $insert_data->id,
            'ip_address'     => $request->ip()
        ]);
        send_watsapp($request->phone_number, 'Your Activation OTP CODE : ' . $otp);
        /*
        if ($referral_code <> null) {
            $check_upline->update([
                'point' => $check_upline->point + website_config('main')->bonus_point_upline
            ]);
        }
        $this->send_wa($request->phone_number, $otp);*/
        /*
        if ($input_data['is_verified'] == '0') {
            $input_data['token'] = md5($input_data['email'] . '-' . $input_data['username']);
            $details = [
                'name' => $input_data['full_name'],
                'url'  => url('auth/activate/' . $input_data['token'])
            ];
            $insert_data = UserActivateAccount::create([
                'email' => $input_data['email'],
                'token' => $input_data['token']
            ]);
            if ($insert_data == true) {
                if ($this->send_email($details, $input_data['email']) == true) {
                    return response()->json([
                        'status'  => true,
                        'message' => 'Silahkan periksa Email anda untuk mengaktifkan akun Anda.'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'type'    => 'alert',
                        'message' => 'Terjadi Kesalahan.'
                    ]);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'type'    => 'alert',
                    'message' => 'Terjadi Kesalahan.'
                ]);
            }
        }*/
        return response()->json([
            'status'  => true,
            'message' => 'Pendaftaran berhasil, silahkan masukan kode otp yang terkirim ke hp anda'
        ]);
    }

    public function getOtp()
    {
        return view('user.auth.otp');
    }
    public function activeotp(Request $request)
    {
        $otp = $request->satu . $request->dua . $request->tiga . $request->empat . $request->lima;
        $cek = User::where('otpcode', $otp)->first();
        if ($cek) {
            $cek->update([
                "is_verified" => "1",
                "otpcode" => ""
            ]);
			
			$upline_user_id = $cek->upline;
			
			if ($upline_user_id != "") {
				$uplines = User::where('id', $upline_user_id)->first();
				$uplines->update([
					'bonus_rp' => $uplines->bonus_rp+2000,
					'point' => $uplines->point + website_config('bonus_point')->upline
				]);
			}
			
            return response()->json([
                'status'  => true,
                'message' => 'Akun Anda Telah Aktif'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Otp Code Expired'
            ]);
        }
    }
    public function send_email($details = [], $to = '')
    {
        config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
        config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
        config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
        config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
        config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
        config(['mail.from.address' => website_config('smtp')->from]);
        config(['mail.from.name' => website_config('main')->website_name]);
        try {
            Mail::send('user.mail.activate_account', $details, function ($message) use ($details, $to) {
                $message
                    ->to($to, $details['name'])
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Aktivasi Akun - ' . website_config('main')->website_name . '');
            });
            return true;
        } catch (Exception $message) {
            return $message->getMessage();
        }
    }
    function randomOtp($length = 10)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

class RegisterRequest extends FormRequest
{
    protected function getValidatorInstance()
    {
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
    public function rules()
    {
        return [
            'full_name'        => 'required|max:30|regex:/^[\pL\s\-]+$/u|unique:users,full_name',
            'phone_number'     => 'required|numeric|phone:ID,mobile|unique:users,phone_number',
            'email'            => 'required|email|unique:users,email',
            'username'         => 'required|alpha_num|min:5|max:20|unique:users,username',
            'password'         => 'required|alpha_num|min:5|max:20',
            'confirm_password' => 'required|same:password',
            'approval'         => 'required|in:1',
        ];
    }
    public function attributes()
    {
        return [
            'full_name'        => 'Nama Lengkap',
            'phone_number'     => 'Nomor Telepon',
            'email'            => 'Email',
            'username'         => 'Username',
            'password'         => 'Password',
            'confirm_password' => 'Konfirmasi Password',
            'approval'         => 'Persetujuan',
        ];
    }
}
