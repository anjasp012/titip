<?php

namespace App\DataTables\User\Page;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Information;
use App\Models\RegisterLog;
use Illuminate\Support\Str;
use App\Models\UserLoginLog;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Admin\Log\Register;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MonitoringServiceDataTable extends DataTable {
    public function dataTable($query) {
        DB::statement(DB::raw('set @rownum=0'));
        $query = Order::with('service')->where('orders.status', 'Success')->whereDate('created_at', date('Y-m-d'))->select(DB::raw('@rownum := 0 r'))
        ->select(DB::raw('@rownum := @rownum + 1 AS rank'), 'orders.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('orders.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_updated_at') AND request('filter_updated_at') <> null) {
                    $query->where('orders.updated_at', 'like', "%".escape_input(request('filter_updated_at'))."%");
                }
                if (request()->has('filter_service') AND request('filter_service') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('service', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('filter_service'))."%");
                        })
                        ->orWhere('orders.service_name', 'like', "%".escape_input(request('filter_service'))."%");
                    });
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('service', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('orders.service_name', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('orders.quantity', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('quantity', function ($query) {
                return number_format($query->quantity,0,',','.');
            })
            ->editColumn('service_id', function ($query) {
                if ($query->service == true) {
                    return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Layanan', '".url('page/service/detail/'.$query->service_id.'')."')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->service->name, 30, '...')."</a>";
                }
                return $query->service_name;
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('H:i:s \W\I\B');
            })
            ->editColumn('updated_at', function ($query) {
                return Carbon::parse($query->updated_at)->translatedFormat('H:i:s \W\I\B');
            })
            ->addColumn('processing_time', function ($query) {
                return Carbon::parse(Carbon::parse($query->updated_at)->diffInSeconds(Carbon::parse($query->created_at)))->format('H \J\a\m\, i \M\e\n\i\t\, s \D\e\t\i\k');
            })
            ->setRowClass(function ($query) {
                return 'table-success';
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['category', 'content', 'service_id']);
    }

    public function query(Order $model) {
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
                            d.filter_updated_at = $("#filter_updated_at option:selected").val();
                            d.filter_category = $("#filter_category option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ]);
    }

    protected function getColumns() {
        return [
            ['data' => 'rank', 'title' => 'ID', 'width' => '50'],
            ['data' => 'service_id', 'name' => 'service.name', 'title' => 'LAYANAN'],
            ['data' => 'quantity', 'name' => 'orders.quantity', 'title' => 'JUMLAH'],
            ['data' => 'created_at', 'name' => 'orders.created_at', 'title' => 'DIBUAT'],
            ['data' => 'updated_at', 'name' => 'orders.updated_at', 'title' => 'DIPERBARUI'],
            ['data' => 'processing_time', 'title' => 'WAKTU PROSES'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
