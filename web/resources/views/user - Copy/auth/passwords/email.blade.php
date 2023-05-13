@extends('user.auth.template')

@section('content')
<form method="post" action="{{ request()->url() }}" id="main_form">
    @csrf
    <div class="form-group">
        <label>Email <text class="text-danger">*</text></label>
        <input class="form-control" type="email" name="email" value="{{ old('email') }}">
        <small class="text-danger email_error"></small>
    </div>
    <div class="mt-4 text-center">
        <button class="btn btn-primary btn-block" type="submit">Atur Ulang Kata Sandi</button>
    </div>
</form>
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
            $('button[type="submit"]').text('Atur Ulang Kata Sandi<');
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
                        swal.fire("Berhasil!", data.message, "success");
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
    <div class="col-sm-12 text-center {{ $text_class }}">
        <p class="{{ $text_class }}-50 mb-0"><a href="{{ url('auth/login') }}" class="{{ $text_class }}-50 ml-1"><b>Masuk</b></a> - <a href="{{ url('auth/register') }}" class="{{ $text_class }}-50 ml-1"><b>Daftar</b></a></p>
    </div>
</div>
@else
<div class="row mt-3">
    <div class="col-sm-12 text-center {{ $text_class }}">
        <p class="{{ $text_class }}-50 mb-0"><a href="{{ url('auth/login') }}" class="{{ $text_class }}-50 ml-1"><b>Masuk</b></a></p>
    </div>
</div>
@endif
@endsection
