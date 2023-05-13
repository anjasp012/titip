<?php

namespace App\DataTables\Admin;

use App\Models\OrderBonus;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OrderBonusDataTable extends DataTable {
    public function dataTable($query) {
        $query = OrderBonus::with('user')->select('order_bonuses.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('order_bonuses.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_user') AND request('filter_user') <> null) {
                    $query->whereHas('user', function($query){
                        $query->where('username', 'like', "%".escape_input(request('filter_user'))."%");
                    });
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('order_bonuses.is_sent', '=', escape_input(request('filter_status')));
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('user', function($query) {
                            $query->where('username', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('order_bonuses.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('order_bonuses.amount', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('order_bonuses.note', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('user_id', function ($query) {
                if ($query->user == true) {
                    return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Pengguna', '".url('admin/user/detail/'.$query->user_id.'')."')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->user->username, 10, '...')."</a>";
                }
                return null;
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->editColumn('id', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Bonus Pesanan', '".url('admin/order/bonus/detail/'.$query->id.'')."')\" class=\"badge badge-info badge-sm\" data-toggle=\"tooltip\" title=\"Detail\">#$query->id</a>";
            })
            ->editColumn('target', function ($query) {
                return $query->target;
                // return "<div class=\"input-group\">
                //     <input type=\"text\" class=\"form-control\" value=\"$query->target\" id=\"data-$query->id\" readonly>
                //     <div class=\"input-group-append\">
                //         <button class=\"btn btn-dark\" type=\"button\" onclick=\"copy('data-$query->id')\"><i class=\"fa fa-copy fa-fw\"></i> Salin</button>
                //     </div>
                // </div>";
            })
            ->editColumn('amount', function ($query) {
                return number_format($query->amount,0,',','.').' Poin';
            })
            ->addColumn('status', function ($query) {
                if ($query->is_sent == '1') {
                    $labels = '<a href="javascript:void(0);" class="badge badge-success badge-sm">TERKIRIM</a>';
                } elseif ($query->is_sent == '0') {
                    $labels = '<a href="javascript:void(0);" class="badge badge-warning badge-sm">BELUM DIKIRIM</a>';
                } else {
                    $labels = '<span class="badge badge-info badge-sm">ERROR</span>';
                }
                return $labels;
            })
            ->setRowClass(function ($query) {
                if ($query->is_sent == '0') {
                    return 'table-warning';
                } elseif ($query->is_sent == '1') {
                    return 'table-success';
                } else {
                    return 'table-secondary';
                }
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['id', 'user_id', 'target', 'action', 'status']);
    }

    public function query(OrderBonus $model) {
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
                            d.filter_user = $("#filter_user option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'created_at', 'name' => 'order_bonuses.created_at', 'title' => 'DIBUAT'],
            ['data' => 'user_id', 'name' => 'user.username', 'title' => 'PENGGUNA'],
            ['data' => 'amount', 'name' => 'order_bonuses.amount', 'title' => 'JUMLAH'],
            ['data' => 'note', 'title' => 'CATATAN'],
            ['data' => 'status', 'title' => 'STATUS', 'class' => 'text-center', 'max-width' => '50'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
