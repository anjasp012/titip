<?php

namespace App\Http\Controllers\User\Auth;

use Exception;
use App\Models\User;
use App\Mail\HelperEmail;
use App\Models\UserPasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordController extends Controller {
	public function __construct() {
        $this->middleware('guest');
	}
	public function getReset($reset_token = null) {
        if ($reset_token == null) {
            return view('user.auth.passwords.email');
        } else {
            $check_token = UserPasswordReset::where('token', $reset_token)->first();
            if ($check_token == false) return redirect('auth/reset');
            $components['target'] = $check_token;
            return view('user.auth.passwords.reset', $components);
        }
    }
    public function postReset(ResetRequest $request, $reset_token = null) {
		if ($request->ajax() == false) abort('404');
        if ($reset_token == null) {
            $input_data['email'] = escape_input($request->email);
            $user = User::where('email', $input_data['email'])->first();
            $input_data['token'] = md5($input_data['email'].'-'.$user->username);
            $check_data = UserPasswordReset::where('email', $input_data['email'])->first();
            if ($check_data == false) {
                $insert_data = UserPasswordReset::create($input_data);
                if ($insert_data == true) {
                    $details = [
                        'name' => $user->full_name,
                        'url'  => url('auth/reset/'.$input_data['token'])
                    ];
                    if ($this->send_email($details, $input_data['email']) == true) {
                        return response()->json([
                            'status'  => true, 
                            'message' => 'Silahkan periksa Email anda untuk mengatur ulang kata sandi akun Anda.'
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
            } else {
                $input_data['created_at'] = now();
                $update_data = UserPasswordReset::where('email', $input_data['email'])->update($input_data);
                if ($update_data == true) {
                    $details = [
                        'nama' => $user->full_name,
                        'url'  => url('auth/reset/'.$input_data['token'])
                    ];
                    if ($this->send_email($details, $input_data['email']) == true) {
                        return response()->json([
                            'status'  => true, 
                            'message' => 'Silahkan periksa Email anda untuk mengatur ulang kata sandi akun Anda.'
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
            }
        } else {
            $input_data['password'] = escape_input(Hash::make($request->password));
            $update_data = User::where('email', $request->email)->update($input_data);
            if ($update_data == true) {
                $delete_data = UserPasswordReset::where('email', $request->email)->delete();
                return response()->json([
                    'status'  => true, 
                    'message' => 'Password berhasil diatur ulang.'
                ]);
            } else {
                return response()->json([
                    'status'  => false, 
                    'type'    => 'alert',
                    'message' => 'Terjadi Kesalahan.'
                ]);
            }
        }
    }
    public function send_email($details = '', $to = '') {
		config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
		config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
		config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
		config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
		config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
		config(['mail.from.address' => website_config('smtp')->from]);
		config(['mail.from.name' => website_config('main')->website_name]);
		try {
            Mail::send('user.mail.reset_password', $details, function($message) use ($details, $to) {
                $message
                 ->to($to, $details['name'])
                 ->from(config('mail.from.address'), config('mail.from.name'))
                 ->subject('Atur Ulang Kata Sandi - '.website_config('main')->website_name.'');
             });
			return true;
		} catch (Exception $message) {
			return $message->getMessage();
		}
    }
}

class ResetRequest extends FormRequest {
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
        if (request()->segment(3) == null) {
            return [
                'email' => 'required|email|exists:users,email',
            ];
        } else {
            return [
                'email'            => 'required|email|exists:users,email',
                'password'         => 'required|alpha_num|min:5|max:15',
                'confirm_password' => 'required|same:password',
            ];
        }
    }
    public function attributes() {
        return [
            'email'            => 'Email',
            'password'         => 'Password',
            'confirm_password' => 'Konfirmasi Password'
        ];
    }
}