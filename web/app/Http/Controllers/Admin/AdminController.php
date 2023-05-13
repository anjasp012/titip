<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\AdminLoginLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\DataTables\Admin\AdminDataTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminController extends Controller {
    public function list(AdminDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Admin',
			'second' => website_config('main')->website_name
		];
        $components['levels'] = ['Developer', 'Admin'];
        $components['statuses'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        return $dataTable->render('admin.admin.list', $components);
    }
    public function getForm(Admin $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        if ($target == true) $components['target'] = $target;
        $components['levels'] = ['Admin', 'Developer'];
        return view('admin.admin.form', $components);
    }
    public function postForm(Admin $target, PostRequest $request) {
		if ($request->ajax() == false) abort('404');
        /*if (Auth::guard('admin')->user()->level == 'Admin') {
			return response()->json([
				'status'  => false, 
				'type'    => 'alert',
                'message' => 'Aksi tidak diperbolehkan.'
			]);
        }*/
		$input_data = [
            'full_name' => escape_input($request->full_name),
            'username'  => escape_input($request->username),
            'level'     => escape_input($request->level),
        ];
        if (request()->segment(4) == null) {
            $input_data['password'] = escape_input(Hash::make($request->password));
        }
		if ($target->id <> null) {
            if ($request->password <> '') {
                $input_data['password'] = escape_input(Hash::make($request->password));
            }
            $check_data = Admin::where([['full_name', $input_data['full_name']]])->first();
            if ($input_data['full_name'] <> $target['full_name'] AND $check_data) {
                $validator = Validator::make($request->all(), [
                    'full_name' => 'required|unique:admins,full_name|max:30',
                ], [], ['full_name' => 'Nama Lengkap']);
                if ($validator->fails()) {
                    return response()->json([
                        'status'  => false, 
                        'type'    => 'validation',
                        'message' => $validator->errors()->toArray()
                    ]);
                }
            }
            $check_data = Admin::where([['username', $input_data['username']]])->first();
            if ($input_data['username'] <> $target['username'] AND $check_data) {
                $validator = Validator::make($request->all(), [
                    'username' => 'required|unique:admins,username|max:20',
                ], [], ['username' => 'Username']);
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
                'message' => 'Admin berhasil diperbarui.'
            ]);
		} else {
            $insert_data = Admin::create($input_data);
            return response()->json([
                'status'  => true, 
                'message' => 'Admin berhasil ditambahkan.'
            ]);
        }
	}
	public function status(Admin $target, $status, Request $request) {
		if ($request->ajax() == false) abort('404');
        if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if (Arr::exists(['0', '1'], $status) == false) return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        $update_data = $target->update(['status' => $status]);
        if ($update_data == true) {
            return (json_encode([
                'result'  => true,
                'message' => 'Status Admin <b>'.$target->full_name.'</b> berhasil diperbarui.'
            ], JSON_PRETTY_PRINT));
        } else {
            return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        }
	}
	public function delete(Admin $target, Request $request) {
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
		if (request()->segment(4) == null) {
			return [
				'full_name' => 'required|min:5|max:30|regex:/^[\pL\s\-]+$/u|unique:admins,full_name',
                'username'  => 'required|alpha_num|min:5|max:20|unique:admins,username',
                'password'  => 'required|alpha_num|min:5|max:20',
                'level'     => 'required|in:Admin,Developer',
			];
		}
		return [
            'full_name' => 'required|max:30|regex:/^[\pL\s\-]+$/u',
            'username'  => 'required|alpha_num|min:5|max:20',
            'level'     => 'required|in:Admin,Developer',
		];
    }
    public function attributes() {
		return [
            'full_name' => 'Nama Lengkap',
            'username'  => 'Username',
            'password'  => 'Password',
            'level'     => 'Akses',
		];
    }
}
