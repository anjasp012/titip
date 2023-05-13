@extends('user.layouts.app')
@section('content')
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <div class="mt-4">
                <h4>Pertanyaan</h4>
                {!!$target->konten!!}
            </div>
        </div>
    </div>
</div>
<div class="col-md-8">
    <div class="card">
        <div class="card-body">
            <div class="mt-4">
                <form method="post" action="{{ request()->url() }}" id="main_form">
                    @csrf
                    <div class="form-group">
                        <label>Pesan <text class="text-danger">*</text></label>
                        <textarea class="form-control custom-text-editor" name="jawaban" rows="5">{{ old('jawaban') }}</textarea>
                        <small class="text-danger jawaban_error"></small>
						<div class="row">
							<div class="col-md-6">
								<a href="javascript:void(0)" id="uploadIcon" class="btn btn-dark btniconattacth"><i class="ri-image-add-line iattc"></i></a>
							</div>
							<div class="col-md-6 mt-2">
								<div class="d-flex flexfile">
									<div class="flex-shrink-0">
										<img id="blah" src="#" width="50"/>
									</div>
									<div class="flex-grow-1 ms-3">
										<span class="filenames"></span>
									</div>
								</div>
								
							</div>
						</div>
                    </div>
					<input type="file" value="upload" id="uploadFile" name="gambar" class="uploadFile"  accept="image/*" />
                    <hr />
					<div class="text-right">
                        <button type="submit" class="btn btn-success rounded-pill btnsubmit"><i class="fa fa-check"></i> Tambahkan Jawaban Anda</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
	$('#blah').hide();
    $("#uploadIcon").click(function(){
        $("#uploadFile").trigger('click');
    });
    $("#uploadFile").change(function(){
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(".filenames").html(input.value.replace(/^.*[\\\/]/, ''))
                $('#blah').attr('src', e.target.result);
                $('#blah').show();
            }
            reader.readAsDataURL(input.files[0]);
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
            $('button[type="submit"]').text('Submit');
        }
    }

    $('.custom-text-editor').summernote({
        height: 230,
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript']],
		],
		codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*?>/gi,
    });
	$('#filebutton').click(function() {
		$('.file-upload-input').trigger( 'click' );
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
						
						if (data.type == 'alert') {
							swal.fire("Gagal!", data.message, "error");
						}
					} else {
						swal.fire("Berhasil!", data.message, "success").then(function () {
							window.location.replace("/posting/read/<?=$target->id?>");
						});
					}
				},
				error:function() {
					swal.fire("Gagal!", "Terjadi kesalahan.", "error");
				},
			});
		});
	});

});
</script>
@endsection
