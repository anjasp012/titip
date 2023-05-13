<?php

namespace App\DataTables\User;

use App\Models\Ticket;
use App\Models\Deposit;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TicketDataTable extends DataTable {
    public function dataTable($query) {
        $query = Ticket::where('user_id', Auth::user()->id)->select('tickets.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('tickets.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('tickets.status', 'like', "%".escape_input(request('filter_status'))."%");
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->where('tickets.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('tickets.subject', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('tickets.status', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('subject', function ($query) {
                if ($query->is_read_user == 0) {
                    return '<i class="fa fa-envelope text-warning"></i> '.Str::limit($query->subject, 20, '...').'';
                }
                return Str::limit($query->subject, 20, '...'); 
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->editColumn('updated_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->addColumn('action', function ($query) {
                if ($query->status == 'Closed') {
                    return "<a href=\"javascript:;\" onclick=\"modal('reply', 'Tiket: $query->subject', '".url('ticket/reply/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Balas\" style=\"pointer-events:none; cursor:default;\"><i class=\"fa fa-edit fa-fw\"></i> Balas</a>";
                } else {
                    return "<a href=\"javascript:;\" onclick=\"modal('reply', 'Tiket: $query->subject', '".url('ticket/reply/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Balas\"><i class=\"fa fa-edit fa-fw\"></i> Balas</a>";
                }
            })
            ->addColumn('status', function ($query) {
                return status($query->status);
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['user_id', 'subject', 'action', 'status']);
    }

    public function query(Ticket $model) {
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
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'created_at', 'name' => 'tickets.created_at', 'title' => 'DIBUAT'],
            ['data' => 'created_at', 'name' => 'tickets.created_at', 'title' => 'DIPERBARUI'],
            ['data' => 'subject', 'name' => 'tickets.subject', 'title' => 'SUBJEK'],
            ['data' => 'status', 'name' => 'tickets.status', 'title' => 'STATUS', 'class' => 'text-center', 'max-width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
