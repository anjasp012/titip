<?php

namespace App\DataTables\Admin;

use App\Models\Deposit;
use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TicketDataTable extends DataTable {
    public function dataTable($query) {
        $query = Ticket::with('user')->select('tickets.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('tickets.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_user')) {
                    $query->whereHas('user', function($query){
                        $query->where('username', 'like', "%".escape_input(request('filter_user'))."%");
                    });
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('tickets.status', escape_input(request('filter_status')));
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('user', function($query) {
                            $query->where('username', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('tickets.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('tickets.subject', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('tickets.status', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('user_id', function ($query) {
                if ($query->user == true) {
                    return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Pengguna', '".url('admin/user/detail/'.$query->user_id.'')."')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->user->username, 10, '...')."</a>";
                }
                return null;
            })
            ->editColumn('subject', function ($query) {
                if ($query->is_read_admin == 0) {
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
                    return "
                    <a href=\"javascript:;\" onclick=\"closeTicket(this, $query->id, '$query->subject', '".url('admin/ticket/close/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Tutup\" style=\"pointer-events:none; cursor:default;\"><i class=\"fa fa-times\"></i></a>
                    <a href=\"javascript:;\" onclick=\"modal('reply', 'Tiket: $query->subject', '".url('admin/ticket/reply/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Balas\" style=\"pointer-events:none; cursor:default;\"><i class=\"fa fa-edit fa-fw\"></i></a>
                    <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->subject', '".url('admin/ticket/delete/'.$query->id.'')."', '<br /><small>jika Anda menghapus <b>$query->subject</b>, maka <b>Balasan Tiket</b> yang berhubungan dengan <b>$query->subject</b> juga akan ikut terhapus.')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                    ";
                } else {
                    return "
                    <a href=\"javascript:;\" onclick=\"closeTicket(this, $query->id, '$query->subject', '".url('admin/ticket/close/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Tutup\"><i class=\"fa fa-times\"></i></a>
                    <a href=\"javascript:;\" onclick=\"modal('reply', 'Tiket: $query->subject', '".url('admin/ticket/reply/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Balas\"><i class=\"fa fa-edit fa-fw\"></i></a>
                    <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->subject', '".url('admin/ticket/delete/'.$query->id.'')."', '<br /><small>jika Anda menghapus <b>$query->subject</b>, maka <b>Balasan Tiket</b> yang berhubungan dengan <b>$query->subject</b> juga akan ikut terhapus.')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                    ";
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
            ['data' => 'created_at', 'name' => 'tickets.created_at', 'title' => 'DIBUAT'],
            ['data' => 'created_at', 'name' => 'tickets.created_at', 'title' => 'DIPERBARUI'],
            ['data' => 'user_id', 'name' => 'user.username', 'title' => 'PENGGUNA'],
            ['data' => 'subject', 'name' => 'tickets.subject', 'title' => 'SUBJEK'],
            ['data' => 'status', 'name' => 'tickets.status', 'title' => 'STATUS', 'class' => 'text-center', 'max-width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
