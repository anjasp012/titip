@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('button')
@include('user.point.filter')
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
    document.getElementById("data-table").children[0].className = "thead-light";
</script>
@endsection