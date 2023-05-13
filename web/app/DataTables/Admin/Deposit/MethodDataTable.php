<?php

namespace App\DataTables\Admin\Deposit;

use App\Models\DepositMethod;
use Yajra\DataTables\Services\DataTable;

class MethodDataTable extends DataTable {
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_payment') AND request('filter_payment') <> null) {
                    $query->where('payment', 'like', "%".escape_input(request('filter_payment'))."%");
                }
                if (request()->has('filter_type') AND request('filter_type') <> null) {
                    $query->where('type', 'like', "%".escape_input(request('filter_type'))."%");
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('status', 'like', "%".escape_input(request('filter_status'))."%");
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->where('id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('payment', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('type', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('name', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('rate', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('min', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Metode', '".url('admin/deposit/method/form/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->name', '".url('admin/deposit/method/delete/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->editColumn('min', function ($query) {
                return 'Rp '.number_format($query->min,0,',','.');
            })
            ->addColumn('status', function ($query) {
                if ($query->status == '1') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"0\" onclick=\"switchStatus(this, $query->id, '".url('admin/deposit/method/status/'.$query->id.'/0')."')\" checked>
                    <label class=\"custom-control-label\" for=\"switch-status-$query->id\">Aktif</label></div>";
                } elseif ($query->status == '0') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"1\" onclick=\"switchStatus(this, $query->id, '".url('admin/deposit/method/status/'.$query->id.'/1')."')\">
                    <label class=\"custom-control-label\" for=\"switch-status-$query->id\">Nonaktif</label></div>";
                } else {
                    $labels = '<span class="badge badge-info badge-sm">ERROR</span>';
                }
                return $labels;
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['action', 'status']);
    }

    public function query(DepositMethod $model) {
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
                            d.filter_payment = $("#filter_payment option:selected").val();
                            d.filter_type = $("#filter_type option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID', 'max-width' => '50'],
            ['data' => 'payment', 'title' => 'PEMBAYARAN', 'width' => '60'],
            ['data' => 'type', 'title' => 'TIPE', 'width' => '60'],
            ['data' => 'name', 'title' => 'NAMA'],
            ['data' => 'rate', 'title' => 'RATE'],
            ['data' => 'min', 'title' => 'MIN.'],
            ['data' => 'status', 'title' => 'STATUS', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'max-width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'max-width' => '100'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
