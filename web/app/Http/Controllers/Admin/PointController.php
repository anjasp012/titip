<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ExchangePoint;
use App\Http\Controllers\Controller;
use App\DataTables\Admin\PointDataTable;

class PointController extends Controller {
    public function list(PointDataTable $dataTable) {
        $components['breadcrumb'] = (object) [
            'first' => 'Daftar',
            'second' => 'Tuker Poin'
        ];
        $components['users'] = ExchangePoint::distinct()->latest('user_id')->get(['user_id']);
        $components['created_at'] = ExchangePoint::selectRaw('DATE(created_at) AS created_at')->distinct()->latest('created_at')->get();
        return $dataTable->render('admin.point.list', $components);
    }
}
