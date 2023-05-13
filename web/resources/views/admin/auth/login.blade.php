@extends('admin.auth.template')

@section('content')
<form method="post" action="{{ request()->url() }}" id="main_form">
    @csrf
    <div class="form-group">
        <label>Username <text class="text-danger">*</text></label>
        <input class="form-control" type="text" name="username" value="{{ old('username') }}">
        @error('username')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
        <small class="text-danger username_error"></small>
    </div>
    <div class="form-group">
        <label>Password <text class="text-danger">*</text></label>
        <input class="form-control" type="password" name="password">
        @error('password')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
        <small class="text-danger password_error"></small>
    </div>
    <div class="mt-4 text-center">
        <button class="btn btn-primary btn-block" type="submit">Masuk</button>
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
                            window.location = "<?= url('admin') ?>";
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
