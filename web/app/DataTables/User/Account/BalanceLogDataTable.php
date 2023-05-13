<?php

namespace App\DataTables\User\Account;

use App\Models\Ticket;
use App\Models\UserBalanceLog;
use App\Models\RegisterLog;
use Illuminate\Support\Str;
use App\Models\UserLoginLog;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Admin\Log\Register;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BalanceLogDataTable extends DataTable
{
    public function dataTable($query)
    {
        $query = UserBalanceLog::where('user_id', Auth::user()->id);
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('created_at')) {
                    $query->where('created_at', 'like', "%" . escape_input(request('created_at')) . "%");
                }
                if (request()->has('type')) {
                    $query->where('type', 'like', "%" . escape_input(request('type')) . "%");
                }
                if (request()->has('action')) {
                    $query->where('action', 'like', "%" . escape_input(request('action')) . "%");
                }
                if (request()->has('search')) {
                    $query->where(function ($query) {
                        $query
                            ->where('id', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('type', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('action', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('amount', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('description', 'like', "%" . escape_input(request('search')) . "%");
                    });
                }
            })
            ->editColumn('description', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->description')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">" . Str::limit($query->description, 30, '...') . "</a>";
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d/m/y H:i');
            })
            ->editColumn('amount', function ($query) {
                if ($query->action == 'Bonus') return number_format($query->amount, 0, ',', '.') . ' Poin';
                return 'Rp ' . number_format($query->amount, 0, ',', '.');
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
            ->rawColumns(['description']);
    }

    public function query(UserBalanceLog $model)
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
                            d.created_at = $("#created_at option:selected").val();
                            d.type = $("#type option:selected").val();
                            d.action = $("#action option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
            ])
            ->orderBy(0);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'created_at', 'title' => 'DIBUAT', 'width' => '180'],
            ['data' => 'type', 'title' => 'TIPE'],
            ['data' => 'action', 'title' => 'AKSI'],
            ['data' => 'amount', 'title' => 'JUMLAH'],
            ['data' => 'description', 'title' => 'DESKRIPSI'],
        ];
    }

    protected function filename()
    {
        return 'Lead_' . date('YmdHis');
    }
}
