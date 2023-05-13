@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col d-flex justify-content-center">
        <div class="col col-lg-8">
			<div class="card">
				<div class="card-body">
					<form method="post" action="{{ request()->url() }}">
						@csrf
						@if ($target->slug == 'token-pln')
						<div class="form-group">
							<label>No. Meter/ID Pelanggan <text class="text-danger">*</text></label>
							<input type="text" class="form-control @error('target') is-invalid @enderror" name="target" id="target" value="{{ old('target') }}" placeholder="">
							@error('target')
							<div class="invalid-feedback">
								{{ $message }}
							</div>
							@enderror
						</div>
						<div class="form-group hidden">
							<label>No. Handphone <text class="text-danger">*</text></label>
							<input type="number" class="form-control @error('target') is-invalid @enderror" name="target" id="target" value="1" placeholder="">
							@error('target')
							<div class="invalid-feedback">
								{{ $message }}
							</div>
							@enderror
						</div>
						@else
						@if ($target->slug == 'saldo-e-money' OR $target->slug == 'voucher-game')
						<div class="form-group">
							<label>Sub Kategori <text class="text-danger">*</text></label>
							<select class="form-control @error('sub_category') is-invalid @enderror" name="sub_category" id="sub_category">
								<option value="" selected>Pilih...</option>
								@foreach(\App\Models\SubCategory::where('category_id', $target->id)->get() as $key => $value)
									@if (old('sub_category') == $value['id'])
										<option value="{{ $value['id'] }}" selected>{{ $value['name'] }}</option>
									@else
										<option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
									@endif
								@endforeach
							</select>
							@error('sub_category')
							<div class="invalid-feedback">
								{{ $message }}
							</div>
							@enderror
						</div>
						@endif
						<div class="form-group">
							<label>No. Handphone <text class="text-danger">*</text></label>
							<input type="number" class="form-control @error('target') is-invalid @enderror" name="target" id="target" value="{{ old('target') }}" placeholder="">
							@error('target')
							<div class="invalid-feedback">
								{{ $message }}
							</div>
							@enderror
						</div>
						@endif
						<div id="products" class="hidden"></div>
						<div id="error_response" class="hidden">
							<div class="alert alert-danger"><div id="error_message"></div></div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@if ($target->slug == 'saldo-e-money' OR $target->slug == 'voucher-game')
<script type="text/javascript">
	$(document).ready(function() {
		$('#sub_category').on('change', function() {
			check_product();
		});
		$('#target').on('keyup change', function() {
			var sub_category = $('#sub_category').val();
			if (!sub_category) {
				$('#error_response').removeClass('hidden');
				$('#error_response').addClass('show');
				$('#error_message').html('Pilih Sub Kategori terlebih dahulu.');
			} else {
				check_product();
			}
		});
	});
</script>
@elseif ($target->slug == 'token-pln')
<script type="text/javascript">
	$(document).ready(function() {
		$('#target').on('keyup change', function() {
			var target = $('#target').val();
			if (!target) {
				$('#error_response').removeClass('hidden');
				$('#error_response').addClass('show');
				$('#error_message').html('Masukan No. Meter/ID Pelanggan terlebih dahulu.');
			} else {
				check_product();
			}
		});
	});
</script>
@else
<script type="text/javascript">
	$(document).ready(function() {
		$('#target').keyup(function () {
			check_product();
		});
	});
</script>
@endif
<script>
	function check_product() {
		var target = $('#target').val();
		data = {}
		data.phone_number = target;
		data.category = "<?= $target->slug ?>";
		if (data.category == 'saldo-e-money' || data.category == 'voucher-game') {
			data.sub_category = $('#sub_category').val();
		}
		$.ajax({
			type: "GET",
			url: "{{ url('ajax/product/list') }}",
			data: data,
			dataType: "json",
			success: function(result) {
				if (result.result == false) {
					$('#products').removeClass('show');
					$('#products').addClass('hidden');
					$('#error_response').removeClass('hidden');
					$('#error_response').addClass('show');
					$('#error_message').html(result.message);
				} else {
					$('#products').removeClass('hidden');
					$('#products').addClass('show');
					$('#error_response').removeClass('show');
					$('#error_response').addClass('hidden');
					$('#products').html(result.data);
				}
			}, error: function() {
				alert('Terjadi kesalahan.');
			}
		});
	}
	function purchaseProduct(name, target, price, url, type = '') {
		swal.fire({
			title: "Konfirmasi Pembelian",
			html: "Anda akan membeli <b style='font-weight: bold;'>"+name+"</b> ke nomor <b style='font-weight: bold;'>"+target+"</b> seharga <b style='font-weight: bold;'>"+price+"</b>",
			type: "warning",
			showCancelButton: !0,
			confirmButtonText: "Ya, Beli!",
			cancelButtonText: "Tutup",
			confirmButtonClass: "btn btn-success mt-2",
			cancelButtonClass: "btn btn-secondary ml-2 mt-2",
			buttonsStyling: !1,
		}).then(result => {
			if (result.value) {
				data = {};
				data.target = target;
				type = "<?= $target->slug ?>";
				if (type == 'token-pln') {
					data.input_pln = $('#input_pln').val();
				}
				$.ajax({
					url: url,
					type: 'GET',
					data: data,
					error: function() {
						swal.fire("Gagal", "Terjadi kesalahan.", "error");
					},
					success: function(result) {
						result = JSON.parse(result);
						if (result.result == false) {
							swal.fire("Gagal", ""+result.message+"", "error");
						} else {
							swal.fire("Berhasil!", ""+result.message+".", "success").then(function () {
								window.location.reload();
							});
						}
					}
				});
			}
		});
	}
</script>
@endsection