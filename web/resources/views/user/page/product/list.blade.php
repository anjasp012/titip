@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-dismissable alert-info text-dark">
		    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		    <b>Total Produk:</b> {{ $table->count() }} Produk<br />
			<b>Pembaruan Terakhir Produk:</b> {{ \Carbon\Carbon::parse(\App\Models\WebsiteConfig::where('key', 'last_update_products')->value('value'))->translatedFormat('d F Y - H:i') }}
		</div>
	</div>
	<div class="col-lg-12">
		<div class="card card-white grid-margin">
			<div class="card-body">
				<div class="row">
                    <div class="col-lg-12">
                        <form method="get">
                            <div class="row">
                                @if (!empty($categories) AND $categories <> '')
                                <div class="form-group col-lg-4">
                                    <label>Filter Kategori</label>
                                    <div class="input-group">							
                                        <select class="form-control" name="category_id" id="category_id" data-toggle="select2">
                                            <option value="" selected>Semua...</option>
                                            @foreach($categories as $item)
                                                @if (request('category_id') == $item['id'])
                                                    <option value="{{ $item['id'] }}" selected>{{ $item['name'] }}</option>
                                                @else
                                                    <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <span class="input-group-prepend last">
                                            <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                                        </span>
                                    </div>
                                </div>
                                @endif
                                @if (!empty($sub_categories) AND $sub_categories <> '')
                                <div class="form-group col-lg-4">
                                    <label>Filter Sub Kategori</label>
                                    <div class="input-group">							
                                        <select class="form-control" name="sub_category_id" id="sub_category_id" data-toggle="select2">
                                            <option value="" selected>Semua...</option>
                                            @foreach($sub_categories as $item)
                                                @if (request('sub_category_id') == $item['id'])
                                                    <option value="{{ $item['id'] }}" selected>{{ $item['name'] }}</option>
                                                @else
                                                    <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <span class="input-group-prepend last">
                                            <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                                        </span>
                                    </div>
                                </div>
								@endif
                                <div class="form-group col-lg-4">
                                    <label>Cari</label>
                                    <div class="input-group">							
                                        <input type="text" class="form-control" name="search_value" id="search_value" placeholder="Ketik sesuatu..." value="{{ request('search_value') }}">
                                        <span class="input-group-prepend">
                                            <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
			</div>
		</div>
	</div>
	@foreach ($table->sortByDesc(['category_id'])->unique('category') as $category)
	<div class="col-lg-12">
		<div class="card card-white grid-margin">
			<div class="card-body">
				<h4 class="form-text text-default text-center mb-3">{{ $category->category }}</h4>
				@foreach ($table->sortBy(['sub_category_id'])->where('category_id', $category->category_id)->unique('sub_category') as $sub_category)
				<div class="table-responsive">
					<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap">
						<thead class="thead-dark text-center">
							<tr>
								<th colspan="12">{{ $sub_category->sub_category }}</th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th style="width: 100px;">ID</th>
								<th>NAMA</th>
								<th style="width: 150px;">HARGA AGEN</th>
								<th style="width: 150px;">HARGA RESELLER</th>
								<th style="width: 100px;">STATUS</th>
							</tr>
						</thead>
						<tbody>
						@php
						if ($category->category == 'Paket Data Internet') {
							$sortBy = 'name';
						} else {
							$sortBy = 'agen_price';
						}
						@endphp
						@foreach($table->sortBy([$sortBy])->where('category_id', $category->category_id)->where('sub_category_id', $sub_category->sub_category_id) as $value)
							<tr>
                                <td>{{ $value->provider_product_id }}</td>
                                <td>{{ $value->name }}</td>
								<td>Rp {{ number_format($value->agen_price,0,',','.') }}</td>
								<td>Rp {{ number_format($value->reseller_price,0,',','.') }}</td>
								<td align="center">{!! status($value->status, $value->id) !!}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				@endforeach
			</div>
		</div>
	</div>
	@endforeach
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#category_id').on('change', function() {
			var category_id = $('#category_id').val();
			$.ajax({
				type: "GET",
				url: "{{ url('ajax/product/category') }}",
				data: "category_id="+ category_id,
				dataType: "json",
				success: function(result) {
					$('#sub_category_id').html(result.data);
				}, error: function() {
					swal.fire("Gagal!", "Terjadi kesalahan.", "error");
				}
			});
		});
	});
</script>
@endsection