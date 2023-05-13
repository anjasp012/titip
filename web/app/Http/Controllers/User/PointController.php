<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\BalanceLog;
use Illuminate\Http\Request;
use App\Models\ExchangePoint;
use App\Models\UserBalanceLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\User\PointDataTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PointController extends Controller {
    public function getExchange() {
        $components['breadcrumb'] = (object) [
			'first' => 'Baru',
			'second' => 'Tukar Poin'
        ];
        return view('user.point.exchange', $components);
    }
    public function postExchange(PostRequest $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::user()->username == 'demouser') {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
		$input_data = [
            'user_id'           => Auth::user()->id,
            'amount'            => fixed_amount($request->amount),
            'ip_address'        => $request->ip()
        ];
        if ($input_data['amount'] > Auth::user()->point) {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Poin Anda tidak menucukupi.'
			]);
        }
        $insert_data = ExchangePoint::create($input_data);
        if ($insert_data == true) {
            $check_user = User::find(Auth::user()->id);
            $add_balance = $check_user->update([
                'balance' => Auth::user()->balance + $input_data['amount'],
            ]);
            $cut_point = $check_user->update([
                'point' => Auth::user()->point - $input_data['amount'],
            ]);
            $balance_logs = UserBalanceLog::create([
                'user_id'     => $input_data['user_id'],
                'type'        => 'Plus',
                'action'      => 'Other',
                'amount'      => $input_data['amount'],
                'description' => 'Penukaran Poin #'.$insert_data->id.'.',
            ]);
            session()->flash('result', [
                'alert'   => 'success', 
                'title'   => 'Berhasil', 
                'message' => '
                    <br />ID: '.$insert_data->id.'
                    <br />Jumlah: Rp '.number_format($input_data['amount'],0,',','.').'
                    <br />Berhasil menukarkan Poin menjadi Saldo.
                '
            ]);
            return response()->json([
				'status'  => true, 
                'message' => 'Berhasil menukarkan Poin menjadi Saldo.'
			]);
        } else {
            return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Terjadi kesalahan.'
			]);
        }
    }
    public function history(PointDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Riwayat',
			'second' => 'Tuker Poin'
		];
        $components['created_at'] = ExchangePoint::where('user_id', Auth::user()->id)->selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('user.point.history', $components);
    }
}

class PostRequest extends FormRequest {
    protected function prepareForValidation() {
		$this->merge([
			'amount' => $this->amount <> '' ? fixed_amount($this->amount) : '',
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
    public function rules(Request $request) {
        return [
            'amount'            => 'required|numeric|integer|min:10000',
        ];
    }
    public function attributes() {
        return [
            'amount'            => 'Jumlah',
        ];
    }
}
