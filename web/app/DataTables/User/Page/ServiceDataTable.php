<?php

namespace App\DataTables\User\Page;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Admin\Log\Register;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ServiceDataTable extends DataTable {
    public function dataTable($query) {
        $query = Service::with('category')->select('services.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_category') AND request('filter_category') <> null) {
                    $query->whereHas('category', function($query){
                        $query->where('name', 'like', "%".escape_input(request('filter_category'))."%");
                    });
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('services.status', 'like', "%".escape_input(request('filter_status'))."%");
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('category', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('services.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('services.name', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('services.price', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('services.min', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('services.max', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('category_id', function ($query) {
                if ($query->category == true) {
                    return $query->category->name;
                }
                return null;
            })
            ->editColumn('id', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Layanan', '".url('page/service/detail/'.$query->id.'')."')\" class=\"badge badge-info badge-sm\" data-toggle=\"tooltip\" title=\"Detail\">#$query->id</a>";
            })
            ->editColumn('name', function ($query) {
                // return Str::limit($query->name, 30, '...');
                return $query->name;
            })
            ->editColumn('price', function ($query) {
                return 'Rp '.number_format($query->price,0,',','.');
            })
            ->editColumn('min', function ($query) {
                return number_format($query->min,0,',','.');
            })
            ->editColumn('max', function ($query) {
                return number_format($query->max,0,',','.');
            })
            ->addColumn('action', function ($query) {
                return "
                <a href='".url('admin/service/form/'.$query->id.'')."' class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\" ></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->name', '".url('admin/service/delete/'.$query->id.'')."', '<br /><small>jika Anda menghapus <b>$query->name</b>, maka <b>Harga Khusus</b> yang berhubungan dengan <b>$query->name</b> juga akan ikut terhapus.')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->addColumn('status', function ($query) {
                if ($query->status == '1') {
                    $labels = '<a href="'.url('admin/service/status/'.$query->id.'/0').'" class="badge badge-success badge-sm">AKTIF</a>';
                } elseif ($query->status == '0') {
                    $labels = '<a href="'.url('admin/service/status/'.$query->id.'/1').'" class="badge badge-danger badge-sm">NONAKTIF</a>';
                } else {
                    $labels = '<span class="badge badge-info badge-sm">ERROR</span>';
                }
                return $labels;
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['id', 'action', 'status']);
    }

    public function query(Service $model) {
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
                            d.filter_category = $("#filter_category option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'category_id', 'name' => 'category.name', 'title' => 'KATEGORI'],
            ['data' => 'name', 'title' => 'NAMA'],
            ['data' => 'price', 'title' => 'HARGA/K'],
            ['data' => 'min', 'title' => 'MIN.'],
            ['data' => 'max', 'title' => 'MAKS.'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
