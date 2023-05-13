<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\DataTables\Admin\Settings\BankAccountDataTable;

class BankAccountController extends Controller {
    public function list(BankAccountDataTable $dataTable) {
		if (Auth::guard('admin')->user()->level == 'Admin') {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Akun Bank',
			'second' => 'Pengaturan'
		];
        return $dataTable->render('admin.settings.bank_account.list', $components);
    }
    public function getForm(BankAccount $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        return view('admin.settings.bank_account.form', $components);
    }
    public function postForm(BankAccount $target, PostRequest $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }
		$input_data = [
            'name'     => escape_input($request->name),
            'username' => escape_input($request->username),
            'password' => escape_input($request->password),
            'rekening' => escape_input($request->rekening),
            'token'    => escape_input($request->token),
		];
		if ($target->id <> null) {
			$check_data = BankAccount::where([['name', $input_data['name']]])->first();
			if ($input_data['name'] <> $target['name'] AND $check_data) {
				$validator = Validator::make($request->all(), [
					'name' => 'required|unique:bank_accounts,name|max:20',
				], [], ['name' => 'Nama']);
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
				'message' => 'Akun Bank berhasil diperbarui.'
			]);
		} else {
			$insert_data = BankAccount::create($input_data);
			return response()->json([
				'status'  => true, 
				'message' => 'Akun Bank berhasil ditambahkan.'
			]);
		}
	}
    public function delete(BankAccount $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
		if ($target->delete()) {
			return json_encode(['result' => true], JSON_PRETTY_PRINT);
		}
	}
}

class PostRequest extends FormRequest {
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
		if (request()->segment(5) == null) {
			return [
				'name'     => 'required|max:30|unique:bank_accounts,name',
				'username' => 'max:30',
				'password' => 'max:30',
				'rekening' => 'required|max:30',
				'token'    => 'max:2000',
			];
		}
		return [
            'name'     => 'required|max:30',
            'username' => 'max:30',
            'password' => 'max:30',
            'rekening' => 'required|max:30',
            'token'    => 'max:2000',
		];
    }
    public function attributes() {
		return [
            'name'     => 'Nama',
            'username' => 'Username',
            'password' => 'Password',
            'rekening' => 'Rekening',
            'token'    => 'Token',
		];
    }
}