<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\OrderBonus;
use Illuminate\Http\Request;
use App\Models\UserBalanceLog;
use App\Models\ProductCategory;
use App\Models\ProductProvider;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ProductSubCategory;
use App\Models\ServiceCustomPrice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\DataTables\User\OrderDataTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function getNew(ProductCategory $category)
    {
        $components = [
            'target'         => $category,
            'sub_categories' => ProductSubCategory::where('status', '1')->where('category_id', $category->id)->orderBy('id', 'desc')->get(),
        ];
        $components['breadcrumb'] = (object) [
            'first' => 'Pesan Baru - ' . $category->name . '',
            'second' => 'Pemesanan'
        ];
        return view('user.order.new', $components);
    }
    public function postNew(Product $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        $input_data = [
            'user_id'             => Auth::user()->id,
            'product_id'          => $target->id,
            'target'              => escape_input($request->target),
            'serial_number'       => 'Dalam proses...',
            'price'               => 0,
            'profit'              => 0,
            'bonus'               => 'Pending',
            'provider_id'         => 0,
            'provider_order_id'   => 0,
            'is_bonus'            => '0',
            'ip_address'          => $request->ip(),
            'provider_order_log'  => null,
            'provider_status_log' => null,
        ];
        $check_product = Product::find($input_data['product_id']);
        if ($check_product == false) {
            return json_encode(['result' => false, 'message' => 'Produk tidak tersedia'], JSON_PRETTY_PRINT);
        } else {
            if (Auth::user()->level == 'Reseller') {
                $input_data['price'] = $check_product->reseller_price;
                $input_data['profit'] = $check_product->reseller_price - $check_product->agen_price + ($check_product->profit);
            } else {
                $input_data['price'] = $check_product->agen_price;
                $input_data['profit'] = $check_product->profit;
            }
            $input_data['bonus'] = $check_product->bonus;
            $check_category = ProductCategory::find($check_product->category_id);
            $check_provider = ProductProvider::where([
                ['id', $check_product->provider_id],
                ['status', '1']
            ])->first();
            if ($check_provider == false) {
                exit(json_encode(['result' => false, 'message' => 'Produk tidak tersedia'], JSON_PRETTY_PRINT));
            } elseif (Auth::user()->balance < $input_data['price']) {
                exit(json_encode(['result' => false, 'message' => 'Saldo anda tidak mencukupi'], JSON_PRETTY_PRINT));
            } elseif ($check_category->name == 'Token PLN' and $request['target'] == null) {
                exit(json_encode(['result' => false, 'message' => 'Mohon untuk mengisi No. Meter/ID Pelanggan.'], JSON_PRETTY_PRINT));
            } else {
                $count_order = Order::latest('id')->value('id') + 1;
                $count_user_order = Order::where([
                    ['user_id', Auth::user()->id],
                    ['product_id', $input_data['product_id']],
                    ['target', $input_data['target']]
                ])->whereDate('created_at', date('Y-m-d'))->count() + 1;
                $input_data['provider_id'] = $check_product->provider_id;
                $order_response = false;
                if ($check_provider->name == 'PORTALPULSA') {
                    $header_api = array(
                        'portal-userid: ' . $check_provider->provider_id,
                        'portal-key: ' . $check_provider->provider_key,
                        'portal-secret: ' . $check_provider->provider_secret,
                    );
                    $post_api_check_balance = [
                        'inquiry' => 'S'
                    ];
                    if ($check_category->name == 'Token PLN' and $request['input_pln'] <> null) {
                        $config_input['target'] = $input_data['target'] . ' - IDPEL(' . escape_input($request['input_pln']) . ')';
                        $post_api = array(
                            'inquiry'   => 'PLN',
                            'code'      => $check_product->provider_product_id,
                            'phone'     => $input_data['target'],
                            'idcust'    => escape_input($request['input_pln']),
                            'trxid_api' => $count_order,
                            'no'        => $count_user_order,
                        );
                    } else {
                        $post_api = array(
                            'inquiry'   => 'I',
                            'code'      => $check_product->provider_product_id,
                            'phone'     => $input_data['target'],
                            'trxid_api' => $count_order,
                            'no'        => $count_user_order,
                        );
                    }
                    $curl_check_balance = post_curl($check_provider->provider_url_order, $header_api, $post_api_check_balance);
                    $curl_check_balance_response = json_decode($curl_check_balance, true);
                    if (isset($curl_check_balance_response['result']) and $curl_check_balance_response['result'] == 'failed') {
                        $order_response = false;
                    } else {
                        if ($curl_check_balance_response['balance'] < $input_data['price']) {
                            exit(json_encode(['result' => false, 'message' => 'Harap untuk menghubungi Admin.'], JSON_PRETTY_PRINT));
                        }
                    }
                    $curl = post_curl($check_provider->provider_url_order, $header_api, $post_api);
                    $curl_response = json_decode($curl, true);
                    if (isset($curl_response['result']) and $curl_response['result'] == 'failed') {
                        $order_response = false;
                        if ($curl_response['message'] == 'Invalid phone') {
                            exit(json_encode(['result' => false, 'message' => 'No. Handphone tidak valid.'], JSON_PRETTY_PRINT));
                        }
                    } else {
                        $order_response = true;
                    }
                    $input_data['id'] = $count_order;
                    $input_data['provider_order_id'] = $count_order;
                    $input_data['provider_order_log'] = $curl;
                } elseif ($check_provider->name == 'DIGIFLAZZ') {
                    $header_api = [
                        'Content-Type: application/json'
                    ];
                    $post_api = [
                        'username' => $check_provider->provider_id,
                        'sign' => md5($check_provider->provider_id . $check_provider->provider_key . $count_order),
                        'buyer_sku_code' => $check_product->provider_product_id,
                        'ref_id' => '' . $count_order . '',
                        'customer_no' => $input_data['target'],
                    ];
                    $curl = Http::post($check_provider->provider_url_order, $post_api);
                    $curl_response = json_decode($curl, true);
                    if (isset($curl_response['data']['status']) and $curl_response['data']['status'] == 'Gagal') {
                        $order_response = false;
                    } else {
                        $order_response = true;
                    }
                    if (isset($curl_response['data']['rc']) and $curl_response['data']['rc'] >= 41) {
                        $order_response = false;
                    } else {
                        $order_response = true;
                    }
                    $input_data['id'] = $count_order;
                    $input_data['provider_order_id'] = $count_order;
                    $input_data['provider_order_log'] = $curl;
                }
                if ($order_response == false) {
                    exit(json_encode(['result' => false, 'message' => 'Produk tidak tersedia.'], JSON_PRETTY_PRINT));
                } else {
                    $check_user = User::find(Auth::user()->id);
                    if ($check_user->upline <> 'System') {
                        $check_upline = User::where('username', $check_user->upline)->where('level', 'Reseller')->first();
                        if ($check_upline == true) {
                            $input_data['is_bonus'] = '1';
                        }
                    }
                    $insert_data = Order::create($input_data);
                    if ($insert_data == true) {
                        $insert_bonus = OrderBonus::create([
                            'user_id'  => $check_user->id,
                            'order_id' => $insert_data->id,
                            'amount'   => $input_data['bonus'],
                            'note'     => 'Bonus pesanan #' . $insert_data->id . '.',
                            'is_sent'  => '0'
                        ]);
                        $cut_balance = $check_user->update([
                            'balance' => Auth::user()->balance - $input_data['price'],
                        ]);
                        $balance_logs = UserBalanceLog::create([
                            'user_id'     => $input_data['user_id'],
                            'type'        => 'Minus',
                            'action'      => 'Order',
                            'amount'      => $input_data['price'],
                            'description' => 'Membuat Pesanan #' . $insert_data->id . '.',
                        ]);
                        if (json_decode(Auth::user()->notification)->order == '1') {
                            $details = [
                                'name'       => Auth::user()->full_name,
                                'id'         => $insert_data->id,
                                'product'    => $check_product->name,
                                'target'     => $input_data['target'],
                                'sn'         => $input_data['serial_number'],
                                'price'      => $input_data['price'],
                                'status'     => 'Pending',
                                'source'     => 'WEB',
                                'ip_address' => $request->ip(),
                            ];
                            $this->send_email_user($details, Auth::user()->email);
                        }
                        if (website_config('notification')->email <> '' and website_config('notification')->order == '1') {
                            $details = [
                                'username'   => Auth::user()->username,
                                'full_name'  => Auth::user()->full_name,
                                'id'         => $insert_data->id,
                                'product'    => $check_product->name,
                                'target'     => $input_data['target'],
                                'sn'         => $input_data['serial_number'],
                                'price'      => $input_data['price'],
                                'status'     => 'Pending',
                                'source'     => 'WEB',
                                'ip_address' => $request->ip(),
                            ];
                            $this->send_email_admin($details, website_config('notification')->email);
                        }
                        session()->flash('result', [
                            'alert'   => 'success',
                            'title'   => 'Berhasil',
                            'message' => '
                                <br />ID: ' . $insert_data->id . '
                            '
                        ]);
                        exit(json_encode(['result' => true, 'message' => 'Pembelian berhasil, ID Pesanan: ' . $insert_data->id . '.'], JSON_PRETTY_PRINT));
                    } else {
                        exit(json_encode(['result' => false, 'message' => 'Terjadi kesalahan.'], JSON_PRETTY_PRINT));
                    }
                }
            }
        }
    }
    public function history(OrderDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Riwayat',
            'second' => 'Pemesanan'
        ];
        $components['products'] = Order::where('user_id', Auth::user()->id)->distinct()->latest('product_id')->get(['product_id']);
        $components['statuses'] = ['Pending', 'Success', 'Error', 'Partial'];
        $components['created_at'] = Order::where('user_id', Auth::user()->id)->selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('user.order.history', $components);
    }
    public function detail(Order $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        return view('user.order.detail', compact('target'));
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
            Mail::send('user.mail.notification.order', $details, function ($message) use ($details, $to) {
                $message
                    ->to($to, $details['name'])
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Informasi Pesanan - ' . website_config('main')->website_name . '');
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
            Mail::send('admin.mail.notification.order', $details, function ($message) use ($details, $to) {
                $message
                    ->to($to, 'Admin')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Informasi Pesanan - ' . website_config('main')->website_name . '');
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
            'quantity' => $this->quantity <> '' ? fixed_amount($this->quantity) : '',
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
            'category_id' => 'required|numeric|exists:service_categories,id',
            'service_id'  => 'required|numeric|exists:services,id',
            'target'      => 'required',
            'quantity'    => 'required|numeric|integer|min:0',
            'approval'    => 'required|in:1',
        ];
    }
    public function attributes()
    {
        return [
            'category_id' => 'Kategori',
            'service_id'  => 'Layanan',
            'target'      => 'Target',
            'quantity'    => 'Jumlah',
            'approval'    => 'Persetujuan',
        ];
    }
}
