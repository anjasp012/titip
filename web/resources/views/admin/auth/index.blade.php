@extends('admin.layouts.app')
@section('breadcrumb-first', 'Dasbor')
@section('breadcrumb-second', website_config('main')->website_name)
@section('content')
@if (Auth::guard('admin')->check() == true)
{{--
<div class="row">
    <div class="col-lg-4 grid-margin">
        <div class="card card-white stats-widget card m-b-30">
            <div class="card-body">
                <div class="float-left">
                    <span class="label label-warning mb-3">Saldo</span>
                    <h5 class="stats-number mt-2">Rp {{ number_format($users->sum('balance'),0,',','.') }} ({{ number_format($users->count('*'),0,',','.') }})</h5>
                </div>
                <div class="float-right">
                    <i class="fa fa-money-check-alt text-success"></i>
                </div>
                <div class="clearfix"></div>
                <div class="">
                    <p class="mb-1 text-muted text-truncate">Total Saldo Pengguna</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 grid-margin">
        <div class="card card-white stats-widget card m-b-30">
            <div class="card-body">
                <div class="float-left">
                    <span class="label label-warning mb-3">Pesanan</span>
                    <h5 class="stats-number mt-2">Rp {{ number_format($orders->sum('price'),0,',','.') }} ({{ number_format($orders->count('*'),0,',','.') }})</h5>
                </div>
                <div class="float-right">
                    <i class="fa fa-shopping-cart text-success"></i>
                </div>
                <div class="clearfix"></div>
                <div class="">
                    <p class="mb-1 text-muted text-truncate">Total Pesanan Pengguna</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 grid-margin">
        <div class="card card-white stats-widget card m-b-30">
            <div class="card-body">
                <div class="float-left">
                    <span class="label label-warning mb-3">Deposit</span>
                    <h5 class="stats-number mt-2">Rp {{ number_format($deposits->sum('amount'),0,',','.') }} ({{ number_format($deposits->count('*'),0,',','.') }})</h5>
                </div>
                <div class="float-right">
                    <i class="fa fa-money-check-alt text-success"></i>
                </div>
                <div class="clearfix"></div>
                <div class="">
                    <p class="mb-1 text-muted text-truncate">Total Deposit Pengguna</p>
                </div>
            </div>
        </div>
    </div>
</div>
--}}
@endif
@endsection
