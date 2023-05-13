<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\OrderBonusDataTable;
use App\Http\Controllers\Controller;
use App\Models\OrderBonus;
use Illuminate\Http\Request;

class OrderBonusController extends Controller {
    public function list(OrderBonusDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
			'first' => 'Daftar Bonus',
			'second' => 'Pemesanan'
        ];
        $components['users'] = OrderBonus::distinct()->latest('user_id')->get(['user_id']);
        // $components['products'] = OrderBonus::distinct()->latest('product_id')->get(['product_id']);
        $components['statuses'] = ['0' => 'Belum Dikirim', '1' => 'Terkirim'];
        $components['created_at'] = OrderBonus::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        $components['updated_at'] = OrderBonus::selectRaw('DATE(updated_at) AS updated_at')->distinct()->latest('updated_at')->get();
        return $dataTable->render('admin.order.bonus.list', $components);
    }
	public function detail(OrderBonus $target, Request $request) {
		if ($request->ajax() == false) abort('404');
        return view('admin.order.bonus.detail', compact('target'));
    }
}
