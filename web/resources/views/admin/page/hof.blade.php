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
    <div class="col-lg-6">
		<div class="card m-b-30">
			<div class="card-body">
                <h4 class="{{ $card_title_class }} text-uppercase mb-3">
                    <i class="fa fa-trophy"></i> 10 Pesanan Terbanyak
                </h4>
				@if ($orders->count() == 0)
				<div class="alert alert-dismissable alert-info text-dark">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					Tidak ada Top 10 Pesanan dengan total tertinggi.
				</div>
				@else
				<div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead class="thead-light">
							<tr>
								<th>#</th>
								<th>PENGGUNA</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($orders as $item)
							<tr class="{{ ($loop->iteration == 1) ? 'table-warning' : '' }}">
								<td>{{ $loop->iteration }}</td>
								<td>{!! ($loop->iteration == 1) ? '<span class="badge badge-warning"><i class="fa fa-star text-white"></i></span>' : '' !!} <a href="javascript: void(0);" onclick="modal('detail', 'Pengguna', '{{ url('admin/user/detail/'.$item->id.'') }}')" class="text-dark">{{ $item['full_name'] }} ({{ $item['username'] }})</a></td>
								<td>Rp {{ number_format($item['amount'],0,',','.') }} ({{ number_format($item['total'],0,',','.') }})</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				@endif
			</div>
		</div>
    </div>
    <div class="col-lg-6">
		<div class="card m-b-30">
			<div class="card-body">
                <h4 class="{{ $card_title_class }} text-uppercase mb-3">
                    <i class="fa fa-trophy"></i> 10 Deposit Terbanyak
                </h4>
				@if ($services->count() == 0)
				<div class="alert alert-dismissable alert-info text-dark">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					Tidak ada Top 10 Deposit dengan total tertinggi.
				</div>
				@else
				<div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead class="thead-light">
							<tr>
								<th>#</th>
								<th>PENGGUNA</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody> 
						@foreach ($deposits as $item)
							<tr class="{{ ($loop->iteration == 1) ? 'table-warning' : '' }}">
								<td>{{ $loop->iteration }}</td>
								<td>{!! ($loop->iteration == 1) ? '<span class="badge badge-warning"><i class="fa fa-star text-white"></i></span>' : '' !!} <a href="javascript: void(0);" onclick="modal('detail', 'Pengguna', '{{ url('admin/user/detail/'.$item->id.'') }}')" class="text-dark">{{ $item['full_name'] }} ({{ $item['username'] }})</a></td>
								<td>Rp {{ number_format($item['amount'],0,',','.') }} ({{ number_format($item['total'],0,',','.') }})</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				@endif
			</div>
		</div>
    </div>
	<div class="col-lg-6 offset-lg-3">
		<div class="card m-b-30">
			<div class="card-body">
				<h4 class="{{ $card_title_class }} text-uppercase mb-3">
					<i class="fa fa-trophy"></i> 10 Layanan Terbaik
				</h4>
				@if ($services->count() == 0)
				<div class="alert alert-dismissable alert-info text-dark">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					Tidak ada Top 10 Layanan dengan total tertinggi.
				</div>
				@else
				<div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead class="thead-light">
							<tr>
								<th>#</th>
								<th>LAYANAN</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($services as $item)
							<tr class="{{ ($loop->iteration == 1) ? 'table-warning' : '' }}">
								<td>{{ $loop->iteration }}</td>
								<td>{!! ($loop->iteration == 1) ? '<span class="badge badge-warning"><i class="fa fa-star text-white"></i></span>' : '' !!} <a href="javascript: void(0);" onclick="modal('detail', 'Layanan', '{{ url('admin/service/detail/'.$item->id.'') }}')" class="text-dark">{{ $item['name'] }}</a></td>
								<td>Rp {{ number_format($item['amount'],0,',','.') }} ({{ number_format($item['total'],0,',','.') }})</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				@endif
			</div>
		</div>
    </div>
</div>
@endsection