<?php

namespace App\DataTables\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable {
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at')) {
                    $query->where('created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_level')) {
                    $query->where('level', 'like', "%".escape_input(request('filter_level'))."%");
                }
                if (request()->has('filter_verification')) {
                    $query->where('is_verified', 'like', "%".escape_input(request('filter_verification'))."%");
                }
                if (request()->has('filter_status')) {
                    $query->where('status', 'like', "%".escape_input(request('filter_status'))."%");
                }
                if (request()->has('search')) {
                    $query->where(function($query) {
                        $query
                        ->where('id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('username', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('full_name', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('email', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('phone_number', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('balance', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('level', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('api_key', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('upline', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('id', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Pengguna', '".url('admin/user/detail/'.$query->id.'')."')\" class=\"badge badge-info badge-sm\" data-toggle=\"tooltip\" title=\"Detail\">#$query->id</a>";
            })
            ->editColumn('username', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->username')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->username, 20, '...')."</a>";
            })
            ->editColumn('full_name', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->full_name')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->full_name, 20, '...')."</a>";
            })
            ->editColumn('email', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->email')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->email, 20, '...')."</a>";
            })
            ->editColumn('phone_number', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->phone_number')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->phone_number, 20, '...')."</a>";
            })
            ->editColumn('balance', function ($query) {
                return 'Rp '.number_format($query->balance,0,',','.');
            })
            ->editColumn('is_verified', function ($query) {
                if ($query->is_verified == '0') return '<i class="fa fa-times text-danger"></i> Belum';
                if ($query->is_verified == '1') return '<i class="fa fa-check text-success"></i> Sudah';
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y');
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Pengguna', '".url('admin/user/form/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->full_name', '".url('admin/user/delete/'.$query->id.'')."', '<br /><small>jika Anda menghapus <b>$query->full_name</b>, maka <b>Pesanan</b>, <b>Bonus Pesanan</b>, <b>Deposit</b>, <b>Penukaran Poin</b>, <b>Riwayat Masuk</b>, <b>Mutasi Saldo</b>, dan <b>Tiket</b> dari <b>$query->full_name</b> juga akan ikut terhapus.')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->addColumn('status', function ($query) {
                if ($query->status == '1') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"0\" onclick=\"switchStatus(this, $query->id, '".url('admin/user/status/'.$query->id.'/0')."')\" checked>
                    <label class=\"custom-control-label\" for=\"switch-status-$query->id\">Aktif</label></div>";
                } elseif ($query->status == '0') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"1\" onclick=\"switchStatus(this, $query->id, '".url('admin/user/status/'.$query->id.'/1')."')\">
                    <label class=\"custom-control-label\" for=\"switch-status-$query->id\">Nonaktif</label></div>";
                } else {
                    $labels = '<span class="badge badge-info badge-sm">ERROR</span>';
                }
                return $labels;
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['id', 'username', 'full_name', 'email', 'phone_number', 'balance', 'is_verified', 'action', 'status']);
    }

    public function query(User $model) {
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
                            d.filter_level = $("#filter_level option:selected").val();
                            d.filter_verification = $("#filter_verification option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID', 'max-width' => '50'],
            ['data' => 'created_at', 'title' => 'BERGABUNG'],
            ['data' => 'username', 'title' => 'USERNAME'],
            ['data' => 'full_name', 'title' => 'NAMA LENGKAP'],
            ['data' => 'balance', 'title' => 'SALDO'],
            ['data' => 'level', 'title' => 'AKSES'],
            ['data' => 'is_verified', 'title' => 'VERIFIKASI'],
            ['data' => 'status', 'title' => 'STATUS', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'max-width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'max-width' => '100'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
