<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Deposit;
use App\Models\OrderBonus;
use App\Models\Penarikan;
use App\Models\TicketReply;
use Illuminate\Support\Arr;
use App\Models\UserLoginLog;
use Illuminate\Http\Request;
use App\Models\UserBalanceLog;
use App\Models\UserRegisterLog;
use App\Models\ServiceCustomPrice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\DataTables\Admin\UserDataTable;
use App\Models\ExchangePoint;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
	public function list(UserDataTable $dataTable)
	{
		$components['breadcrumb'] = (object) [
			'first' => 'Daftar Pengguna',
			'second' => website_config('main')->website_name
		];
		$components['levels'] = ['Member', 'Moderator'];
		$components['statuses'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
		$components['created_at'] = User::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
		return $dataTable->render('admin.user.list', $components);
	}
	public function getForm(User $target, Request $request)
	{
		if ($request->ajax() == false) abort('404');
		if ($target == true) $components['target'] = $target;
		$components['levels'] = ['Member', 'Moderator'];
		return view('admin.user.form', $components);
	}
	public function postForm(User $target, PostRequest $request)
	{
		if ($request->ajax() == false) abort('404');
		if (Auth::guard('admin')->user()->level == 'Developer') {
			return response()->json([
				'status'  => false,
				'type'    => 'alert',
				'message' => 'Aksi tidak diperbolehkan.'
			]);
		}

		if (fixed_amount($request->balance) > $target->balance) {
			$amounts = fixed_amount($request->balance) - $target->balance;
			$balance_logs = UserBalanceLog::create([
				'user_id'     => $target->id,
				'order_id'    => "0",
				'type'        => 'Plus',
				'action'      => 'Transfer',
				'amount'      => $amounts,
				'description' => 'Topup Saldo By Admin',
			]);
		}

		$input_data = [
			'username'      => escape_input($request->username),
			'full_name'     => escape_input($request->full_name),
			'email' 	    => escape_input($request->email),
			'phone_number'  => escape_input($request->phone_number),
			'balance'       => fixed_amount($request->balance),
			'point'       => fixed_amount($request->point),
			/*'level'         => $request->level,
			'is_verified'	=> '1',
			'upline'	    => 'Admin',
			'referral_code' => sha1(escape_input($request->username)),*/
		];
		if (request()->segment(4) == null) {
			$input_data['password'] = escape_input(Hash::make($request->password));
			$input_data['api_key']  = create_api_key();
		}
		if ($target->id <> null) {
			if ($request->password <> '') {
				$input_data['password'] = escape_input(Hash::make($request->password));
			}
			if ($request->api_key <> '') {
				$input_data['api_key'] = escape_input($request->api_key);
			}
			$check_data = User::where([
				['username', $input_data['username']]
			])->first();
			if ($input_data['username'] <> $target['username'] and $check_data) {
				$validator = Validator::make($request->all(), [
					'username' => 'required|unique:users,username|max:20',
				], [], ['username' => 'Username']);
				if ($validator->fails()) {
					return response()->json([
						'status'  => false,
						'type'    => 'validation',
						'message' => $validator->errors()->toArray()
					]);
				}
			}
			$check_data = User::where([
				['full_name', $input_data['full_name']]
			])->first();
			if ($input_data['full_name'] <> $target['full_name'] and $check_data) {
				$validator = Validator::make($request->all(), [
					'full_name' => 'required|unique:users,full_name|max:30',
				], [], ['full_name' => 'Nama Lengkap']);
				if ($validator->fails()) {
					return response()->json([
						'status'  => false,
						'type'    => 'validation',
						'message' => $validator->errors()->toArray()
					]);
				}
			}
			$check_data = User::where([
				['email', $input_data['email']]
			])->first();
			if ($input_data['email'] <> $target['email'] and $check_data) {
				$validator = Validator::make($request->all(), [
					'email' => 'required|email|unique:users,email',
				], [], ['email' => 'Email']);
				if ($validator->fails()) {
					return response()->json([
						'status'  => false,
						'type'    => 'validation',
						'message' => $validator->errors()->toArray()
					]);
				}
			}
			$check_data = User::where([['phone_number', $input_data['phone_number']]])->first();
			if ($input_data['phone_number'] <> $target['phone_number'] and $check_data) {
				$validator = Validator::make($request->all(), [
					'phone_number' => 'required|numeric|phone:ID,mobile|unique:users,phone_number',
				], [], ['phone_number' => 'Nomor Telepon']);
				if ($validator->fails()) {
					return response()->json([
						'status'  => false,
						'type'    => 'validation',
						'message' => $validator->errors()->toArray()
					]);
				}
			}
			$update_data = $target->update($input_data);
			return response()->json([
				'status'  => true,
				'message' => 'Pengguna berhasil diperbarui.'
			]);
		} else {
			$insert_data = User::create($input_data);
			return response()->json([
				'status'  => true,
				'message' => 'Pengguna berhasil ditambahkan.'
			]);
		}
	}
	public function status(User $target, $status, Request $request)
	{
		if ($request->ajax() == false) abort('404');
		if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if (Arr::exists(['0', '1'], $status) == false) return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		$update_data = $target->update(['status' => $status]);
		if ($update_data == true) {
			return (json_encode([
				'result'  => true,
				'message' => 'Status Pengguna <b>' . $target->full_name . '</b> berhasil diperbarui.'
			], JSON_PRETTY_PRINT));
		} else {
			return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		}
	}
	public function delete(User $target, Request $request)
	{
		if ($request->ajax() == false) abort('404');
		if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) {
			$order = Order::where('user_id', $target->id)->delete();
			$order_bonus = OrderBonus::where('user_id', $target->id)->delete();
			$exchange_point = ExchangePoint::where('user_id', $target->id)->delete();
			$deposit = Deposit::where('user_id', $target->id)->delete();
			$register_log = UserRegisterLog::where('user_id', $target->id)->delete();
			$login_log = UserLoginLog::where('user_id', $target->id)->delete();
			$balance_log = UserBalanceLog::where('user_id', $target->id)->delete();
			$ticket = Ticket::where('user_id', $target->id)->delete();
			$ticket_reply = TicketReply::where('user_id', $target->id)->delete();
			return json_encode(['result' => true], JSON_PRETTY_PRINT);
		}
	}
	public function detail(User $target, Request $request)
	{
		if ($request->ajax() == false) abort('404');
		return view('admin.user.detail', compact('target'));
	}
	public function pendingwd()
	{
		$components['breadcrumb'] = (object) [
			'first' => 'Withdraw Pending',
			'second' => website_config('main')->website_name
		];
		$components['list'] = Penarikan::where("status", "pending")->get();
		return view('admin.user.withraw_pending', $components);
	}
	public function historywd()
	{

		$components['breadcrumb'] = (object) [
			'first' => 'Withdraw History',
			'second' => website_config('main')->website_name
		];
		$components['list'] = Penarikan::get();
		return view('admin.user.withraw_history', $components);
	}

	function approvewd(Request $request)
	{
		if ($request->ajax() == false) abort('404');

		$cek = Penarikan::where("id", $request->dataid)->first();

		if ($cek) {
			$cek->update([
				"status" => "Sukses"
			]);

			$user = User::where("id", $cek->user_id)->first();
			$text = "Selamat, pencairan uang anda telah selesai dilakukan. Silahkan cek rekening anda (titiptugas.com)";
			send_watsapp($user->phone_number, $text);

			return response()->json([
				'status'  => true,
				'type'    => 'alert',
				'message' => 'Wihtdraw Telah Di Update'
			]);
		} else {
			return response()->json([
				'status'  => false,
				'type'    => 'alert',
				'message' => 'Proses Wihtdraw Gagal'
			]);
		}
	}
}

class PostRequest extends FormRequest
{
	protected function prepareForValidation()
	{
		$this->merge([
			'balance' => $this->balance <> '' ? fixed_amount($this->balance) : '',
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
	public function rules()
	{
		if (request()->segment(4) == null) {
			return [
				'full_name'    => 'required|max:30|regex:/^[\pL\s\-]+$/u|unique:users,full_name',
				'phone_number' => 'required|numeric|phone:ID,mobile|unique:users,phone_number',
				'email'        => 'required|email|unique:users,email',
				'username'     => 'required|alpha_num|min:5|max:20|unique:users,username',
				'password'     => 'required|alpha_num|min:5|max:20',
				'balance'      => 'required|numeric|integer|min:0',
				//'level'        => 'required|in:Agen,Reseller',
			];
		}
		return [
			'full_name'    => 'required|max:30|regex:/^[\pL\s\-]+$/u',
			'phone_number' => 'required|numeric|phone:ID,mobile',
			'email'        => 'required|email',
			'username'     => 'required|alpha_num|min:5|max:20',
			'balance'      => 'required|numeric|integer|min:0',
			/*'api_key'      => 'required|alpha_num|max:60',
			'level'        => 'required|in:Agen,Reseller',*/
		];
	}
	public function attributes()
	{
		return [
			'full_name'    => 'Nama Lengkap',
			'phone_number' => 'Nomor Telepon',
			'email'        => 'Email',
			'username'     => 'Username',
			'password'     => 'Password',
			'balance'      => 'Saldo',
			/*'api_key'      => 'Kunci API',
			'level'        => 'Level',*/
		];
	}
}
