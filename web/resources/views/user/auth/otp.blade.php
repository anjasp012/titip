@extends('user.auth.template')

@section('content')
<link href="{{ asset('assets/titiptugas/css/otp.css')}}?v=<?= date("ymdhis") ?>" rel="stylesheet">
<div class="d-flex justify-content-center py-4">
	<a href="{{url("/>")}}" class="logo d-flex align-items-center w-auto">
	  <img src="assets/img/logo.png" alt="">
	  <span class="d-none d-lg-block">Titiptugas</span>
	</a>
  </div><!-- End Logo -->

  <div class="card mb-3">

	<div class="card-body">

	  <div class="pt-4 pb-2">
		<h5 class="card-title text-center pb-0 fs-4">Otp Code Whatsapp</h5>
		<p class="text-center small">Enter your otp code</p>
	  </div>


	  <form method="post" action="{{ request()->url() }}" id="main_form" class="row g-3 needs-validation digit-group" data-group-name="digits" data-autosubmit="false" autocomplete="off">
		@csrf
		<div class="col-12 userInput">
            <input type="text" id='ist' maxlength="1" onkeyup="clickEvent(this,'sec')" name="satu">
			<input type="text" id="sec" maxlength="1" onkeyup="clickEvent(this,'third')" name="dua">
			<input type="text" id="third" maxlength="1" onkeyup="clickEvent(this,'fourth')" name="tiga">
			<input type="text" id="fourth" maxlength="1" onkeyup="clickEvent(this,'fifth')" name="empat">
			<input type="text" id="fifth" maxlength="1" name="lima">
		</div>

		
		<div class="col-12">
		<button class="btn btn-primary w-100" type="submit">Verifikasi</button>
		</div>
		
	  </form>

	</div>
  </div>
@endsection

@section('script')
<script>
    function clickEvent(first,last){
        if(first.value.length){
            document.getElementById(last).focus();
        }
    }              
    function reset_button(value = 0) {
        if (value == 0) {
            $('button[type="submit"]').attr('disabled', 'true');
            $('button[type="submit"]').text('');
            $('button[type="submit"]').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
        } else {
            $('button[type="submit"]').removeAttr('disabled');
            $('button[type="submit"]').removeAttr('span');
            $('button[type="submit"]').text('Masuk');
        }
    }
    $(function() {
        $("#main_form").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:$(this).attr('action'),
                method:$(this).attr('method'),
                data:new FormData(this),
                processData:false,
                dataType:'json',
                contentType:false,
                beforeSend:function(){
                    reset_button(0);
                    $(document).find('small.text-danger').text('');
                    $(document).find('input').removeClass('is-invalid');
                },
                success:function(data){
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
                        $('#main_form')[0].reset();
                        swal.fire("Berhasil!", data.message, "success").then(function () {
                            window.location = "<?= url('/auth/login') ?>";
                        });
                    }
                },
                error:function() {
                    reset_button(1);
                    swal.fire("Gagal!", "Terjadi kesalahan.", "error");
                },
            });
        });
    });
</script>
@endsection

@section('another-page')
@php
if (website_config('template')->number == 'third-template') {
    $text_class = 'text-white';
} else {
    $text_class = 'text-dark';
}
@endphp
@if (Route::has('user.register'))
<div class="row mt-3">
    <div class="col-sm-12 text-center">
        <p class="{{ $text_class }}-50 mb-0">Belum mempunyai akun? <a href="{{ url('auth/register') }}" class="{{ $text_class }}-50 ml-1"><b>Daftar</b></a></p>
    </div>
</div>
@endif
@endsection

