<?php

namespace App\DataTables\Admin;

use App\Models\Deposit;
use App\Models\Posting;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PostingDataTable extends DataTable
{
    public function dataTable($query)
    {
        $query = Posting::with('user')->select('postings.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') and request('filter_created_at') <> null) {
                    $query->where('postings.created_at', 'like', "%" . escape_input(request('filter_created_at')) . "%");
                }
                if (request()->has('filter_user')) {
                    $query->whereHas('user', function ($query) {
                        $query->where('username', 'like', "%" . escape_input(request('filter_user')) . "%");
                    });
                }
                if (request()->has('filter_status') and request('filter_status') <> null) {
                    $query->where('postings.status', escape_input(request('filter_status')));
                }
                if (request()->has('search') and request('search') <> null) {
                    $query->where(function ($query) {
                        $query
                            ->whereHas('user', function ($query) {
                                $query->where('username', 'like', "%" . escape_input(request('search')) . "%");
                            })
                            ->orWhere('postings.id', 'like', "%" . escape_input(request('search')) . "%")
                            ->orWhere('postings.konten', 'like', "%" . escape_input(request('search')) . "%");
                    });
                }
            })
            ->editColumn('user_id', function ($query) {
                if ($query->user == true) {
                    return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Pengguna', '" . url('admin/user/detail/' . $query->user_id . '') . "')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">" . Str::limit($query->user->username, 10, '...') . "</a>";
                }
                return null;
            })
            ->editColumn('subject', function ($query) {
                if ($query->is_read_admin == 0) {
                    return '<i class="fa fa-envelope text-warning"></i> ' . Str::limit($query->subject, 20, '...') . '';
                }
                return Str::limit($query->subject, 20, '...');
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d/m/y');
            })
            ->editColumn('updated_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })

            ->addColumn('konten', function ($query) {
                return '<a href="' . url('posting/read/' . $query->id . '" target="_blank">') . Str::limit(strip_tags($query->konten), '50') . "</a>";
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->subject', '" . url('admin/posting/delete/' . $query->id . '') . "', '')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->addColumn('status', function ($query) {
                return status($query->status);
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['user_id', 'konten', 'action', 'status']);
    }

    public function query(Posting $model)
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
                            d.filter_user = $("#filter_user option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
            ])
            ->orderBy(0);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'created_at', 'name' => 'postings.created_at', 'title' => 'DIBUAT', 'width' => '150'],
            ['data' => 'user_id', 'name' => 'user.username', 'title' => 'PENGGUNA', 'width' => '150'],
            ['data' => 'konten', 'name' => 'postings.konten', 'title' => 'KONTEN'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '110'],
        ];
    }

    protected function filename()
    {
        return 'Lead_' . date('YmdHis');
    }
}
