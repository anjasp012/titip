@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-3">
		<div class="card m-b-30">
			<div class="card-body">
				<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a class="nav-link active" href="#password" data-toggle="pill" role="tab"><i class="fa fa-key fa-fw"></i> Kata Sandi</a>
					<a class="nav-link" href="#notifications" data-toggle="pill" role="tab"><i class="fa fa-bell fa-fw"></i> Notifikasi</a>
				</div>
			</div>
		</div>
    </div>
    <div class="col-lg-9">
        <div class="card m-b-30">
            <div class="card-body">
				<div class="tab-content">
					<div class="tab-pane fade show active" id="password">
						<form method="post" action="{{ request()->url() }}" id="main_form">
							@method('patch')
							@csrf
							<div class="form-group">
								<label>Nama Lengkap <text class="text-danger">*</text></label>
								<input type="text" class="form-control" name="full_name" value="{{ old('full_name') ?? Auth::user()->full_name }}">
								<small class="text-danger full_name_error"></small>
							</div>
							<div class="form-group">
								<label>Password Baru</label>
								<input type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" placeholder="Kosongkan jika tidak dibutuhkan">
								<small class="text-danger new_password_error"></small>
							</div>
							<div class="form-group">
								<label>Konfirmasi Password Baru</label>
								<input type="password" class="form-control" name="confirm_new_password" placeholder="Kosongkan jika tidak dibutuhkan">
								<small class="text-danger confirm_new_password_error"></small>
							</div>
							<div class="form-group">
								<label>Password <text class="text-danger">*</text></label>
								<input type="password" class="form-control" name="password">
								<small class="text-danger password_error"></small>
							</div>
							<button type="reset" class="btn btn-danger">Reset</button>
							<button type="submit" class="btn btn-success">Submit</button>
						</form>
					</div>
					<div class="tab-pane fade" id="notifications">
						<h6 class="font-weight-medium">Pemesanan</h6>
						<div class="custom-control custom-switch mb-0">
							<input type="checkbox" class="custom-control-input" id="notification-order" name="notification-order" value="{{ $notification->order == '1' ? '0' : '1' }}" onclick="setNotification(this, '{{ url('account/settings/notification/order') }}')" @if ($notification->order == '1') checked @endif>
							<label class="custom-control-label font-weight-normal" for="notification-order">Informasi pembuatan atau pembaruan pesanan anda.</label>
						</div>
						<h6 class="font-weight-medium mt-2">Deposit</h6>
						<div class="custom-control custom-switch mb-0">
							<input type="checkbox" class="custom-control-input" id="notification-deposit" name="notification-deposit" value="{{ $notification->deposit == '1' ? '0' : '1' }}" onclick="setNotification(this, '{{ url('account/settings/notification/deposit') }}')" @if ($notification->deposit == '1') checked @endif>
							<label class="custom-control-label font-weight-normal" for="notification-deposit">Informasi pembuatan atau pembaruan deposit anda.</label>
						</div>
						<h6 class="font-weight-medium mt-2">Tiket</h6>
						<div class="custom-control custom-switch mb-0">
							<input type="checkbox" class="custom-control-input" id="notification-ticket" name="notification-ticket" value="{{ $notification->ticket == '1' ? '0' : '1' }}" onclick="setNotification(this, '{{ url('account/settings/notification/ticket') }}')" @if ($notification->ticket == '1') checked @endif>
							<label class="custom-control-label font-weight-normal" for="notification-ticket">Informasi pembuatan atau pembaruan tiket anda.</label>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<script>
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
						swal.fire("Berhasil!", data.message, "success");
						$("input[name=password]").val('');
					}
				},
				error:function() {
					swal.fire("Gagal!", "Terjadi kesalahan.", "error");
				},
			});
		});
	});
	function setNotification(elt, url) {
		$.ajax({
			url: url+'/'+$(elt).attr('value'),
			type: 'GET',
			error: function() {
				alertify.error('<i class="fa fa-times"></i> Terjadi kesalahan.')
			},
			success: function(result) {
				result = JSON.parse(result);
				if (result.result == false) {
					alertify.error('<i class="fa fa-times"></i> Terjadi kesalahan.')
				} else {
					if ($(elt).attr('value') == '1') {
						$("input[id="+$(elt).attr('id')+"]").val('0');
					} else {
						$("input[id="+$(elt).attr('id')+"]").val('1');
					}
					alertify.success('<span class="text-white"><i class="fa fa-check"></i> '+result.message+'</span>');
				}
			}
		});
	}
</script>
@endsection