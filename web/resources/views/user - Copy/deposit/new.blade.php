@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card m-b-30">
			<div class="card-body">
				<form method="post" action="{{ request()->url() }}" id="main_form">
                    @csrf
                    <div class="form-group">
						<label>Metode <text class="text-danger">*</text></label>
						<select class="form-control @error('deposit_method_id') is-invalid @enderror" name="deposit_method_id" id="deposit_method_id" data-toggle="select2">
							<option value="" selected>Pilih...</option>
							@foreach($deposit_methods as $item)
								<option value="{{ $item->id }}" {{ old('deposit_method_id') ? (old('deposit_method_id') == $item->id ? 'selected' : '') : ''  }}>{{ $item->name }} ({{ $item->payment }} - {{ $item->type }}) | Minimal Rp: {{ number_format($item->min,0,',','.') }}</option>
							@endforeach
						</select>
						<small class="text-danger deposit_method_id_error"></small>
					</div>
					<div class="row">
						<div class="form-group col-lg-6">
							<label>Jumlah <text class="text-danger">*</text></label>
							<input type="text" class="form-control autonumeric-currency" name="amount" id="amount" value="{{ old('amount') ?? 'Rp ' }}">
							<small class="text-danger amount_error"></small>
						</div>
						<div class="form-group col-lg-6">
							<label>Saldo Didapat</label>
							<input type="hidden" id="rate" value="0">
							<span class="form-control  autonumeric-currency" data-a-sign="Rp " data-a-sep="." data-a-dec="," id="balance">Rp</span>
						</div>
					</div>
					<div class="form-group">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="approval" name="approval" value="1"> 
							<label class="custom-control-label font-weight-normal" for="approval">
								Saya telah membaca <a href="#information" class="text-primary">Informasi</a>
							</label>
						</div>
						<small class="text-danger approval_error"></small>
					</div>
					<button type="reset" class="btn btn-danger">Reset</button>
					<button type="submit" class="btn btn-success">Submit</button>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-4" id="information">
		<div class="card m-b-30">
			<div class="card-body">
				{!! website_config('other')->deposit_info !!}
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
        $('#deposit_method_id').on('change', function() {
			var deposit_method_id = $('#deposit_method_id').val();
			$.ajax({
				type: "GET",
				url: "{{ url('ajax/deposit/get_balance') }}",
				data: "deposit_method_id="+ deposit_method_id,
				dataType: "json",
				success: function(result) {
                    $('#rate').val(result.data.rate);
				}, error: function() {
					swal.fire("Gagal!", "Terjadi kesalahan.", "error");
				}
			});
		});
		function numeric(string) {
			return string.trim().replace('Rp ', '').split('.').join('');
		}
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
		$("#amount").keyup(function() { 
			var amount = $('#amount').val(), rate = $('#rate').val()
			var total = (numeric(amount) * rate);
			$('#balance').html(total.toFixed(0)).autoNumeric('update');
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
	function reset_button(value = 0) {
		if (value == 0) {
			$('button[type="submit"]').attr('disabled', 'true');
			$('button[type="submit"]').text('');
			$('button[type="submit"]').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
			$('button[type="reset"]').hide();
		} else {
			$('button[type="submit"]').removeAttr('disabled');
			$('button[type="submit"]').removeAttr('span');
			$('button[type="submit"]').text('Submit');
			$('button[type="reset"]').show();
		}
	}
</script>
@endsection