@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('button')
<a href="javascript:;" onclick="modal('add', 'Kategori', '{{ url('admin/product/category/form') }}')" class="btn btn-success btn-sm">
	<i class="fa fa-plus fa-fw"></i> Tambah Kategori
</a>
@include('admin.product.category.filter')
@endsection
@section('content')
<div class="row">
	<div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-borderless table-hover mb-0'], false) !!}
                </div>
			</div>
		</div>
	</div>
</div>
{!! $dataTable->scripts() !!}
<script>
    var table = document.getElementById("data-table");
    table.children[0].className = "thead-light";
    $('#search-form').on('submit', function(e) {
        window.LaravelDataTables["data-table"].draw();
        e.preventDefault();
    });
</script>
@endsection