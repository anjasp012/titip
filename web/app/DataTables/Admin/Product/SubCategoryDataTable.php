<?php

namespace App\DataTables\Admin\Product;

use App\Models\Category;
use App\Models\Provider;
use App\Models\ProductSubCategory;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SubCategoryDataTable extends DataTable {
    public function dataTable($query) {
        $query = ProductSubCategory::with('category')->select('product_sub_categories.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_category') AND request('filter_category') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('category', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('filter_category'))."%");
                        });
                    });
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('status', 'like', "%".escape_input(request('status'))."%");
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('category', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('name', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('slug', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Kategori', '".url('admin/product/sub_category/form/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->name', '".url('admin/product/sub_category/delete/'.$query->id.'')."', '<br /><small>jika Anda menghapus <b>$query->name</b>, maka <b>Produk</b> dari <b>$query->name</b> juga akan ikut terhapus.')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->addColumn('status', function ($query) {
                if ($query->status == '1') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"0\" onclick=\"switchStatus(this, $query->id, '".url('admin/product/sub_category/status/'.$query->id.'/0')."')\" checked>
                    <label class=\"custom-control-label\" for=\"switch-status-$query->id\">Aktif</label></div>";
                } elseif ($query->status == '0') {
                    $labels = "<div class=\"custom-control custom-switch\">
                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"switch-status-$query->id\" value=\"1\" onclick=\"switchStatus(this, $query->id, '".url('admin/product/sub_category/status/'.$query->id.'/1')."')\">
                    <label class=\"custom-control-label\" for=\"switch-status-$query->id\">Nonaktif</label></div>";
                } else {
                    $labels = '<span class="badge badge-info badge-sm">ERROR</span>';
                }
                return $labels;
            })
            ->editColumn('category_id', function ($query) {
                if ($query->category == true) return $query->category->name;
                return null;
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['action', 'status', 'icon']);
    }

    public function query(ProductSubCategory $model) {
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
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'category_id', 'title' => 'KATEGORI'],
            ['data' => 'name', 'title' => 'NAMA'],
            ['data' => 'slug', 'title' => 'SLUG'],
            ['data' => 'status', 'title' => 'STATUS', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '100'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
