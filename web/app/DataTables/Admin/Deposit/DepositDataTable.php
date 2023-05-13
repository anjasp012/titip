<?php

namespace App\DataTables\Admin\Deposit;

use App\Models\Deposit;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DepositDataTable extends DataTable {
    public function dataTable($query) {
        $query = Deposit::with('user', 'deposit_method')->select('deposits.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('deposits.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_user') AND request('filter_user') <> null) {
                    $query->whereHas('user', function($query){
                        $query->where('username', 'like', "%".escape_input(request('filter_user'))."%");
                    });
                }
                if (request()->has('filter_method') AND request('filter_method') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('deposit_method', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('filter_method'))."%");
                        })
                        ->orWhere('deposits.deposit_method_name', 'like', "%".escape_input(request('filter_method'))."%");
                    });
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('deposits.status', 'like', "%".escape_input(request('filter_status'))."%");
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('user', function($query) {
                            $query->where('username', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhereHas('deposit_method', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('deposits.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('deposits.amount', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('deposits.balance', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('deposits.status', 'like', "%".escape_input(request('search'))."%");
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
            ->editColumn('id', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Deposit', '".url('admin/deposit/detail/'.$query->id.'')."')\" class=\"badge badge-info badge-sm\" data-toggle=\"tooltip\" title=\"Detail\">#$query->id</a>";
            })
            ->editColumn('deposit_method_id', function ($query) {
                if ($query->deposit_method == true) {
                    return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Metode Deposit', '".url('admin/deposit/method/detail/'.$query->deposit_method_id.'')."')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->deposit_method->name, 30, '...')."</a>";
                }
                return $query->deposit_method_name;
            })
            ->editColumn('amount', function ($query) {
                return 'Rp '.number_format($query->amount,0,',','.');
            })
            ->editColumn('balance', function ($query) {
                return 'Rp '.number_format($query->balance,0,',','.');
            })
            ->addColumn('action', function ($query) {
                if ($query->status == 'Pending') {
                    return "
                    <a href=\"javascript:;\" onclick=\"confirmDeposit(this, $query->id, '$query->id', '".url('admin/deposit/confirm/'.$query->id.'')."')\" class=\"badge badge-success badge-sm\" data-toggle=\"tooltip\" title=\"Konfirmasi\"><i class=\"fa fa-check\"></i></a>
                    <a href=\"javascript:;\" onclick=\"cancelDeposit(this, $query->id, '$query->id', '".url('admin/deposit/cancel/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Konfirmasi\"><i class=\"fa fa-times\"></i></a>
                    <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->id', '".url('admin/deposit/delete/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                    ";
                } elseif ($query->status == 'Success') {
                    return "
                    <a href=\"javascript:;\" onclick=\"confirmDeposit(this, $query->id, '$query->id', '".url('admin/deposit/confirm/'.$query->id.'')."')\" class=\"badge badge-success badge-sm\" data-toggle=\"tooltip\" title=\"Konfirmasi\" style=\"pointer-events:none; cursor:default;\"><i class=\"fa fa-check\"></i></a>
                    <a href=\"javascript:;\" onclick=\"cancelDeposit(this, $query->id, '$query->id', '".url('admin/deposit/cancel/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Konfirmasi\" style=\"pointer-events:none; cursor:default;\"><i class=\"fa fa-times\"></i></a>
                    <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->id', '".url('admin/deposit/delete/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                    ";
                } else {
                    return "
                    <a href=\"javascript:;\" onclick=\"confirmDeposit(this, $query->id, '$query->id', '".url('admin/deposit/confirm/'.$query->id.'')."')\" class=\"badge badge-success badge-sm\" data-toggle=\"tooltip\" title=\"Konfirmasi\" style=\"pointer-events:none; cursor:default;\"><i class=\"fa fa-check\"></i></a>
                    <a href=\"javascript:;\" onclick=\"cancelDeposit(this, $query->id, '$query->id', '".url('admin/deposit/cancel/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Konfirmasi\" style=\"pointer-events:none; cursor:default;\"><i class=\"fa fa-times\"></i></a>
                    <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->id', '".url('admin/deposit/delete/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                    ";
                }
            })
            ->addColumn('status', function ($query) {
                return status($query->status);
            })
            ->setRowClass(function ($query) {
                if ($query->status == 'Pending') {
                    return 'table-warning';
                } elseif ($query->status == 'Success') {
                    return 'table-success';
                } elseif ($query->status == 'Canceled') {
                    return 'table-danger';
                } else {
                    return 'table-secondary';
                }
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['id', 'user_id', 'deposit_method_id', 'target', 'action', 'status']);
    }

    public function query(Deposit $model) {
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
                            d.filter_method = $("#filter_method option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'created_at', 'name' => 'deposits.created_at', 'title' => 'DIBUAT'],
            ['data' => 'user_id', 'name' => 'user.username', 'title' => 'PENGGUNA'],
            ['data' => 'deposit_method_id', 'name' => 'deposit_method.name', 'title' => 'METODE'],
            ['data' => 'amount', 'name' => 'deposits.amount', 'title' => 'JUMLAH'],
            ['data' => 'balance', 'name' => 'deposits.balance', 'title' => 'SALDO DIDAPAT'],
            ['data' => 'status', 'name' => 'deposits.status', 'title' => 'STATUS', 'class' => 'text-center', 'max-width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
