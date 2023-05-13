@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
        <div class="alert alert-dismissable alert-info text-dark">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <b><i class="fa fa-info-circle"></i> Informasi:</b> 
            <br /> - 1 Poin = Rp 1 Saldo.
            <br /> - Minimal penukaran Poin adalah 10.000.
        </div>
		<div class="card">
			<div class="card-body">
				<form method="post" action="{{ request()->url() }}" id="main_form">
                    @csrf
                    <div class="form-group">
                        <label>Jumlah <text class="text-danger">*</text></label>
                        <input type="text" class="form-control autonumeric-quantity" name="amount" id="amount" value="">
                        <small class="text-danger amount_error"></small>
                    </div>
					<button type="reset" class="btn btn-danger">Reset</button>
					<button type="submit" class="btn btn-success">Submit</button>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
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