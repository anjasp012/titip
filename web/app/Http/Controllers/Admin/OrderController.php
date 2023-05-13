<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Admin\OrderDataTable;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller {
    public function list(OrderDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Pesanan',
			'second' => 'Pemesanan'
        ];
        $components['users'] = Order::distinct()->latest('user_id')->get(['user_id']);
        $components['products'] = Order::distinct()->latest('product_id')->get(['product_id']);
        $components['product_providers'] = Order::distinct()->latest('provider_id')->get(['provider_id']);
        $components['statuses'] = ['Pending', 'Success', 'Error', 'Partial'];
        $components['created_at'] = Order::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.order.list', $components);
    }
    public function getForm(Order $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        $components['status_list'] = ['Pending', 'Success', 'Error', 'Partial'];
        return view('admin.order.form', $components);
    }
    public function postForm(Order $target, PostRequest $request) {
        if (Auth::guard('admin')->user()->level == 'Admin') {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
        if ($target->user == true AND json_decode($target->user->notification)->order == '1' AND $request->status <> $target->status) {
            $details = [
                'name'       => $target->user->full_name,
                'id'         => $target->id,
                'product'    => $target->product ? $target->product->name : null,
                'target'     => $target->target,
                'sn'         => $target->serial_number,
                'price'      => $target->price,
                'status'     => $request->status,
                'source'     => $target->is_api == '1' ? 'API' : 'WEB',
                'ip_address' => $target->ip_address,
            ];
            $this->send_email($details, $target->user->email);
        }
		$input_data = [
            'status'        => escape_input($request->status),
            'serial_number' => $request->serial_number,
		];
        $update_data = $target->update($input_data);
        return response()->json([
            'status'  => true, 
            'message' => 'Pesanan berhasil diperbarui.'
        ]);
	}
	public function delete(Order $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) return json_encode(['result' => true], JSON_PRETTY_PRINT);
	}
	public function detail(Order $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        return view('admin.order.detail', compact('target'));
    }
    public function report(Request $request) {
        $components = [
            'start_date' => date('Y-m-01'),
            'end_date'   => date('Y-m-t')
        ];
        $components['breadcrumb'] = (object) [
			'first'  => 'Laporan',
			'second' => 'Pemesanan'
        ];
        if ($request->start_date == true OR $request->end_date == true) {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date|date_format:d F Y',
                'end_date'   => 'required|date|date_format:d F Y',
            ], [], [
                'start_date' => 'Tanggal Mulai',
                'end_date' => 'Tanggal Berakhir',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        if ($request->start_date <> '') $components['start_date'] = date('Y-m-d', strtotime($request->start_date));
        if ($request->end_date <> '') $components['end_date'] = date('Y-m-d', strtotime($request->end_date));
        $components['orders'] = [
            'all'        => Order::whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Order::raw('SUM(orders.price) AS amount'), Order::raw('COUNT(orders.id) AS total'))->first(),
            'gross'      => Order::where('status', 'Success')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Order::raw('SUM(orders.price) AS amount'), Order::raw('COUNT(orders.id) AS total'))->first(),
            'net'        => Order::where('status', 'Success')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Order::raw('SUM(orders.profit) AS amount'), Order::raw('COUNT(orders.id) AS total'))->first(),
            'pending'    => Order::where('status', 'Pending')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Order::raw('SUM(orders.price) AS amount'), Order::raw('COUNT(orders.id) AS total'))->first(),
            'error'      => Order::where('status', 'Error')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Order::raw('SUM(orders.price) AS amount'), Order::raw('COUNT(orders.id) AS total'))->first(),
            'partial'    => Order::where('status', 'Partial')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Order::raw('SUM(orders.price) AS amount'), Order::raw('COUNT(orders.id) AS total'))->first(),
            'success'    => Order::where('status', 'Success')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Order::raw('SUM(orders.price) AS amount'), Order::raw('COUNT(orders.id) AS total'))->first(),
        ];
        return view('admin.order.report', $components);
    }
    public function send_email($details = [], $to = '') {
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
}

class PostRequest extends FormRequest {
	protected function prepareForValidation() {
		$this->merge([
			'remains'     => $this->remains <> '' ? fixed_amount($this->remains) : '',
			'start_count' => $this->start_count <> '' ? fixed_amount($this->start_count) : '',
		]);
	}
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
            'status'        => 'required|in:Pending,Success,Error,Partial',
            'serial_number' => 'required|string',
        ];
    }
    public function attributes() {
		return [
            'status'        => 'Status',
            'serial_number' => 'No. Serial',
		];
    }
}