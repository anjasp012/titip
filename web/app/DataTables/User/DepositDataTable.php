<?php

namespace App\DataTables\User;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Admin\Log\Register;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DepositDataTable extends DataTable
{
    public function dataTable($query)
    {
        $query = Deposit::where('user_id', Auth::user()->id)->with('deposit_method')->select('deposits.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') and request('filter_created_at') <> null) {
                    $query->where('deposits.created_at', 'like', "%" . escape_input(request('filter_created_at')) . "%");
                }
                if (request()->has('filter_method') and request('filter_method') <> null) {
                    $query->where(function ($query) {
                        $query
                            ->whereHas('deposit_method', function ($query) {
                                $query->where('name', 'like', "%" . escape_input(request('filter_method')) . "%");
                            })
                            ->orWhere('deposits.deposit_method_name', 'like', "%" . escape_input(request('filter_method')) . "%");
                    });
                }
                if (request()->has('filter_status') and request('filter_status') <> null) {
                    $query->where('deposits.status', 'like', "%" . escape_input(request('filter_status')) . "%");
                }
                if (request()->has('search') and request('search') <> null) {
                    $query->where(function ($query) {
                        $query
                            ->whereHas('deposit_method', function ($query) {
                                $query->where('name', 'like', "%" . escape_input(request('search')) . "%");
                            })
                            ->orWhere('deposits.id', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('deposits.deposit_method_name', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('deposits.amount', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('deposits.balance', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('deposits.status', 'like', "%" . escape_input(request('search')) . "%");
                    });
                }
            })
            ->editColumn('deposit_method_id', function ($query) {
                if ($query->deposit_method == true) {
                    return $query->deposit_method->name;
                }
                return $query->deposit_method_name;
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d/m/y H:i');
            })
            ->editColumn('amount', function ($query) {
                return number_format($query->amount, 0, ',', '.');
            })
            ->editColumn('balance', function ($query) {
                return number_format($query->balance, 0, ',', '.');
            })
            ->editColumn('id', function ($query) {
                return "<a href=\"/deposit/detail/$query->id\"  class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">#$query->id</a>";
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
            ->rawColumns(['id', 'deposit_method_id', 'status']);
    }

    public function query(Deposit $model)
    {
        return $model->newQuery();
    }

    public function html()
    {
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
                        "previous" => "<i class='ri-arrow-left-s-line'>",
                        "next" => "<i class='ri-arrow-right-s-line'>",
                        "last" =>    "Terakhir"
                    ],
                ]
            ])
            ->dom('<bottom><"float-left"><"float-right">r<"row"<"col-sm-4"i><"col-sm-4"><"col-sm-4"p>>')
            ->ajax([
                'url' => url()->current(),
                'data' => 'function(d) { 
                            d.filter_created_at = $("#filter_created_at option:selected").val();
                            d.filter_method = $("#filter_method option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
            ])
            ->orderBy(0);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'created_at', 'name' => 'deposits.created_at', 'title' => 'DIBUAT', 'width' => '150'],
            ['data' => 'deposit_method_id', 'name' => 'deposit_method.name', 'title' => 'METODE'],
            ['data' => 'amount', 'name' => 'deposits.amount', 'title' => 'JUMLAH'],
            ['data' => 'balance', 'name' => 'deposits.balance', 'title' => 'SALDO DIDAPAT'],
            ['data' => 'status', 'name' => 'deposits.status', 'title' => 'STATUS'],
        ];
    }

    protected function filename()
    {
        return 'Lead_' . date('YmdHis');
    }
}
