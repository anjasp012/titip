<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Models\User;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\DepositMethod;
use App\Models\UserBalanceLog;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;

class SendController extends Controller
{
    public function getForm(Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $components['methods'] = DepositMethod::where('status', '1')->latest('id')->get();
        $components['users'] = User::where('status', '1')->latest('id')->get();
        return view('admin.deposit.send.form', $components);
    }
    public function postForm(PostRequest $request)
    {
        if ($request->ajax() == false) abort('404');
        /*if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }*/
        $input_data = [
            'user_id'           => escape_input($request->user_id),
            'deposit_method_id' => escape_input($request->deposit_method_id),
            'amount'            => fixed_amount($request->amount),
            'balance'           => fixed_amount($request->amount),
            'status'            => 'Success',
        ];
        $user = User::find($input_data['user_id']);
        $insert_data = Deposit::create($input_data);
        $add_balance = $user->update([
            'balance' => $user->balance + $input_data['balance'],
        ]);
        $balance_logs = UserBalanceLog::create([
            'user_id'     => $input_data['user_id'],
            'type'        => 'Plus',
            'action'      => 'Deposit',
            'amount'      => $input_data['balance'],
            'description' => 'Topup Saldo By Admin #' . $insert_data->id . '.',
        ]);

        /*if (json_decode($user->notification)->deposit == '1') {
            $details = [
                'name'       => $user->full_name,
                'id'         => $insert_data->id,
                'method'     => $insert_data->deposit_method ? $insert_data->deposit_method->name . ' (' . $insert_data->deposit_method->payment . ' - ' . $insert_data->deposit_method->type . ')' : null,
                'amount'     => $insert_data->amount,
                'status'     => 'Direct',
            ];
            $this->send_email($details, $user->email);
        }*/
        return response()->json([
            'status'  => true,
            'message' =>
            '
                <br /><b>Pengguna:</b> ' . $user->username . ' (' . $user->full_name . ')
                <br /><b>Jumlah:</b> Rp ' . number_format($input_data['amount'], 0, ',', '.') . '
                <br /><b>Saldo didapat:</b> Rp ' . number_format($input_data['balance'], 0, ',', '.') . '
            '
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
            Mail::send('user.mail.notification.deposit', $details, function ($message) use ($details, $to) {
                $message
                    ->to($to, $details['name'])
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Informasi Deposit - ' . website_config('main')->website_name . '');
            });
            return true;
        } catch (Exception $message) {
            return true;
        }
    }
}

class PostRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => $this->amount <> '' ? fixed_amount($this->amount) : '',
        ]);
    }
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
    public function rules(Request $request)
    {
        return [
            'deposit_method_id' => 'required|numeric|exists:deposit_methods,id',
            'user_id'           => 'required|numeric|exists:users,id',
            'amount'            => 'required|numeric|integer|min:0',
        ];
    }
    public function attributes()
    {
        return [
            'deposit_method_id' => 'Metode',
            'user_id'           => 'Pengguna',
            'amount'            => 'Jumlah',
        ];
    }
}
