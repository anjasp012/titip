@extends('user.layouts.app')
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
        <div class="alert alert-dismissable alert-info text-dark">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <b><i class="fa fa-info-circle"></i> Informasi:</b> Dibawah ini merupakan Top 10 Pesanan, Deposit dan Layanan dengan total tertinggi bulan ini.
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
					Tidak ada Top 10 Pesanan dengan total tertinggi bulan ini.
				</div>
				@else
				<div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead>
							<tr class="thead-light">
								<th>#</th>
								<th>PENGGUNA</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($orders as $item)
							<tr class="{{ ($loop->iteration == 1) ? 'table-warning' : '' }}">
								<td>{{ $loop->iteration }}</td>
								<td>{!! ($loop->iteration == 1) ? '<span class="badge badge-warning"><i class="fa fa-star text-white"></i></span>' : '' !!} {{ $item['full_name'] }}</td>
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
				@if ($deposits->count() == 0)
				<div class="alert alert-dismissable alert-info text-dark">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					Tidak ada Top 10 Deposit dengan total tertinggi bulan ini.
				</div>
				@else
				<div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead>
							<tr class="thead-light">
								<th>#</th>
								<th>PENGGUNA</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody> 
						@foreach ($deposits as $item)
							<tr class="{{ ($loop->iteration == 1) ? 'table-warning' : '' }}">
								<td>{{ $loop->iteration }}</td>
								<td>{!! ($loop->iteration == 1) ? '<span class="badge badge-warning"><i class="fa fa-star text-white"></i></span>' : '' !!} {{ $item['full_name'] }}</td>
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
					Tidak ada Top 10 Layanan dengan total tertinggi bulan ini.
				</div>
				@else
				<div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead>
							<tr class="thead-light">
								<th>#</th>
								<th>LAYANAN</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($services as $item)
							<tr class="{{ ($loop->iteration == 1) ? 'table-warning' : '' }}">
								<td>{{ $loop->iteration }}</td>
								<td>{!! ($loop->iteration == 1) ? '<span class="badge badge-warning"><i class="fa fa-star text-white"></i></span>' : '' !!} {{ $item['name'] }}</td>
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