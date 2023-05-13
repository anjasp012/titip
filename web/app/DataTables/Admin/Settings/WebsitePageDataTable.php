<?php

namespace App\DataTables\Admin\Settings;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\WebsitePage;
use Yajra\DataTables\Services\DataTable;

class WebsitePageDataTable extends DataTable {
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at')) {
                    $query->where('created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('search')) {
                    $query->where(function($query) {
                        $query
                        ->where('id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('title', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('slug', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('content', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Halaman', '".url('admin/settings/website_page/form/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->title', '".url('admin/settings/website_page/delete/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->editColumn('content', function ($query) {
                return Str::limit($query->content, 40, '...');
            })
            ->editColumn('title', function ($query) {
                return Str::limit($query->title, 20, '...');
            })
            ->editColumn('slug', function ($query) {
                return Str::limit($query->slug, 20, '...');
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['action', 'is_popup']);
    }

    public function query(WebsitePage $model) {
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
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'created_at', 'name' => 'orders.created_at', 'title' => 'DIBUAT'],
            ['data' => 'title', 'title' => 'JUDUL'],
            ['data' => 'slug', 'title' => 'SLUG'],
            ['data' => 'content', 'title' => 'KONTEN'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '100'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
