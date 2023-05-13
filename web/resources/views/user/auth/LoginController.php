<?php

namespace App\Http\Controllers\User\Auth;

use Exception;
use Laravel\Socialite\Facades\Socialite;

use App\Models\Order;
use App\Models\Deposit;
use App\Models\UserCookie;
use App\Models\Posting;
use App\Models\Social;
use App\Models\User;
use App\Models\Project;
use App\Models\WebsitePage;
use Illuminate\Support\Str;
use App\Models\UserLoginLog;
use Illuminate\Support\Carbon;
use App\Models\UserActivateAccount;
use App\Models\WebsiteInformation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('index', 'logout');
    }

    public function redirectToProvider($provider)
    {

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect('auth/login');
        }


        $authUser = $this->findOrCreateUser($user, $provider);
        Auth::login($authUser, true);
        return redirect($this->redirectTo);
    }
    public function findOrCreateUser($providerUser, $provider)
    {
        $account = Social::whereProviderName($provider)
            ->whereProviderId($providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        } else {
            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'full_name'  => $providerUser->getName(),
                    'username'  => $providerUser->getName(),
                ]);
            }

            $user->social()->create([
                'provider_id'   => $providerUser->getId(),
                'provider_name' => $provider,
            ]);

            return $user;
        }
    }

    public function index()
    {
        /*if (Auth::check() == true) {
            $components['listdata'] = Posting::orderBy('id', 'desc')->skip(0)->take(20)->get();
            return view('user.auth.index', $components);
        } else {
            return redirect('auth/login');
        }*/
        $components['listdata'] = Posting::orderBy('id', 'desc')->skip(0)->take(8)->get();
        $components['projectlist'] = Project::orderBy('id', 'desc')->skip(0)->take(8)->get();
        return view('user.auth.index', $components);
    }
    public function getLogin()
    {
        return view('user.auth.login');
    }
    public function postLogin(LoginRequest $request)
    {
        if ($request->ajax() == false) abort('404');
        $input_data = [
            'phone_number' => escape_input($request->username),
            'password' => escape_input($request->password),
        ];
        if (Auth::attempt($input_data) == true) {
            if (Auth::user()->status == 0) {
                Auth::logout();
                return response()->json([
                    'status'  => false,
                    'type'    => 'alert',
                    'message' => 'Akun dinonaktifkan.'
                ]);
            }
            if (Auth::user()->is_verified == '0' and website_config('main')->is_email_confirmation_enabled <> '') {
                $input_data['token'] = md5(Auth::user()->email . '-' . Auth::user()->username);
                $details = [
                    'name' => Auth::user()->full_name,
                    'url'  => url('auth/activate/' . $input_data['token'])
                ];
                $check_data = UserActivateAccount::where('email', Auth::user()->email)->first();
                if ($check_data == false) {
                    $insert_data = UserActivateAccount::create([
                        'email' => Auth::user()->email,
                        'token' => $input_data['token']
                    ]);
                    if ($insert_data == true) {
                        if ($this->send_email($details, Auth::user()->email) == true) {
                            Auth::logout();
                            return response()->json([
                                'status'  => false,
                                'type'    => 'alert',
                                'message' => 'Silahkan periksa Email anda untuk mengaktifkan akun Anda.'
                            ]);
                        } else {
                            Auth::logout();
                            return response()->json([
                                'status'  => false,
                                'type'    => 'alert',
                                'message' => 'Terjadi kesalahan.'
                            ]);
                        }
                    } else {
                        Auth::logout();
                        return response()->json([
                            'status'  => false,
                            'type'    => 'alert',
                            'message' => 'Terjadi kesalahan.'
                        ]);
                    }
                } else {
                    $input_data['created_at'] = now();
                    $update_data = $check_data->update([
                        'email' => Auth::user()->email,
                        'token' => $input_data['token']
                    ]);
                    if ($update_data == true) {
                        if ($this->send_email($details, Auth::user()->email) == true) {
                            Auth::logout();
                            return response()->json([
                                'status'  => false,
                                'type'    => 'alert',
                                'message' => 'Silahkan periksa Email anda untuk mengaktifkan akun Anda.'
                            ]);
                        } else {
                            Auth::logout();
                            return response()->json([
                                'status'  => false,
                                'type'    => 'alert',
                                'message' => 'Terjadi kesalahan.'
                            ]);
                        }
                    } else {
                        Auth::logout();
                        return response()->json([
                            'status'  => false,
                            'type'    => 'alert',
                            'message' => 'Terjadi kesalahan.'
                        ]);
                    }
                }
            }
            if ($request->remember  == '1') {
                $random_string = Str::random(40);
                UserCookie::create([
                    'user_id'       => Auth::user()->id,
                    'value'            => $random_string,
                    'expired_at'  => date('Y-m-d H:i:s', strtotime('next month'))
                ]);
                Cookie::queue(Cookie::make('user', $random_string, strtotime('next month')));
            }
            UserLoginLog::create([
                'user_id'    => Auth::user()->id,
                'ip_address' => $request->ip()
            ]);
            Session::put('information_popup', true);
            session()->flash('result', [
                'alert'   => 'success',
                'title'   => 'Berhasil',
                'message' => 'Selamat datang <b>' . Auth::user()->full_name . '</b>.'
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
    public function logout()
    {
        if (Auth::check() == false) return redirect('auth/login');
        if (Session::has('information_popup') == true) Session::forget('information_popup');
        if (Cookie::has('user') == true) {
            UserCookie::where('value', Cookie::get('user'))->delete();
            Cookie::queue(Cookie::forget('user'));
        }
        Auth::logout();
        return redirect('auth/login')->with('result', [
            'alert'   => 'success',
            'title'   => 'Berhasil',
            'message' => 'Sampai jumpa lagi...'
        ]);
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
}

class LoginRequest extends FormRequest
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
            'username' => 'required|exists:users,username',
            'password' => 'required|string'
        ];
    }
    public function attributes()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
        ];
    }
}
