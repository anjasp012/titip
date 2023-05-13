@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')

    <div class="col-md-3 mb-3">
        @include('user.layouts.left_profile')
    </div>
    <div class="col-md-9">
        <div class="card">
                <div class="card-body pb-5">
                    <h5 class="card-title">{{$target->title}}</h5>
                    <div class="news">
                        <form method="post" action="{{ request()->url() }}" id="main_form">
                            @csrf
                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">Project Title</label>
                            <div class="col-sm-9">
                            {{$target->title}}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">Description</label>
                            <div class="col-sm-9">
                            {!!$target->deskripsi!!}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputPassword3" class="col-sm-3 col-form-label">Published Budget</label>
                            <div class="col-sm-9">
                            {{rupiah($target->budget_from)}} - {{rupiah($target->budget_to)}}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">Amount</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control autonumeric-currency" id="inputText" name="amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">Message</label>
                            <div class="col-sm-9">
                                <textarea class="form-control custom-text-editor" name="desc" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="inputEmail3" class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary btn-sm">Place New Bid</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
        </div><!-- End News & Updates -->

    </div>

@endsection


@section('script')
<script>
$(document).ready(function() {
    $('.custom-text-editor').summernote({
        height: 230,
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript']],
		],
		codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*?>/gi,
    });
    $('.autonumeric-currency').autoNumeric('init', {
		mDec  : '0',
		aSep  : '.',
		aDec  : ',',
		aSign : 'Rp ',
	});
	
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
						window.location.replace("/project/bid/placed");
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
