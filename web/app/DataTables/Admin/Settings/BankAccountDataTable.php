<?php

namespace App\DataTables\Admin\Settings;

use App\Models\Ticket;
use App\Models\RegisterLog;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Admin\Log\Register;
use App\Models\AdminLoginLog;
use App\Models\BankAccount;
use App\Models\BankMutation;
use App\Models\UserLoginLog;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BankAccountDataTable extends DataTable {
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('search')) {
                    $query->where(function($query) {
                        $query
                        ->where('id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('name', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('username', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('password', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('rekening', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('token', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Akun Bank', '".url('admin/settings/bank_account/form/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->name', '".url('admin/settings/bank_account/delete/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->editColumn('token', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->token')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->token, 30, '...')."</a>";
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['action', 'token']);
    }

    public function query(BankAccount $model) {
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
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'name', 'title' => 'NAMA'],
            ['data' => 'username', 'title' => 'USERNAME'],
            ['data' => 'password', 'title' => 'PASSWORD'],
            ['data' => 'rekening', 'title' => 'REKENING'],
            ['data' => 'token', 'title' => 'TOKEN'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'max-width' => '100'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
