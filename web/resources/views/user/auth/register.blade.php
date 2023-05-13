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
    <h5 class="card-title text-center pb-0 fs-4">Create New Account</h5>
    </div>
	<form method="post" action="{{ request()->url() }}" id="main_form" class="row g-3 needs-validation" novalidate>
		@csrf
		<div class="col-12">
            <label>Nama Lengkap <text class="text-danger">*</text></label>
            <input class="form-control" type="text" name="full_name" value="{{ old('full_name') }}">
            <small class="text-danger full_name_error"></small>
		</div>
        <div class="col-12">
            <label>Nomor Telepon <text class="text-danger">*</text></label>
            <input class="form-control" type="number" name="phone_number" value="{{ old('phone_number') }}">
            <small class="text-danger phone_number_error"></small>
        </div>
        
        <div class="col-12">
            <label>Email <text class="text-danger">*</text></label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}">
            <small class="text-danger email_error"></small>
        </div>
        <div class="col-12">
            <label>Username <text class="text-danger">*</text></label>
            <input class="form-control" type="text" name="username" value="{{ old('username') }}">
            <small class="text-danger username_error"></small>
        </div>
        <div class="col-12">
            <label>Password <text class="text-danger">*</text></label>
            <input class="form-control" type="password" name="password">
            <small class="text-danger password_error"></small>
        </div>
        <div class="col-12">
            <label>Konfirmasi Password <text class="text-danger">*</text></label>
            <input class="form-control" type="password" name="confirm_password">
            <small class="text-danger confirm_password_error"></small>
        </div>
        <div class="col-12">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="approval" name="approval" value="1"> 
                <label class="custom-control-label font-weight-normal" for="approval">
                    Saya telah menyetujui <a href="javascript:void(0);" class="text-primary">Ketentuan Layanan</a>
                </label>
            </div>
            <small class="text-danger approval_error"></small>
        </div>
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit">Register</button>
        </div>
		<div class="col-12">
            <p class="small mt-3">have account? <a href="{{url('auth/login')}}">Login</a></p>
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
            $('button[type="submit"]').text('Daftar');
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
                            window.location = "<?= url('auth/otp') ?>";
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
<div class="row mt-4">
    <div class="col-sm-12 text-center">
        <p class="{{ $text_class }}-50 mb-0">Sudah mempunyai akun? <a href="{{ url('auth/login') }}" class="{{ $text_class }}-50 ml-1"><b>Masuk</b></a></p>
    </div>
</div>
@endsection
