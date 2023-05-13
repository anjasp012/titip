@extends('user.auth.template')

@section('content')

<div class="d-flex justify-content-center py-4">
	<a href="{{url("/>")}}" class="logo d-flex align-items-center w-auto">
	  <img src="assets/img/logo.png" alt="">
	  <span class="d-none d-lg-block">Titiptugas</span>
	</a>
  </div><!-- End Logo -->

  <div class="card mb-3">

	<div class="card-body">

	  <div class="pt-4 pb-2">
		<h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
		<p class="text-center small">Enter your username & password to login</p>
	  </div>


	  <form method="post" action="{{ request()->url() }}" id="main_form" class="row g-3 needs-validation" novalidate>
		@csrf
		<div class="col-12">
		  <label for="yourUsername" class="form-label">Nomer Hp</label>
		  
			<input class="form-control" type="text" name="phone_number" value="{{ old('phone_number') }}">
			@error('username')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
			<small class="text-danger username_error"></small>
		
		 
		</div>

		<div class="col-12">
		  <label for="yourPassword" class="form-label">Password</label>
		  <input class="form-control" type="password" name="password">
			@error('password')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
			<small class="text-danger password_error"></small>
		</div>

		<div class="col-12">
			 <div class="form-group">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="remember" name="remember" value="1"> 
					<label class="custom-control-label font-weight-normal" for="remember">
						Tetap masuk selama 30 hari
					</label>
				</div>
			</div>
		</div>
		<div class="col-12">
		  <button class="btn btn-primary w-100" type="submit">Login</button>
		</div>
		<div class="col-12">
		  
          <a href="{{ url('auth/facebook') }}" class="btn btn-primary w-100" class="btn btn-facebook"><i class="fa fa-facebook"></i>Login With Facebook</a>
          
          <p class="small mt-3">Don't have account? <a href="{{url('auth/register')}}">Register</a></p>
		</div>
	  </form>

	</div>
  </div>
@endsection

@section('script')
<script>
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
                            window.location = "<?= url('/') ?>";
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

