<?php

namespace App\Http\Controllers\User;

use App\Models\Deposit;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\DepositMethod;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\User\DepositDataTable;
use App\Http\Requests\User\DepositRequest;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;

class DepositController extends Controller
{
    public function getNew()
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Deposit Baru',
            'second' => 'Deposit'
        ];
        $components['deposit_methods'] = DepositMethod::where([['type', '<>', 'Direct'], ['status', '1']])->latest('id')->get();
        return view('user.deposit.new', $components);
    }
    public function postNew(PostRequest $request)
    {
        if ($request->ajax() == false) abort('404');
        /*if (Auth::user()->username == 'demouser') {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }*/
        $input_data = [
            'user_id'           => Auth::user()->id,
            'deposit_method_id' => escape_input($request->deposit_method_id),
            'amount'            => fixed_amount($request->amount),
            'balance'           => 0,
            'status'            => 'Pending',
            'ip_address'        => $request->ip()
        ];
        $check_data = [
            'deposit_method' => DepositMethod::find($input_data['deposit_method_id']),
            'deposit_limit'  => Deposit::where([['user_id', Auth::user()->id], ['status', 'Pending']])->whereDate('created_at', date('Y-m-d'))->get(),
        ];
        if ($input_data['amount'] < $check_data['deposit_method']['min']) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Minimal deposit adalah Rp ' . number_format($check_data['deposit_method']['min'], 0, ',', '.') . '.'
            ]);
        } elseif ($check_data['deposit_limit']->count() >= 3) {
            return response()->json([
                'status'  => false,
                'type'    => 'alert',
                'message' => 'Anda masih memiliki ' . $check_data['deposit_limit']->count() . ' Deposit berstatus Pending.'
            ]);
        } else {
            /*if ($check_data['deposit_method']['payment'] == 'Bank' and $check_data['deposit_method']['type'] == 'Auto') {
                for ($i = 0; $i < 100; $i++) {
                    $input_data['amount'] = $input_data['amount'] + rand(100, 1000);
                    if (Deposit::where('amount', $input_data['amount'])->first() == true) continue;
                    break;
                }
            }*/

            $input_data['balance'] = $input_data['amount'] * $check_data['deposit_method']['rate'];
            $input_data['deposit_method_name'] = $check_data['deposit_method']['name'];
            $insert_data = Deposit::create($input_data);
            if ($insert_data == true) {
                $datapost = [
                    'method'            => $check_data['deposit_method']['merchant_code'],
                    'merchant_ref'      => $insert_data->id,
                    'amount'            => fixed_amount($request->amount),
                    'customer_name'     => 'User_' . Auth::user()->id,
                    'customer_email'    => 'user_' . Auth::user()->id . "@titiptugas.com",
                    'customer_phone'    => '',
                    'order_items'       => [
                        [
                            'sku'       => 'Topup Saldo' . $insert_data->id,
                            'name'      => 'Topup Saldo',
                            'price'     => fixed_amount($request->amount),
                            'quantity'  => 1
                        ]
                    ],
                    'callback_url'      => 'https://titiptugas.com/ipntripay',
                    'return_url'        => 'https://titiptugas.com/ipntripay',
                    'expired_time'      => (time() + (24 * 60 * 60)), // 24 jam
                    'signature'         => hash_hmac('sha256', $check_data['deposit_method']['api_id'] . $insert_data->id . fixed_amount($request->amount), $check_data['deposit_method']['api_private'])
                ];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_FRESH_CONNECT     => true,
                    CURLOPT_URL               => "https://tripay.co.id/api/transaction/create",
                    CURLOPT_RETURNTRANSFER    => true,
                    CURLOPT_HEADER            => false,
                    CURLOPT_HTTPHEADER        => array(
                        "Authorization: Bearer " . $check_data['deposit_method']['api_public']
                    ),
                    CURLOPT_FAILONERROR       => false,
                    CURLOPT_POST              => true,
                    CURLOPT_POSTFIELDS        => http_build_query($datapost)
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                //var_dump($response);
                curl_close($curl);
                if (!empty($err)) {
                    echo "nodata";
                } else {
                    $json = json_decode($response);

                    $ket = $json->data->instructions;
                    $qr = @$json->data->qr_url;
                    $pay_code = @$json->data->pay_code;
                    $updatetrx = Deposit::where('id', $insert_data->id)->update(['server_note' => $ket, 'qr_image' => $qr, 'pay_code' => $pay_code]);
                    return response()->json([
                        'status'  => true,
                        'target'  => "/deposit/detail/$insert_data->id",
                        'message' => 'Deposit anda segera diproses, lunasi pembayaran anda.'
                    ]);
                }
                /*if (json_decode(Auth::user()->notification)->deposit == '1') {
                    $details = [
                        'name'       => Auth::user()->full_name,
                        'id'         => $insert_data->id,
                        'method'     => $check_data['deposit_method']['name'] . ' (' . $check_data['deposit_method']['payment'] . ' - ' . $check_data['deposit_method']['type'] . ')',
                        'amount'     => $input_data['amount'],
                        'status'     => 'Pending',
                        'ip_address' => $request->ip(),
                    ];
                    $this->send_email_user($details, Auth::user()->email);
                }
                /*
                if (website_config('notification')->email <> '' and website_config('notification')->deposit == '1') {
                    $details = [
                        'username'   => Auth::user()->username,
                        'full_name'  => Auth::user()->full_name,
                        'id'         => $insert_data->id,
                        'method'     => $check_data['deposit_method']['name'] . ' (' . $check_data['deposit_method']['payment'] . ' - ' . $check_data['deposit_method']['type'] . ')',
                        'amount'     => $input_data['amount'],
                        'status'     => 'Pending',
                        'ip_address' => $request->ip(),
                    ];
                    $this->send_email_admin($details, website_config('notification')->email);
                }
                session()->flash('result', [
                    'alert'   => 'success',
                    'title'   => 'Berhasil',
                    'message' => '
                        <br /><b>ID:</b> ' . $insert_data->id . '
                        <br /><b>Metode:</b> ' . $check_data['deposit_method']['name'] . '
                        <br /><b>Jumlah:</b> Rp ' . number_format($input_data['amount'], 0, ',', '.') . '
                        <br /><b>Catatan:</b> ' . $check_data['deposit_method']['note'] . '
                        <br /><b>Harap men-transfer sesuai jumlah deposit.</b>
                    '
                ]);
                return response()->json([
                    'status'  => true,
                    'target'  => $insert_data->id,
                    'message' => 'Deposit anda segera diproses, lunasi pembayaran anda.'
                ]);*/
            } else {
                return response()->json([
                    'status'  => false,
                    'type'    => 'alert',
                    'message' => 'Terjadi kesalahan.'
                ]);
            }
        }
    }
    public function history(DepositDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Riwayat',
            'second' => 'Deposit'
        ];
        $components['methods'] = Deposit::where('user_id', Auth::user()->id)->get(['deposit_method_id', 'deposit_method_name'])->unique('deposit_method_id')->unique('deposit_method_name');
        $components['statuses'] = ['Pending', 'Success', 'Canceled'];
        $components['created_at'] = Deposit::where('user_id', Auth::user()->id)->selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('user.deposit.history', $components);
    }
    public function detail(Deposit $target, Request $request)
    {
        //if ($request->ajax() == false) abort('404');
        if ($target->user_id <> Auth::user()->id) abort('404');
        $components['deposit_methods'] = DepositMethod::where('id', $target->deposit_method_id)->first();
        $components['target'] = Deposit::where('id', $target->id)->first();
        return view('user.deposit.detail', $components);
    }
    public function send_email_user($details = [], $to = '')
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
    public function send_email_admin($details = [], $to = '')
    {
        config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
        config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
        config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
        config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
        config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
        config(['mail.from.address' => website_config('smtp')->from]);
        config(['mail.from.name' => website_config('main')->website_name]);
        try {
            Mail::send('admin.mail.notification.deposit', $details, function ($message) use ($details, $to) {
                $message
                    ->to($to, 'Admin')
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
            'amount'            => 'required|numeric|integer|min:0',

        ];
    }
    public function attributes()
    {
        return [
            'deposit_method_id' => 'Metode',
            'amount'            => 'Jumlah',

        ];
    }
}
