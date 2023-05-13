<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Support\Arr;
use App\Models\UserLoginLog;
use App\Models\Penarikan;
use App\Models\Posting;
use App\Models\PostCategory;
use App\Models\PostingAnswer;
use Illuminate\Http\Request;
use App\Models\UserBalanceLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use App\DataTables\Admin\Log\BalanceDataTable;
use App\DataTables\User\Account\LoginLogDataTable;
use App\DataTables\User\Account\BalanceLogDataTable;
use Illuminate\Http\Exceptions\HttpResponseException;

class AccountController extends Controller
{
    public function create_api_key()
    {
        $update_user = User::where('id', Auth::user()->id)->update(['api_key' => create_api_key()]);
        if ($update_user == true) {
            return redirect('page/api_doc')->with('result', [
                'alert'   => 'success',
                'title'   => 'Berhasil',
                'message' => 'API Key berhasil diperbarui.'
            ]);
        }
    }
    public function profile()
    {
        $components['breadcrumb'] = (object) [
            'first'  => 'Profil',
            'second' => 'Akun'
        ];
        $components['answer'] = PostingAnswer::where('user_id', Auth::user()->id)->get();
        $components['listdata'] = Posting::where('user_id', Auth::user()->id)->get();
        return view('user.account.profile', $components);
    }
    public function login_log(LoginLogDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Log Masuk',
            'second' => 'Akun'
        ];
        $components['created_at'] = UserLoginLog::where('user_id', Auth::user()->id)->selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('user.account.login_log.list', $components);
    }
    public function balance_log(BalanceLogDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Log Saldo',
            'second' => 'Akun'
        ];
        $components['types'] = ['Minus', 'Plus'];
        $components['actions'] = ['Order', 'Deposit', 'Refund', 'Transfer', 'Bonus', 'Other'];
        $components['created_at'] = UserBalanceLog::where('user_id', Auth::user()->id)->selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('user.account.balance_log.list', $components);
    }
    public function withdraw()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Withdraw',
            'second' => 'Akun'
        ];

        $components['list'] = Penarikan::where('user_id', Auth::user()->id)->get();
        return view('user.account.withdraw', $components);
    }
    public function withdrawpost(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $user = User::where("id", Auth::user()->id)->first();
        $nominal = fixed_amount($request->nominal);
        if ($user->balance < $nominal) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Maaf Saldo Anda tidak mencukupi.'
            ]);
        }
        if ($nominal < website_config('bonus_point')->minwd) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Maaf Penarikan Saldo Minimum Rp.' . website_config('bonus_point')->minwd
            ]);
        }
        $user->update([
            "balance" => $user->balance - $nominal
        ]);
        $nominal2 =  $nominal - website_config('bonus_point')->adminwd;
        $input_data = [
            'user_id'           => Auth::user()->id,
            'amount'            => $nominal2,
            'bank'              => $request->bank,
            'rekening'           => $request->rekening,
            'nama'           => $request->nama,
        ];
        $insert_data = Penarikan::create($input_data);


        $text = "Permintaan penarikan anda sudah masuk ke dalam sistem, silahkan tunggu paling lama 2x24 jam untuk ditransfer ke rekening anda";
        send_watsapp($user->phone_number, $text);

        if ($insert_data == true) {
            return response()->json([
                'status'  => true,
                'type'    => 'alert',
                'message' => 'Penarikan berhasil. penarikan akan di proses dalam waktu 1x24 Jam'
            ]);
        }
    }
    public function getSettings()
    {
        // $user = json_decode(Auth::user()->notifcation)->order;
        // dd($user);
        // foreach (json_decode(Auth::user()->notifcation, true) as $key => $item) {
        // 	$array[$key] = $item;
        // 	if ($key == 'website_logo') {
        // 		$array[$key] = '';
        // 	}
        // }
        // dd($array);
        $components['breadcrumb'] = (object) [
            'first'  => 'Pengaturan',
            'second' => 'Akun'
        ];
        $components['notification'] = (object) [
            'order'   => "", //json_decode(Auth::user()->notification)->order,
            'deposit' => "", //json_decode(Auth::user()->notification)->deposit,
            'ticket'  => "" //json_decode(Auth::user()->notification)->ticket,
        ];
        return view('user.account.settings', $components);
    }
    public function postSettings(PostRequest $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::user()->username == 'demouser') {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $input_data = [
            'full_name' => escape_input($request->full_name),
            'password'  => escape_input($request->password),
        ];
        if (Hash::check($input_data['password'], Auth::user()->password) == true) {
            $input_data['password'] = escape_input(Hash::make($request->password));
            if ($request->new_password <> '') {
                $input_data['password'] = escape_input(Hash::make($request->new_password));
            }
            $update_data = User::where('id', Auth::user()->id)->update($input_data);
            return response()->json([
                'status'  => true,
                'message' => 'Informasi Akun berhasil diperbarui.'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Password salah.'
            ]);
        }
    }
    public function setNotification($type, $value, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        if (Auth::user()->username == 'demouser') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if (in_array($type, ['order', 'deposit', 'ticket']) == false) return (json_encode(['result' => $type], JSON_PRETTY_PRINT));
        if (Arr::exists(['0', '1'], $value) == false) return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        $check_data = User::find(Auth::user()->id);
        foreach (json_decode(Auth::user()->notification, true) as $key => $item) {
            $array[$key] = $item;
            if ($key == $type) {
                $array[$key] = $value;
            }
        }
        $update_data = User::where('id', Auth::user()->id)->update([
            'notification' => json_encode($array)
        ]);
        if ($update_data == true) {
            return (json_encode([
                'result'  => true,
                'message' => 'Notifikasi berhasil diperbarui.'
            ], JSON_PRETTY_PRINT));
        } else {
            return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        }
    }

    public function postUpdateProfile(ProfileRequest $request)
    {
        if ($request->gambar) {
            $imagepath = '/public/avatar/';
            $image_name =  time() . '.' . $request->gambar->extension() . '';
            $request->gambar->move(getcwd() . $imagepath, $image_name);

            $update_data = User::where('id', Auth::user()->id)->update([
                'full_name' => $request->full_name,
                'avatar' => $image_name,
            ]);
        } else {
            $update_data = User::where('id', Auth::user()->id)->update([
                'full_name' => $request->full_name,
            ]);
        }
        return (json_encode([
            'result'  => true,
            'message' => 'Profile berhasil diperbarui.'
        ], JSON_PRETTY_PRINT));
    }
}

class PostRequest extends FormRequest
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
        if (request('new_password') <> '' or request('confirm_new_password') <> '') {
            return [
                'full_name'            => 'required|min:5|max:30',
                'password'             => 'required|alpha_num|min:5|max:20',
                'new_password'         => 'alpha_num|min:5|max:15',
                'confirm_new_password' => 'same:new_password',
            ];
        } else {
            return [
                'full_name'            => 'required|min:5|max:30',
                'password'             => 'required|alpha_num|min:5|max:20',
            ];
        }
    }
    public function attributes()
    {
        if (request('new_password') <> '' or request('confirm_new_password') <> '') {
            return [
                'full_name'            => 'Nama Lengkap',
                'password'             => 'Password',
                'new_password'         => 'Password Baru',
                'confirm_new_password' => 'Konfirmasi Password Baru',
            ];
        } else {
            return [
                'full_name'            => 'Nama Lengkap',
                'password'             => 'Password',
                'new_password'         => 'Password Baru',
                'confirm_new_password' => 'Konfirmasi Password Baru',
            ];
        }
    }
}

class ProfileRequest extends FormRequest
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
        if (request('gambar')) {
            return [
                'full_name'            => 'required|min:5|max:30',
                'gambar'                => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        } else {
            return [
                'full_name'            => 'required|min:5|max:30',
            ];
        }
    }
    public function attributes()
    {
        if (request('gambar')) {
            return [
                'full_name'            => 'Nama Lengkap',
                'gambar'            => 'Foto Profile',
            ];
        } else {
            return [
                'full_name'            => 'Nama Lengkap',
            ];
        }
    }
}
