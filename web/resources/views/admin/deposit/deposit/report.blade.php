@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
@php
if (website_config('template')->number == 'first-template') {
    $card_title_class = 'card-title';
} elseif (website_config('template')->number == 'second-template') {
    $card_title_class = 'header-title';
} elseif (website_config('template')->number == 'third-template') {
    $card_title_class = 'header-title';
} elseif (website_config('template')->number == 'custom-template') {
    $card_title_class = 'header-title';
} else {
    $card_title_class = 'card-title';
}
@endphp
<div class="row">
	<div class="col-lg-12">
		<div class="card m-b-30">
			<div class="card-body">
                <h4 class="{{ $card_title_class }} text-uppercase mb-3">
					<i class="fa fa-table"></i> Laporan {{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }} sampai dengan {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}
                </h4>
				<form method="get">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label>Tanggal Mulai</label>
                            <div class="input-group">							
                                <input type="text" class="form-control datepicker-autoclose @error('start_date') is-invalid @enderror" autocomplete="off" name="start_date" id="datepicker-autoclose" data-provide="datepicker" data-date-autoclose="true" placeholder="yyyy-mm-dd" value="{{ old('start_date') ?? date('d F Y', strtotime($start_date)) }}">
                                <span class="input-group-prepend last">
                                    <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                                </span>
                                @error('start_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Tanggal Berakhir {{ old('end_date') }}</label>
                            <div class="input-group">							
                                <input type="text" class="form-control datepicker-autoclose @error('end_date') is-invalid @enderror" autocomplete="off" name="end_date" id="datepicker-autoclose" data-provide="datepicker" data-date-autoclose="true" placeholder="yyyy-mm-dd" value="{{ old('end_date') ?? date('d F Y', strtotime($end_date)) }}">
                                <span class="input-group-prepend last">
                                    <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                                </span>
                                @error('end_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<div class="card m-b-30">
			<div class="card-body">
				<div class="table-responsive">
					<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap">
						<thead>
							<tr>
								<th class="text-uppercase">Total</th>
								<th class="text-uppercase">Pending</th>
								<th class="text-uppercase">Canceled</th>
								<th class="text-uppercase">Success</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Rp {{ number_format($deposits['all']->amount,0,',','.') }} ({{ number_format($deposits['all']->total,0,',','.') }})</td>
								<td>Rp {{ number_format($deposits['pending']->amount,0,',','.') }} ({{ number_format($deposits['pending']->total,0,',','.') }})</td>
								<td>Rp {{ number_format($deposits['canceled']->amount,0,',','.') }} ({{ number_format($deposits['canceled']->total,0,',','.') }})</td>
								<td>Rp {{ number_format($deposits['success']->amount,0,',','.') }} ({{ number_format($deposits['success']->total,0,',','.') }})</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection