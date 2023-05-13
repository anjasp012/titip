@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
    <div class="col-lg-6 grid-margin">
        <div class="card card-white stats-widget card m-b-30">
            <div class="card-body">
                <div class="float-left">
                    <span class="label label-warning mb-3">Referral</span>
                    <h5 class="">{{ number_format($referral_code_used->count('*'),0,',','.') }} Kali</h5>
                </div>
                <div class="float-right">
                    <i class="fa fa-user icon"></i>
                </div>
                <div class="clearfix"></div>
                <div class="">
                    <p class="mb-1 text-muted text-truncate">Total Kode Referral digunakan</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 grid-margin">
        <div class="card card-white stats-widget card m-b-30">
            <div class="card-body">
                <div class="float-left">
                    <span class="label label-warning mb-3">Bonus</span>
                    <h5 class="stats-number">{{ number_format($bonus_received->sum('amount'),0,',','.') }} Poin</h5>
                </div>
                <div class="float-right">
                    <i class="fa fa-coins icon"></i>
                </div>
                <div class="clearfix"></div>
                <div class="mt-0">
                    <p class="mb-0 text-muted text-truncate">Total Bonus diterima</p>
                </div>
            </div>
        </div>
    </div>
	<div class="col-lg-12">
        <div class="alert alert-dismissable alert-info text-dark">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <b><i class="fa fa-info-circle"></i> Informasi:</b> Ajak orang sekitar anda mendaftar dengan menggunakan Kode Referral anda untuk mendapatkan Bonus Poin Pendafaran.
        </div>
        <div class="card">
            <div class="card-body">
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ url('auth/register').'/'.Auth::user()->referral_code }}" id="referral_code" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="button" onclick="copy('referral_code')"><i class="fa fa-copy fa-fw"></i> Salin</button>
                    </div>
                </div>
			</div>
		</div>
	</div>
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