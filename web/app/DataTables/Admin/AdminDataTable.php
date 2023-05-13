<?php

namespace App\DataTables\Admin;

use App\Models\Admin;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AdminDataTable extends DataTable {
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_level')) {
                    $query->where('level', 'like', "%".escape_input(request('filter_level'))."%");
                }
                if (request()->has('filter_status')) {
                    $query->where('status', 'like', "%".escape_input(request('filter_status'))."%");
                }
                if (request()->has('search')) {
                    $query->where(function($query) {
                        $query
                        ->where('id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('username', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('full_name', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Admin', '".url('admin/admin/form/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->full_name', '".url('admin/admin/delete/'.$query->id.'')."', '<br /><small>jika Anda menghapus <b>$query->full_name</b>, maka <b>Riwayat Masuk</b> dari <b>$query->full_name</b> juga akan ikut terhapus.')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->addColumn('status', function ($query) {
                if ($query->status == '1') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"0\" onclick=\"switchStatus(this, $query->id, '".url('admin/admin/status/'.$query->id.'/0')."')\" checked>
                    <label class=\"custom-control-label\" for=\"switch-status-$query->id\">Aktif</label></div>";
                } elseif ($query->status == '0') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"1\" onclick=\"switchStatus(this, $query->id, '".url('admin/admin/status/'.$query->id.'/1')."')\">
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

    public function query(Admin $model) {
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
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                            d.filter_level = $("#filter_level option:selected").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'username', 'title' => 'USERNAME', 'width' => '150'],
            ['data' => 'full_name', 'title' => 'NAMA LENGKAP'],
            ['data' => 'level', 'title' => 'AKSES', 'width' => '100'],
            ['data' => 'status', 'title' => 'STATUS', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '100'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
