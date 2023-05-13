@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')


    <div class="col-md-3 mb-3">
        @include('user.layouts.left_profile')
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Create Project</h5>

                <form method="post" action="{{ request()->url() }}" id="main_form">
                    @csrf
                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-2 col-form-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" value="{{$target->title}}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control custom-text-editor" name="desc" rows="5">{{$target->deskripsi}}</textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label for="inputNumber" class="col-sm-2 col-form-label">Attachments</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="file" id="formFile" name="formFile">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-2 col-form-label">Finish day</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="finishday" value="{{$target->estimasi}}">
                        </div>
                    </div>

                   

                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-2 col-form-label">Budget Range</label>
                        <div class="col-sm-10">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control autonumeric-currency" placeholder="0" aria-label="From" name="budgetfrom" value="{{$target->budget_from}}">
                                <span class="input-group-text">-</span>
                                <input type="text" class="form-control autonumeric-currency" placeholder="0" aria-label="To" name="budgetto" value="{{$target->budget_to}}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label for="inputPassword" class="col-sm-2 col-form-label">Categories</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="kategori">
                                @foreach ($cat as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Publish</button>
                        </div>
                    </div>

                </form><!-- End General Form Elements -->

            </div>
          </div>
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
						swal.fire("Berhasil!", data.message, "success").then(function () {
						
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
