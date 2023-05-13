<?php

namespace App\DataTables\Admin;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OrderDataTable extends DataTable {
    public function dataTable($query) {
        $query = Order::with('user', 'product', 'product_provider')->select('orders.*');
        return datatables()
            ->eloquent($query)
            ->filter(function ($query) {
                if (request()->has('filter_created_at') AND request('filter_created_at') <> null) {
                    $query->where('orders.created_at', 'like', "%".escape_input(request('filter_created_at'))."%");
                }
                if (request()->has('filter_user') AND request('filter_user') <> null) {
                    $query->whereHas('user', function($query){
                        $query->where('username', 'like', "%".escape_input(request('filter_user'))."%");
                    });
                }
                if (request()->has('filter_product') AND request('filter_product') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('product', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('filter_product'))."%");
                        });
                    });
                }
                if (request()->has('filter_product_provider') AND request('filter_product_provider') <> null) {
                    $query->whereHas('product_provider', function($query){
                        $query->where('name', 'like', "%".escape_input(request('filter_product_provider'))."%");
                    });
                }
                if (request()->has('filter_status') AND request('filter_status') <> null) {
                    $query->where('orders.status', '=', escape_input(request('filter_status')));
                }
                if (request()->has('search') AND request('search') <> null) {
                    $query->where(function($query) {
                        $query
                        ->whereHas('product', function($query) {
                            $query->where('name', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhereHas('user', function($query) {
                            $query->where('username', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhereHas('product_provider', function($query){
                            $query->where('name', 'like', "%".escape_input(request('search'))."%");
                        })
                        ->orWhere('orders.id', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('orders.price', 'like', "%".escape_input(request('search'))."%")
                        ->orWhere('orders.status', 'like', "%".escape_input(request('search'))."%");
                    });
                }
            })
            ->editColumn('product_id', function ($query) {
                if ($query->product == true) {
                    return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Produk', '".url('admin/product/detail/'.$query->product_id.'')."')\" class=\"text-dark\" data-toggle=\"tooltip\" title=\"Detail\">".Str::limit($query->product->name, 30, '...')."</a>";
                }
                return null;
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
                return "<a href=\"javascript:;\" onclick=\"modal('detail', 'Pesanan', '".url('admin/order/detail/'.$query->id.'')."')\" class=\"badge badge-info badge-sm\" data-toggle=\"tooltip\" title=\"Detail\">#$query->id</a>";
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
            ->editColumn('price', function ($query) {
                return 'Rp '.number_format($query->price,0,',','.');
            })
            ->addColumn('action', function ($query) {
                return "
                <a href=\"javascript:;\" onclick=\"modal('edit', 'Pesanan', '".url('admin/order/form/'.$query->id.'')."')\" class=\"badge badge-warning badge-sm\" data-toggle=\"tooltip\" title=\"Edit\"><i class=\"fa fa-edit fa-fw\"></i></a>
                <a href=\"javascript:;\" onclick=\"deleteData(this, $query->id, '$query->id', '".url('admin/order/delete/'.$query->id.'')."')\" class=\"badge badge-danger badge-sm\" data-toggle=\"tooltip\" title=\"Hapus\"><i class=\"fa fa-trash\"></i></a>
                ";
            })
            ->addColumn('status', function ($query) {
                return status($query->status);
            })
            ->setRowClass(function ($query) {
                if ($query->status == 'Pending') {
                    return 'table-warning';
                } elseif ($query->status == 'Processing') {
                    return 'table-primary';
                } elseif ($query->status == 'Success') {
                    return 'table-success';
                } elseif ($query->status == 'Error') {
                    return 'table-danger';
                } elseif ($query->status == 'Partial') {
                    return 'table-danger';
                } else {
                    return 'table-secondary';
                }
            })
            ->setRowId(function ($query) {
                return $query->id;
            })
            ->rawColumns(['id', 'user_id', 'product_id', 'target', 'action', 'status']);
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
                            d.filter_user = $("#filter_user option:selected").val();
                            d.filter_product = $("#filter_product option:selected").val();
                            d.filter_product_provider = $("#filter_product_provider option:selected").val();
                            d.filter_status = $("#filter_status option:selected").val();
                            d.search = $("input[name=search]").val();
                        }',
                    ])
                    ->orderBy(0);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'created_at', 'name' => 'orders.created_at', 'title' => 'DIBUAT'],
            ['data' => 'user_id', 'name' => 'user.username', 'title' => 'PENGGUNA'],
            ['data' => 'product_id', 'name' => 'product.name', 'title' => 'PRODUK'],
            ['data' => 'price', 'name' => 'orders.price', 'title' => 'HARGA'],
            ['data' => 'status', 'name' => 'orders.status', 'title' => 'STATUS', 'class' => 'text-center', 'max-width' => '50'],
            ['data' => 'action', 'title' => 'AKSI', 'class' => 'text-center', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename() {
        return 'Lead_' . date('YmdHis');
    }
}
