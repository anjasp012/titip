<?php

namespace App\DataTables\Admin\Log;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\AdminLoginLog;
use Yajra\DataTables\Services\DataTable;

class AdminLoginDataTable extends DataTable {
    public function dataTable($query) {
        $query = AdminLoginLog::with('admin')->select('admin_login_logs.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('admin_login_logs.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_admin') AND request('filter_admin') <> null) {
                    $query->whereHas('admin', function($query){
                        $query->where('username', 'like', "%".escape_input(request('filter_admin'))."%");
                    });
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('admin', function($query) {
                            $query->where('username', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('admin_login_logs.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('admin_login_logs.ip_address', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('admin_id', function ($query) {
                if ($query->admin == true) {
                    return Str::limit($query->admin->username, 10, '...');
                }
                return null;
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['admin_id']);
    }

    public function query(AdminLoginLog $model) {
        return $model->newQuery();
    }

    public function html() {
        return $this->builder()
                    ->setTableId('data-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        "responsive" => true,
                        "autoWidth" => false,
                        "pageLength" => 30,
                        "lengthMenu" => [5, 10, 30, 50, 100],
                        "pagingType" => "full_numbers", 
                        "language" => [
                            "processing" => 'Sedang memproses...',
                            "lengthMenu" => "_MENU_",
                            "zeroRecords" => "Tidak ditemukan data yang sesuai",
                            "info" => "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                            "infoEmpty" => "Menampilkan 0 sampai 0 dari 0 entri",
                            "infoFiltered" => "(disaring dari _MAX_ entri keseluruhan)",
                            "infoPostFix" => "",
                            "search" => "Cari:",
                            "paginate" => [
                                "first" => "Pertama",
                                "previous" => "<i class='mdi mdi-chevron-left'>",
                                "next" => "<i class='mdi mdi-chevron-right'>",
                                "last" =>    "Terakhir"
                            ],
                        ]
                    ])
                    ->dom('<bottom><"float-left"><"float-right">r<"row"<"col-sm-4"i><"col-sm-4"><"col-sm-4"p>>')
                    ->ajax([
                        'url' => url()->current(),
                        'data' => 'function(d) { 
                            d.filter_created_at = $("#filter_created_at option:selected").val();
                            d.filter_admin = $("#filter_admin option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'created_at', 'name' => 'admin_login_logs.created_at', 'title' => 'DIBUAT', 'width' => '180'],
            ['data' => 'admin_id', 'name' => 'admin.username', 'title' => 'ADMIN', 'width' => '120'],
            ['data' => 'ip_address', 'name' => 'admin_login_logs.ip_address', 'title' => 'ALAMAT IP'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
