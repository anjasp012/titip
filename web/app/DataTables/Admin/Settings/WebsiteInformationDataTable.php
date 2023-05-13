<?php

namespace App\DataTables\Admin\Settings;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\WebsiteInformation;
use Yajra\DataTables\Services\DataTable;

class WebsiteInformationDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at')) {
                    $query->where('created_at', 'like', "%" . escape_input(request('filter_created_at')) . "%");
                }
                if (request()->has('filter_category')) {
                    $query->where('category', 'like', "%" . escape_input(request('filter_category')) . "%");
                }
                if (request()->has('search')) {
                    $query->where(function ($query) {
                        $query
                            ->where('id', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('category', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('content', 'like', "%" . escape_input(request('search')) . "%");
                    });
                }
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Informasi', '" . url('admin/settings/website_information/form/' . $query->id . '') . "')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->id', '" . url('admin/settings/website_information/delete/' . $query->id . '') . "')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->editColumn('content', function ($query) {
                return Str::limit($query->content, 50, '...');
            })
            ->editColumn('is_popup', function ($query) {
                if ($query->is_popup == '1') {
                    return '<i class="fa fa-check text-success"></i>';
                } else {
                    return '<i class="fa fa-times text-danger"></i>';
                }
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['action', 'is_popup']);
    }

    public function query(WebsiteInformation $model)
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
                            d.filter_category = $("#filter_category option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
            ])
            ->orderBy(0);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'created_at', 'name' => 'orders.created_at', 'title' => 'DIBUAT'],
            ['data' => 'category', 'title' => 'KATEGORI'],
            ['data' => 'title', 'title' => 'TITLE'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'max-width' => '100'],
        ];
    }

    protected function filename()
    {
        return 'Lead_' . date('YmdHis');
    }
}
