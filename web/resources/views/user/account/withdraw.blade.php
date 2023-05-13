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
                        <h5 class="card-title">Nominal Penarikan</h5>
                        <div class="alert alert-warning">
                            Penarikan Minimum Rp.{{number_format(website_config('bonus_point')->minwd,0,',','.')}}, setiap penarikan akan dikenakan biaya admin sebesar Rp.10.000. setiap penarikan akan di proses 2x24 JAM
                        </div>
						<form method="post" action="{{ request()->url() }}" id="main_form">
                            @csrf
						<input type="text" class="form-control autonumeric-currency" name="nominal" id="nominal">
						
                        <div class="my-3">
							<b class="text-dark">Bank</b>
						</div>
                        <select class="form-control" name="bank">
                            <option value="BCA">BCA</option>
                            <option value="BRI">BRI</option>
                            <option value="MANDIRI">MANDIRI</option>
                            <option value="BNI">BNI</option>
                            <option value="BTN">BTN</option>
                            <option value="CIBC">CIBC</option>
                            <option value="NISP">NISP</option>
                            <option value="PANIN">PANIN</option>
                        </select>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="my-3">
                                    <b class="text-dark">No Rekening</b>
                                </div>
                                <input type="text" name="rekening" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <div class="my-3">
                                    <b class="text-dark">Nama Rekening</b>
                                </div>
                                <input type="text" name="nama" class="form-control">
                            </div>
                        </div>
                       

						<hr>
						<button type="submit" class="btn btn-primary btn-sm btn-block">Proses</button>
						</form>

					</div>
				</div><!-- End Default Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Withdraw History</h5>
                        <table class="table table-stripe datatable">
                            <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Bank</th>
                                <th scope="col" class="text-center">No.Rekening</th>
                                <th scope="col" class="text-center">Nama</th>
								<th scope="col" class="text-center">Amount</th>
                                <th scope="col" class="text-center">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($list as $item)
                                <tr>
                                    <th scope="row">{{formatTgl($item->created_at)}}</th>
                                    <td>{{$item->bank}}</td>
                                    <td class="text-center">{{$item->rekening}}</td>
                                    <td class="text-center">{{$item->nama}}</td>
									<td class="text-center">{{rupiah($item->amount)}}</td>
                                    <td class="text-center">
                                        {!!getStatus($item->status)!!}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>
	</div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
	
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
	function reset_button(value = 0) {
        if (value == 0) {
            $('button[type="submit"]').attr('disabled', 'true');
            $('button[type="submit"]').text('');
            $('button[type="submit"]').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
        } else {
            $('button[type="submit"]').removeAttr('disabled');
            $('button[type="submit"]').removeAttr('span');
            $('button[type="submit"]').text('Submit');
        }
    }
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
					reset_buttons(1);
					if (data.status == false) {
						if (data.type == 'validation') {
							swal.fire("Gagal!", "Silahkan Isi Data Dengan Benar", "error");
						}
						if (data.type == 'alert') {
							swal.fire("Gagal!", data.message, "error");
						}
					} else {
						swal.fire("Berhasil!", data.message, "success").then(function () {
							window.location.reload();
						});
						
					}
					reset_buttons(1);
				},
				error:function() {
					swal.fire("Gagal!", "Terjadi kesalahan..", "error");
					reset_buttons(1);
				},
			});
			e.preventDefault();
		});
	});

	function reset_buttons(value = 0) {
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
