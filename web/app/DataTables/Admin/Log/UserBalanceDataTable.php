<?php

namespace App\DataTables\Admin\Log;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\UserBalanceLog;
use App\Models\UserLoginLog;
use Yajra\DataTables\Services\DataTable;

class UserBalanceDataTable extends DataTable {
    public function dataTable($query) {
        $query = UserBalanceLog::with('user')->select('user_balance_logs.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('user_balance_logs.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_user') AND request('filter_user') <> null) {
                    $query->whereHas('user', function($query){
                        $query->where('username', 'like', "%".escape_input(request('filter_user'))."%");
                    });
                }
                if (request()->has('filter_type') AND request('filter_type') <> null) {
                    $query->where('user_balance_logs.type', 'like', "%".escape_input(request('filter_type'))."%");
                }
                if (request()->has('filter_action') AND request('filter_action') <> null) {
                    $query->where('user_balance_logs.action', 'like', "%".escape_input(request('filter_action'))."%");
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('user', function($query) {
                            $query->where('username', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('user_balance_logs.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('user_balance_logs.type', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('user_balance_logs.action', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('user_balance_logs.amount', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('user_balance_logs.description', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('user_id', function ($query) {
                if ($query->user == true) {
                    return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Pengguna', '".url('admin/user/detail/'.$query->user_id.'')."')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->user->username, 10, '...')."</a>";
                }
                return null;
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->editColumn('amount', function ($query) {
                if ($query->action == 'Bonus') return number_format($query->amount,0,',','.').' Poin';
                return 'Rp '.number_format($query->amount,0,',','.');
            })
            ->editColumn('description', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->description')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->description, 30, '...')."</a>";
            })
            ->setRowClass(function ($query) {
                if ($query->type == 'Plus') {
                    return 'table-success';
                } elseif ($query->type == 'Minus') {
                    return 'table-danger';
                } else {
                    return 'table-secondary';
                }
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['user_id', 'description']);
    }

    public function query(UserBalanceLog $model) {
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
                            d.filter_user = $("#filter_user option:selected").val();
                            d.filter_type = $("#filter_type option:selected").val();
                            d.filter_action = $("#filter_action option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'created_at', 'name' => 'user_balance_logs.created_at', 'title' => 'DIBUAT', 'width' => '180'],
            ['data' => 'user_id', 'name' => 'user.username', 'title' => 'PENGGUNA', 'width' => '120'],
            ['data' => 'type', 'name' => 'user_balance_logs.type', 'title' => 'TIPE'],
            ['data' => 'action', 'name' => 'user_balance_logs.action', 'title' => 'AKSI'],
            ['data' => 'amount', 'name' => 'user_balance_logs.amount', 'title' => 'JUMLAH'],
            ['data' => 'description', 'name' => 'user_balance_logs.description', 'title' => 'DESKRIPSI'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
