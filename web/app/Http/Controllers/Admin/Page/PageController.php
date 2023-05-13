<?php

namespace App\Http\Controllers\Admin\Page;

use App\DataTables\Admin\Page\NotificationDataTable;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller {
    public function hof(Request $request) {
        $components = [
            'start_date' => date('Y-m-01'),
            'end_date'   => date('Y-m-t')
        ];
        $components['breadcrumb'] = (object) [
            'first'  => 'Top Terbaik',
            'second' => website_config('main')->website_name
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
        $components['orders'] = User::whereBetween('orders.created_at', [$components['start_date'], $components['end_date']])
                                ->join('orders', 'users.id', '=', 'orders.user_id')
                                ->latest('amount')
                                ->whereIn('orders.status', ['Pending', 'Processing', 'Success'])
                                ->groupBy('users.username', 'users.full_name', 'users.id')
                                ->limit(10)
                                ->get(['users.username', 'users.full_name', 'users.id', User::raw('SUM(orders.price) AS amount'), User::raw('COUNT(orders.id) AS total')]);
        $components['deposits'] = User::whereBetween('deposits.created_at', [$components['start_date'], $components['end_date']])
                                ->join('deposits', 'users.id', '=', 'deposits.user_id')
                                ->latest('amount')
                                ->whereIn('deposits.status', ['Success'])
                                ->groupBy('users.username', 'users.full_name', 'users.id')
                                ->limit(10)
                                ->get(['users.username', 'users.full_name', 'users.id', User::raw('SUM(deposits.amount) AS amount'), User::raw('COUNT(deposits.id) AS total')]);
        $components['services'] = Order::whereBetween('orders.created_at', [$components['start_date'], $components['end_date']])
                                ->join('services', 'orders.service_id', '=', 'services.id')
                                ->latest('amount')
                                ->whereIn('orders.status', ['Pending', 'Processing', 'Success'])
                                ->groupBy('services.name', 'services.id')
                                ->limit('10')
                                ->get(['services.name', 'services.id', User::raw('SUM(orders.price) AS amount'), User::raw('COUNT(orders.id) AS total')]);
        return view('admin.page.hof', $components);
    }
}
