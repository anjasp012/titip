@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="col-md-3">
	@include('user.layouts.left_profile')
</div>
<div class="col-md-9">
	<div class="mb-5 pb-5">
		
			<div class="">
				<div class="card info-card revenue-card">

					<div class="card-body">
						<h5 class="card-title">Account Balance</h5>
		
						<div class="d-flex align-items-center">
								<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
									<i class="bi bi-currency-dollar"></i>
									</div>
									<div class="ps-3">
									<h6>Rp.{{ number_format(Auth::user()->balance,0,',','.') }}</h6>
									<span class="text-success small pt-1 fw-bold">{{Auth::user()->full_name}}</span>
								</div>
							</div>
						</div>
					</div>
				</div><!-- End Revenue Card -->

				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Nominal Deposit</h5>
						<input type="text" class="form-control autonumeric-currency" name="nominal" id="nominal">
						<div class="mt-3">
							<b class="text-dark">Metode Topup</b>
						</div>
						<hr>
						<b class="text-dark">Bank Transfer</b>
						<div class="row mt-2">
							@foreach(\App\Models\DepositMethod::where('payment','Transfer')->get() as $key => $value)
							<div class="col-4 mb-2">
								<a href="javascript:void(0)" class="btnmetode" data-prop="img{{$value['id']}}" id="{{$value['id']}}">
									<img src="{{ asset('assets/images/bank/'.$value['merchant'].'.png') }}" class="imgmethod methodlight" id="img{{$value['id']}}" width="80">
								</a>
							</div>
							@endforeach
						</div>

						<b class="text-dark">E-Money</b>
						<div class="row mt-2">
							@foreach(\App\Models\DepositMethod::where('payment','E-Money')->get() as $key => $value)
							<div class="col-4 mb-2">
								<a href="javascript:void(0)" class="btnmetode" data-prop="img{{$value['id']}}" id="{{$value['id']}}">
									<img src="{{ asset('assets/images/bank/'.$value['merchant'].'.png') }}" class="imgmethod methodlight" id="img{{$value['id']}}" width="80">
								</a>
							</div>
							@endforeach
						</div>

						<b class="text-dark">Virtual Account</b>
						<div class="row mt-2">
							@foreach(\App\Models\DepositMethod::where('payment','Virtual Account')->get() as $key => $value)
							<div class="col-1 mb-2">
								<a href="javascript:void(0)" class="btnmetode" data-prop="img{{$value['id']}}" id="{{$value['id']}}">
									<img src="{{ asset('assets/images/bank/'.$value['merchant'].'.png') }}" class="imgmethod methodlight" id="img{{$value['id']}}" width="80">
								</a>
							</div>
							@endforeach
						</div>

						<b class="text-dark">Retail</b>
						<div class="row mt-2">
							@foreach(\App\Models\DepositMethod::where('payment','Retail')->get() as $key => $value)
							<div class="col-4 mb-2">
								<a href="javascript:void(0)" class="btnmetode" data-prop="img{{$value['id']}}" id="{{$value['id']}}">
									<img src="{{ asset('assets/images/bank/'.$value['merchant'].'.png') }}" class="imgmethod methodlight" id="img{{$value['id']}}" width="80">
								</a>
							</div>
							@endforeach
						</div>
						<hr>
						<form method="post" action="{{ request()->url() }}" id="main_form">
							@csrf
							<input type="hidden" name="amount" id="amount" value="">
							<input type="hidden" name="deposit_method_id" id="metode" value="">
							<button type="submit" class="btn btn-primary btn-sm btn-block" id="proses">Proses</button>
						</form>

					</div>
				</div><!-- End Default Card -->
			</div>
	</div>
</div>
@endsection
@section('script')
<script>
	

function reset_buttons(value = 0) {
	if (value == 0) {
		$('#proses').attr('disabled', 'true');
		$('#proses').text('');
		$('#proses').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
		$('button[type="reset"]').hide();
	} else {
		$('#proses').removeAttr('disabled');
		$('#proses').removeAttr('span');
		$('#proses').text('Submit');
		$('button[type="reset"]').show();
	}
}

$(document).ready(function() {
	$(".btnmetode").click(function(){
		$(".imgmethod").removeClass('method_active').addClass('methodlight');
		$("#"+$(this).attr("data-prop")).addClass('method_active');
		$("#metode").val($(this).attr("id"));
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
	$("#nominal").keyup(function() { 
		$("#amount").val(numeric($(this).val()));
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
				console.log("bb");
				reset_buttons(0);
				$(document).find('small.text-danger').text('');
				$(document).find('input').removeClass('is-invalid');
			},
			success: function(data) {
				reset_buttons(1);
				if (data.status == false) {
					if (data.type == 'validation') {
						/*$.each(data.message, function(prefix, val) {
							$("input[name="+prefix+"]").addClass('is-invalid');
							$('small.'+prefix+'_error').text(val[0]);
						});*/
						swal.fire("Gagal!", "Silahkan Isi Data Dengan Benar", "error");
					}
					if (data.type == 'alert') {
						swal.fire("Gagal!", data.message, "error");
					}
				} else {
					/*
					swal.fire("Berhasil!", data.message, "success").then(function () {
						window.location.reload();
					});
					*/
					window.location.href = data.target;
				}
				
			},
			error:function() {
				swal.fire("Gagal!", "Terjadi kesalahan..", "error");
				reset_buttons(1);
			},
		});
		e.preventDefault();
	});
});

	
</script>
@endsection
