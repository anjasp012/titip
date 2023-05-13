<?php

namespace App\DataTables\User;

use App\Models\User;
use App\Models\User\Downline;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DownlineDataTable extends DataTable
{
    public function dataTable($query)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $query = User::where('upline', Auth::user()->id)->select(DB::raw('@rownum := 0 r'))
            ->select(DB::raw('@rownum := @rownum + 1 AS rank'), 'users.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') and request('filter_created_at') <> null) {
                    $query->where('created_at', 'like', "%" . escape_input(request('filter_created_at')) . "%");
                }
                if (request()->has('search') and request('search') <> null) {
                    $query->where(function ($query) {
                        $query
                            ->where('username', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('full_name', 'like', "%" . escape_input(request('search')) . "%");
                    });
                }
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['category', 'content', 'product_id']);
    }

    public function query(User $model)
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
                            d.search = $("input[name=search]").val();
                        }',
            ]);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'rank', 'title' => 'ID', 'width' => '50'],
            ['data' => 'username', 'title' => 'USERNAME'],
            ['data' => 'full_name', 'title' => 'NAMA LENGKAP'],
            ['data' => 'created_at', 'title' => 'BERGABUNG', 'width' => '180'],
        ];
    }

    protected function filename()
    {
        return 'Lead_' . date('YmdHis');
    }
}
