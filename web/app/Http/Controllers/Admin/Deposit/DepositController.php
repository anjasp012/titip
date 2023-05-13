<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Models\User;
use App\Models\Deposit;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\UserBalanceLog;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\DataTables\Admin\Deposit\DepositDataTable;
use Exception;
use Illuminate\Support\Facades\Mail;

class DepositController extends Controller
{
    public function list(DepositDataTable $dataTable)
    {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar Deposit',
            'second' => 'Deposit'
        ];
        $components['users'] = Deposit::distinct()->latest('user_id')->get(['user_id']);
        $components['methods'] = Deposit::get(['deposit_method_id', 'deposit_method_name'])->unique('deposit_method_id')->unique('deposit_method_name');
        $components['statuses'] = ['Pending', 'Success', 'Canceled'];
        $components['created_at'] = Deposit::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.deposit.deposit.list', $components);
    }
    public function delete(Deposit $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        //if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if ($target->delete()) return json_encode(['result' => true], JSON_PRETTY_PRINT);
    }
    public function detail(Deposit $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        return view('admin.deposit.deposit.detail', compact('target'));
    }
    public function report(Request $request)
    {
        $components = [
            'start_date' => date('Y-m-01'),
            'end_date'   => date('Y-m-t')
        ];
        $components['breadcrumb'] = (object) [
            'first'  => 'Laporan',
            'second' => 'Deposit'
        ];
        if ($request->start_date == true or $request->end_date == true) {
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
        $components['deposits'] = [
            'all'      => Deposit::whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Deposit::raw('SUM(deposits.amount) AS amount'), Deposit::raw('COUNT(deposits.id) AS total'))->first(),
            'pending'  => Deposit::where('status', 'Pending')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Deposit::raw('SUM(deposits.amount) AS amount'), Deposit::raw('COUNT(deposits.id) AS total'))->first(),
            'canceled' => Deposit::where('status', 'Canceled')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Deposit::raw('SUM(deposits.amount) AS amount'), Deposit::raw('COUNT(deposits.id) AS total'))->first(),
            'success'  => Deposit::where('status', 'Success')->whereBetween('created_at', [$components['start_date'], $components['end_date']])->select(Deposit::raw('SUM(deposits.amount) AS amount'), Deposit::raw('COUNT(deposits.id) AS total'))->first(),
        ];
        return view('admin.deposit.deposit.report', $components);
    }
    public function confirm(Deposit $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        //if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if ($target->status == 'Success') return response()->json([
            'result'  => false,
            'message' => 'Deposit berstatus Success.'
        ], 200, [], JSON_PRETTY_PRINT);
        $update_data = $target->update([
            'status' => 'Success',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $user = User::find($target->user_id);
        $add_balance = $user->update([
            'balance' => $user->balance + $target->balance,
        ]);
        $balance_logs = UserBalanceLog::create([
            'user_id'     => $target->user_id,
            'type'        => 'Plus',
            'action'      => 'Deposit',
            'amount'      => $target->balance,
            'description' => 'Membuat Deposit #' . $target->id . '.',
        ]);
        if ($target->user == true and json_decode($target->user->notification)->deposit == '1') {
            $details = [
                'name'       => $target->user->full_name,
                'id'         => $target->id,
                'method'     => $target->deposit_method ? $target->deposit_method->name . ' (' . $target->deposit_method->payment . ' - ' . $target->deposit_method->type . ')' : null,
                'amount'     => $target->amount,
                'status'     => 'Success',
                'ip_address' => $request->ip(),
            ];
            $this->send_email($details, $target->user->email);
        }
        return response()->json([
            'status'  => true,
            'message' =>
            '
                <br /><b>Pengguna:</b> ' . $user->username . ' (' . $user->full_name . ')
                <br /><b>Jumlah:</b> Rp ' . number_format($target->amount, 0, ',', '.') . '
                <br /><b>Saldo didapat:</b> Rp ' . number_format($target->balance, 0, ',', '.') . '
            '
        ]);
        return response()->json([
            'result'  => true,
        ], 200, [], JSON_PRETTY_PRINT);;
    }
    public function cancel(Deposit $target, Request $request)
    {
        if ($request->ajax() == false) abort('404');
        //if (Auth::guard('admin')->user()->level == 'Admin') return (json_encode(['result' => false], JSON_PRETTY_PRINT));
        if ($target->status == 'Canceled') return response()->json([
            'result'  => false,
            'message' => 'Deposit berstatus Canceled.'
        ], 200, [], JSON_PRETTY_PRINT);
        $update_data = $target->update([
            'status' => 'Canceled',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if ($target->user == true and json_decode($target->user->notification)->deposit == '1') {
            $details = [
                'name'       => $target->user->full_name,
                'id'         => $target->id,
                'method'     => $target->deposit_method ? $target->deposit_method->name . ' (' . $target->deposit_method->payment . ' - ' . $target->deposit_method->type . ')' : null,
                'amount'     => $target->amount,
                'status'     => 'Canceled',
                'ip_address' => $request->ip(),
            ];
            $this->send_email($details, $target->user->email);
        }
        return response()->json([
            'result'  => true,
        ], 200, [], JSON_PRETTY_PRINT);;
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
