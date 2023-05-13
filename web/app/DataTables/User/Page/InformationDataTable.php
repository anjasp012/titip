<?php

namespace App\DataTables\User\Page;

use App\Models\Ticket;
use App\Models\RegisterLog;
use Illuminate\Support\Str;
use App\Models\UserLoginLog;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Admin\Log\Register;
use App\Models\Information;
use App\Models\WebsiteInformation;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InformationDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') and request('filter_created_at') <> null) {
                    $query->where('created_at', 'like', "%" . escape_input(request('filter_created_at')) . "%");
                }
                if (request()->has('filter_category') and request('filter_category') <> null) {
                    $query->where('category', 'like', "%" . escape_input(request('filter_category')) . "%");
                }
                if (request()->has('search') and request('search') <> null) {
                    $query->where(function ($query) {
                        $query
                            ->where('id', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('category', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('content', 'like', "%" . escape_input(request('search')) . "%");
                    });
                }
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->editColumn('category', function ($query) {
                return category($query->category);
            })
            ->editColumn('title', function ($query) {
                return '<a href="' . url('page/information/' . $query->id) . '">' . $query->title . '</a>';
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['category', 'title']);
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
            ['data' => 'created_at', 'title' => 'DIBUAT', 'width' => '180'],
            ['data' => 'title', 'title' => 'TITLE'],
        ];
    }

    protected function filename()
    {
        return 'Lead_' . date('YmdHis');
    }
}
