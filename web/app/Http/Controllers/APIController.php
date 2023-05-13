<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderBonus;
use Illuminate\Http\Request;
use App\Models\UserBalanceLog;
use App\Models\ProductCategory;
use App\Models\ProductProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class APIController extends Controller {
    public function __construct(Request $request) {
        $this->middleware('API');
    }
    public function profile(Request $request) {
        $user = $request->get('user');
        return response()->json([
            'status' => true,
            'data'   => [
                'full_name' => $user->full_name,
                'username'  => $user->username,
                'balance'   => $user->balance,
            ],
        ], 200, [], JSON_PRETTY_PRINT);
    }
    public function products() {
        $products = Product::join('product_categories', 'products.category_id', '=', 'product_categories.id')->join('product_sub_categories', 'products.sub_category_id', '=', 'product_sub_categories.id')->select('products.provider_product_id AS product_id', 'product_categories.name AS category', 'product_sub_categories.name AS sub_category', 'products.name AS product_name', 'products.agen_price', 'products.reseller_price', 'products.status')->get();
        return response()->json([
            'status' => true,
            'data'   => $products,
        ], 200, [], JSON_PRETTY_PRINT);
    }
    public function status(StatusRequest $request) {
        $user = $request->get('user');
        $check_order = Order::where([
            ['user_id', $user->id],
            ['id', $request['id']]
        ])->first();
        if ($check_order == false) {
            return response()->json([
                'status'  => false,
                'data'    => [
                    'message' => 'Pesanan tidak ditemukan.'
                ]
            ], 403, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json([
                'status' => true,
                'data'   => [
                    'target'        => $check_order->target,
                    'serial_number' => $check_order->serial_number,
                    'status'        => $check_order->status
                ],
            ], 200, [], JSON_PRETTY_PRINT);
        }
    }
    public function order(OrderRequest $request) {
        $user = $request->get('user');
        $input_data = [
            'user_id'             => $user->id,
            'product_id'          => escape_input($request['product']),
            'target'              => escape_input($request['target']),
            'serial_number'       => 'Dalam proses...',
            'price'               => 0,
            'profit'              => 0,
            'status'               => 'Pending',
            'provider_id'         => 0,
            'provider_order_id'   => 0,
            'is_api'              => '1',
            'is_bonus'            => '0',
            'ip_address'          => request()->ip(),
            'provider_order_log'  => null,
            'provider_status_log' => null,
        ];
        $check_product = Product::where([
            ['provider_product_id', $input_data['product_id']],
            ['status', '1']
        ])->first();
		if ($check_product == false) {
            return response()->json([
                'status'  => false,
                'data'    => [
                    'message' => 'Produk tidak tersedia.'
                ]
            ], 403, [], JSON_PRETTY_PRINT);
        } else {
            if ($user->level == 'Reseller') {
                $input_data['price'] = $check_product->reseller_price;
                $input_data['profit'] = $check_product->reseller_price - $check_product->agen_price + ($check_product->profit);
            } else {
                $input_data['price'] = $check_product->agen_price;
                $input_data['profit'] = $check_product->profit;
            }
            $input_data['product_id'] = $check_product->id;
            $input_data['bonus'] = $check_product->bonus;
            $check_category = ProductCategory::find($check_product->category_id);
            $check_provider = ProductProvider::where([
                ['id', $check_product->provider_id],
                ['status', '1']
            ])->first();
            if ($check_provider == false) {
                return response()->json([
                    'status'  => false,
                    'data'    => [
                        'message' => 'Produk tidak tersedia.'
                    ]
                ], 403, [], JSON_PRETTY_PRINT);
            } elseif ($user->balance < $input_data['price']) {
                return response()->json([
                    'status'  => false,
                    'data'    => [
                        'message' => 'Saldo anda tidak mencukupi.'
                    ]
                ], 403, [], JSON_PRETTY_PRINT);
            } elseif ($check_category->name == 'Token PLN' AND $request['target'] == null) {
                return response()->json([
                    'status'  => false,
                    'data'    => [
                        'message' => 'Mohon untuk mengisi No. Meter/ID Pelanggan.'
                    ]
                ], 403, [], JSON_PRETTY_PRINT);
            } else {
                $count_order = Order::latest('id')->value('id') + 1;
                $count_user_order = Order::where([
                    ['user_id', $user->id],
                    ['product_id', $input_data['product_id']],
                    ['target', $input_data['target']]
                ])->whereDate('created_at', date('Y-m-d'))->count() + 1;
                $input_data['provider_id'] = $check_product->provider_id;
                $order_response = false;
                if ($check_provider->name == 'PORTALPULSA') {
                    $header_api = array(
                        'portal-userid: '.$check_provider->provider_id,
                        'portal-key: '.$check_provider->provider_key,
                        'portal-secret: '.$check_provider->provider_secret,
                    );
                    $post_api_check_balance = [
                        'inquiry' => 'S'
                    ];
                    if ($check_category->name == 'Token PLN' AND $request['input_pln'] <> null) {
                        $config_input['target'] = $input_data['target'].' - IDPEL('.escape_input($request['input_pln']).')';
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
                    if (isset($curl_check_balance_response['result']) AND $curl_check_balance_response['result'] == 'failed') {
                        $order_response = false;
                    } else {
                        if ($curl_check_balance_response['balance'] < $input_data['price']) {
                            return response()->json([
                                'status'  => false,
                                'data'    => [
                                    'message' => 'Harap untuk menghubungi Admin.'
                                ]
                            ], 403, [], JSON_PRETTY_PRINT);
                        }
                    }
                    $curl = post_curl($check_provider->provider_url_order, $header_api, $post_api);
                    $curl_response = json_decode($curl, true);
                    if (isset($curl_response['result']) AND $curl_response['result'] == 'failed') {
                        $order_response = false;
                        if ($curl_response['message'] == 'Invalid phone') {
                            return response()->json([
                                'status'  => false,
                                'data'    => [
                                    'message' => 'No. Handphone tidak valid.'
                                ]
                            ], 403, [], JSON_PRETTY_PRINT);
                        }
                    } else {
                        $order_response = true;
                    }
                    $input_data['provider_order_id'] = $count_order;
                    $input_data['provider_order_log'] = $curl;
                } elseif ($check_provider->name == 'DIGIFLAZZ') {
                    $header_api = [
                        'Content-Type: application/json'
                    ];
                    $post_api = [
                        'username' => $check_provider->provider_id,
                        'sign' => md5($check_provider->provider_id.$check_provider->provider_key.$count_order),
                        'buyer_sku_code' => $check_product->provider_product_id,
                        'ref_id' => ''.$count_order.'',
                        'customer_no' => $input_data['target'],
                    ];
                    $curl = Http::post($check_provider->provider_url_order, $post_api);
                    $curl_response = json_decode($curl, true);
                    if (isset($curl_response['data']['status']) AND $curl_response['data']['status'] == 'Gagal') {
                        $order_response = false;
                    } else {
                        $order_response = true;
                    }
                    if (isset($curl_response['data']['rc']) AND $curl_response['data']['rc'] >= 41) {
                        $order_response = false;
                    } else {
                        $order_response = true;
                    }
                    $input_data['id'] = $count_order;
                    $input_data['provider_order_id'] = $count_order;
                    $input_data['provider_order_log'] = $curl;
                }
                if ($order_response == false) {
                    return response()->json([
                        'status'  => false,
                        'data'    => [
                            'message' => 'Produk tidak tersedia.'
                        ]
                    ], 403, [], JSON_PRETTY_PRINT);
                } else {
                    $check_user = User::find($user->id);
                    if ($check_user->uplink <> 'System') {
                        $check_uplink = User::where('username', $check_user->uplink)->first();
                        if ($check_uplink == true AND $check_uplink->is_premium == '1') {
                            $input_data['is_bonus'] = '1';
                        }
                    }
                    $insert_data = Order::create($input_data);
                    if ($insert_data == true) {
                        $insert_bonus = OrderBonus::create([
                            'user_id'  => $check_user->id,
                            'order_id' => $insert_data->id,
                            'amount'   => $input_data['bonus'],
                            'note'     => 'Bonus pesanan #'.$insert_data->id.'.',
                            'is_sent'  => '0'
                        ]);
                        $cut_balance = $check_user->update([
                            'balance' => $user->balance - $input_data['price'],
                        ]);
                        $balance_logs = UserBalanceLog::create([
                            'user_id'     => $input_data['user_id'],
                            'type'        => 'Minus',
                            'action'      => 'Order',
                            'amount'      => $input_data['price'],
                            'description' => 'Membuat Pesanan #'.$insert_data->id.'.',
                        ]);
                        if (json_decode($check_user->notification)->order == '1') {
                            $details = [
                                'name'       => $check_user->full_name,
                                'id'         => $insert_data->id,
                                'product'    => $check_product->name,
                                'target'     => $input_data['target'],
                                'sn'         => $input_data['serial_number'],
                                'price'      => $input_data['price'],
                                'status'     => 'Pending',
                                'source'     => 'API',
                                'ip_address' => $request->ip(),
                            ];
                            $this->send_email_user($details, $check_user->email);
                        }
                        if (website_config('notification')->email <> '' AND website_config('notification')->order == '1') {
                            $details = [
                                'username'   => $check_user->username,
                                'full_name'  => $check_user->full_name,
                                'id'         => $insert_data->id,
                                'product'    => $check_product->name,
                                'target'     => $input_data['target'],
                                'sn'         => $input_data['serial_number'],
                                'price'      => $input_data['price'],
                                'status'     => 'Pending',
                                'source'     => 'API',
                                'ip_address' => $request->ip(),
                            ];
                            $this->send_email_admin($details, website_config('notification')->email);
                        }
                        return response()->json([
                            'status'  => true,
                            'data'    => [
                                'id' => $insert_data->id
                            ]
                        ], 200, [], JSON_PRETTY_PRINT);
                    } else {
                        return response()->json([
                            'status'  => false,
                            'data'    => [
                                'message' => 'Terjadi kesalahan.'
                            ]
                        ], 403, [], JSON_PRETTY_PRINT);
                    }
                }
            }
		}
    }
    public function send_email_user($details = [], $to = '') {
		config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
		config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
		config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
		config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
		config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
		config(['mail.from.address' => website_config('smtp')->from]);
		config(['mail.from.name' => website_config('main')->website_name]);
		try {
            Mail::send('user.mail.notification.order', $details, function($message) use ($details, $to) {
                $message
                 ->to($to, $details['name'])
                 ->from(config('mail.from.address'), config('mail.from.name'))
                 ->subject('Informasi Pesanan - '.website_config('main')->website_name.'');
             });
			return true;
		} catch (Exception $message) {
			return true;
		}
    }
    public function send_email_admin($details = [], $to = '') {
		config(['mail.mailers.smtp.username' => website_config('smtp')->username]);
		config(['mail.mailers.smtp.password' => website_config('smtp')->password]);
		config(['mail.mailers.smtp.encryption' => website_config('smtp')->encryption]);
		config(['mail.mailers.smtp.port' => website_config('smtp')->port]);
		config(['mail.mailers.smtp.host' => website_config('smtp')->host]);
		config(['mail.from.address' => website_config('smtp')->from]);
		config(['mail.from.name' => website_config('main')->website_name]);
		try {
            Mail::send('admin.mail.notification.order', $details, function($message) use ($details, $to) {
                $message
                 ->to($to, 'Admin')
                 ->from(config('mail.from.address'), config('mail.from.name'))
                 ->subject('Informasi Pesanan - '.website_config('main')->website_name.'');
             });
			return true;
		} catch (Exception $message) {
			return true;
		}
    }
}

class OrderRequest extends FormRequest {
    protected function getValidatorInstance() {
		$instance = parent::getValidatorInstance();
        if ($instance->fails() == true) {
            foreach ($instance->errors()->getMessages() as $key => $value) {
                $error_key[] = $value;
            }
            foreach($error_key as $key => $value) {
                $error_message[] = strtolower(str_replace("- ", "", $value[0]));
            }
			throw new HttpResponseException(response()->json([
				'status'  => false, 
                'data' => [
                    'message' => $error_message,
                ]
			]));
		}
        return parent::getValidatorInstance();
    }
    public function rules(Request $request) {
        return [
            'product'  => 'required|string|exists:products,provider_product_id',
            'target'   => 'required|string',
        ];
    }
    public function attributes() {
		return [
            'product'  => 'product',
            'target'   => 'target',
		];
    }
}

class StatusRequest extends FormRequest {
    protected function getValidatorInstance() {
		$instance = parent::getValidatorInstance();
        if ($instance->fails() == true) {
            foreach ($instance->errors()->getMessages() as $key => $value) {
                $error_key[] = $value;
            }
            foreach($error_key as $key => $value) {
                $error_message[] = strtolower(str_replace("- ", "", $value[0]));
            }
			throw new HttpResponseException(response()->json([
				'status'  => false, 
                'data' => [
                    'message' => $error_message,
                ]
			]));
		}
        return parent::getValidatorInstance();
    }
    public function rules(Request $request) {
        return [
            'id' => 'required|numeric|integer|exists:orders,id',
        ];
    }
    public function attributes() {
		return [
            'id' => 'id',
		];
    }
}
