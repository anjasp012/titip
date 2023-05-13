<?php

namespace App\DataTables\Admin\Log;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\BankMutation;
use Yajra\DataTables\Services\DataTable;

class BankMutationDataTable extends DataTable {
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_bank') AND request('filter_bank') <> null) {
                    $query->where('bank', 'like', "%".escape_input(request('filter_bank'))."%");
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->where('id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('bank', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('amount', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('note', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('created_at', function ($query) {
                return Carbon::parse($query->created_at)->translatedFormat('d F Y - H:i');
            })
            ->editColumn('amount', function ($query) {
                return 'Rp '.number_format($query->amount,0,',','.');
            })
            ->editColumn('balance', function ($query) {
                return 'Rp '.number_format($query->balance,0,',','.');
            })
            ->editColumn('is_read', function ($query) {
                if ($query->is_read == '1') {
                    return '<i class="fa fa-check text-success"></i>';
                } else {
                    return '<i class="fa fa-times text-warning"></i>';
                }
            })
            ->editColumn('note', function ($query) {
                return "<a href=\"javascript:;\" onclick=\"info('$query->note')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->note, 30, '...')."</a>";
            })
            ->setRowClass(function ($query) {
                if ($query->is_read == '1') {
                    return 'table-success';
                } elseif ($query->is_read == '0') {
                    return 'table-warning';
                } else {
                    return 'table-secondary';
                }
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['is_read', 'note']);
    }
    
    public function query(BankMutation $model) {
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
                            d.filter_bank = $("#filter_bank option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }
    
    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50'],
            ['data' => 'created_at', 'title' => 'DIBUAT', 'width' => '180'],
            ['data' => 'bank', 'title' => 'BANK'],
            ['data' => 'amount', 'title' => 'JUMLAH'],
            ['data' => 'note', 'title' => 'CATATAN'],
            ['data' => 'is_read', 'title' => 'DIBACA', 'class' => 'text-center', 'width' => '20'],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
