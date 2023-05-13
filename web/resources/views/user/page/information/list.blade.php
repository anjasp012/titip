@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('button')
@include('user.page.information.filter')
@endsection
@section('content')

<div class="col-md-12">
    <div class="card m-b-30">
        <div class="card-body">
            <h5 class="card-title">Informasi</h5>
            {!! $dataTable->table(['class' => 'table table-borderless table-hover mb-0'], false) !!}
        </div>
    </div>
</div>


</div>
{!! $dataTable->scripts() !!}
<script>
    document.getElementById("data-table").children[0].className = "thead-light";
</script>
@endsection
