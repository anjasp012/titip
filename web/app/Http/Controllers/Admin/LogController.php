<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankMutation;
use App\Models\UserLoginLog;
use Illuminate\Http\Request;
use App\Models\AdminLoginLog;
use App\Models\UserBalanceLog;
use App\Models\UserRegisterLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Admin\Log\UserLoginDataTable;
use App\DataTables\Admin\Log\AdminLoginDataTable;
use App\DataTables\Admin\Log\UserBalanceDataTable;
use App\DataTables\Admin\Log\BankMutationDataTable;
use App\DataTables\Admin\Log\UserRegisterDataTable;

class LogController extends Controller {
    public function user_register(UserRegisterDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Pendaftaran',
			'second' => 'Log'
		];
        $components['users'] = UserRegisterLog::distinct()->latest('user_id')->get(['user_id']);
        $components['created_at'] = UserRegisterLog::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.log.user_register.list', $components);
    }
    public function user_login(UserLoginDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Pengguna Masuk',
			'second' => 'Log'
		];
        $components['users'] = UserLoginLog::distinct()->latest('user_id')->get(['user_id']);
        $components['created_at'] = UserLoginLog::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.log.user_login.list', $components);
    }
    public function admin_login(AdminLoginDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Admin Masuk',
			'second' => 'Log'
		];
        $components['admins'] = AdminLoginLog::distinct()->latest('admin_id')->get(['admin_id']);
        $components['created_at'] = AdminLoginLog::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.log.admin_login.list', $components);
    }
    public function user_balance(UserBalanceDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Saldo',
			'second' => 'Log'
		];
        $components['users'] = UserBalanceLog::distinct()->latest('user_id')->get(['user_id']);
        $components['types'] = ['Minus', 'Plus'];
        $components['actions'] = ['Order', 'Deposit', 'Refund', 'Transfer', 'Bonus', 'Other'];
        $components['created_at'] = UserBalanceLog::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.log.user_balance.list', $components);
    }
    public function bank_mutation(BankMutationDataTable $dataTable) {
		if (Auth::guard('admin')->user()->level == 'Admin') {
            return redirect()->back()->with('result', [
                'alert'   => 'danger', 
                'title'   => 'Gagal', 
                'message' => 'Aksi tidak diperbolehkan.'
            ]);
        }
        $components['breadcrumb'] = (object) [
			'first' => 'Mutasi Bank',
			'second' => 'Log'
		];
        $components['banks'] = BankMutation::distinct()->latest('bank')->get(['bank']);
        $components['created_at'] = BankMutation::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.log.bank_mutation.list', $components);
    }
}
