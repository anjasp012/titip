@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('button')
<a href="javascript:;" onclick="modal('add', 'Akun Bank', '{{ url('admin/settings/bank_account/form') }}')" class="btn btn-success btn-sm">
	<i class="fa fa-plus fa-fw"></i> Tambah Akun Bank
</a>
@include('admin.settings.bank_account.filter')
@endsection
@section('content')
<div class="row">
	<div class="col-lg-12">
        <div class="card m-b-30">
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
    document.getElementById("data-table").children[0].className = "thead-light";
</script>
@endsection