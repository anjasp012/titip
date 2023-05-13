<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
	<div class="row">
		<div class="form-group col-lg-6">
			<label>Kategori <text class="text-danger">*</text></label>
			<select class="form-control" name="category_id" id="category_id" data-toggle="select2">
				<option value="" selected>Pilih...</option>
				@foreach($categories as $item)
					<option value="{{ $item->id }}" {{ old('category_id') ? (old('category_id') == $item->id ? 'selected' : '') : ($target->category_id == $item->id ? 'selected' : '')  }}>{{ $item->name }}</option>
				@endforeach
			</select>
			<small class="text-danger category_id_error"></small>
		</div>
		<div class="form-group col-lg-6">
			<label>Sub Kategori <text class="text-danger">*</text></label>
			<select class="form-control" name="sub_category_id" id="sub_category_id" data-toggle="select2">
				<option value="" selected>Pilih Kategori...</option>
				@foreach($sub_categories->where('category_id', $target->category_id) as $key => $item)
					<option value="{{ $item->id }}" {{ old('sub_category_id') ? (old('sub_category_id') == $item->id ? 'selected' : '') : ($target->sub_category_id == $item->id ? 'selected' : '')  }}>{{ $item->name }}</option>
				@endforeach
			</select>
			<small class="text-danger sub_category_id_error"></small>
		</div>
		<div class="form-group col-lg-6">
			<label>Penyedia <text class="text-danger">*</text></label>
			<select class="form-control" name="provider_id" data-toggle="select2">
				<option value="" selected>Pilih...</option>
				@foreach($providers as $item)
					<option value="{{ $item->id }}" {{ old('provider_id') ? (old('provider_id') == $item->id ? 'selected' : '') : ($target->provider_id == $item->id ? 'selected' : '')  }}>{{ $item->name }}</option>
				@endforeach
			</select>
			<small class="text-danger provider_id_error"></small>
		</div>
		<div class="form-group col-lg-6">
			<label>ID Produk Penyedia <text class="text-danger">*</text></label>
			<input type="text" class="form-control" name="provider_product_id" value="{{ old('provider_product_id') ?? $target->provider_product_id }}">
			<small class="text-danger provider_product_id_error"></small>
		</div>
	</div>
	<hr />
	<div class="form-group">
		<label>Nama <text class="text-danger">*</text></label>
		<input type="text" class="form-control" name="name" value="{{ old('name') ?? $target->name }}">
		<small class="text-danger name_error"></small>
	</div>
	<div class="row">
		<div class="form-group col-lg-6">
			<label>Harga Agen <text class="text-danger">*</text></label>
			<input type="text" class="form-control autonumeric-currency" name="agen_price" value="{{ old('agen_price') ?? $target->agen_price }}">
			<small class="text-danger agen_price_error"></small>
		</div>
		<div class="form-group col-lg-6">
			<label>Harga Reseller <text class="text-danger">*</text></label>
			<input type="text" class="form-control autonumeric-currency" name="reseller_price" value="{{ old('reseller_price') ?? $target->reseller_price }}">
			<small class="text-danger reseller_price_error"></small>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-lg-6">
			<label>Bonus <text class="text-danger">*</text></label>
			<input type="text" class="form-control autonumeric-quantity" name="bonus" value="{{ old('bonus') ?? $target->bonus }}">
			<small class="text-danger bonus_error"></small>
		</div>
		<div class="form-group col-lg-6">
			<label>Keuntungan <text class="text-danger">*</text></label>
			<input type="text" class="form-control autonumeric-currency" name="profit" value="{{ old('profit') ?? $target->profit }}">
			<small class="text-danger profit_error"></small>
		</div>
	</div>
	<hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<link href="{{ asset('assets/custom.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/custom-form.js') }}"></script>
<script src="{{ asset('assets/custom-footer.js') }}"></script>
<script>
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
        $('.autonumeric-currency').autoNumeric('init', {
            mDec  : '0',
            aSep  : '.',
            aDec  : ',',
            aSign : 'Rp ',
        });
        $('.autonumeric-quantity').autoNumeric('init', {
            mDec  : '0',
            aSep  : '.',
            aDec  : ',',
            aSign : '# ',
        });
    });
	$(function() {
		$("#main_form").on('submit', function(e) {
			e.preventDefault();
			$.ajax({
				url: $(this).attr('action'),
				method: $(this).attr('method'),
				data: new FormData(this),
				processData: false,
				dataType: 'json',
				contentType: false,
				beforeSend: function() {
					reset_button(0);
					$(document).find('small.text-danger').text('');
					$(document).find('input').removeClass('is-invalid');
				},
				success: function(data) {
					reset_button(1);
					if (data.status == false) {
						if (data.type == 'validation') {
							$.each(data.message, function(prefix, val) {
								$("input[name="+prefix+"]").addClass('is-invalid');
								$('small.'+prefix+'_error').text(val[0]);
							});
						}
						if (data.type == 'alert') {
							swal.fire("Gagal!", data.message, "error");
						}
					} else {
						swal.fire("Berhasil!", data.message, "success").then(function () {
							window.location.reload();
						});
					}
				},
				error:function() {
					swal.fire("Gagal!", "Terjadi kesalahan.", "error");
				},
			});
		});
	});
</script>