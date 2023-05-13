    <form method="post" action="{{ request()->url() }}" id="main_form">
    @csrf
        <div class="form-group">
            
            {{--<label>Pertanyaan <text class="text-danger">*</text></label>--}}
            <textarea class="form-control custom-text-editor" name="message" rows="5">{{ old('message') }}</textarea>
            <small class="text-danger message_error"></small>
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

        <div class="text-right mt-4 d-flex">
            {{--<label>Pertanyaan <text class="text-danger">*</text></label>--}}
            <select class="form-control mx-1" name="category">
                @foreach ($cat as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>

            <select class="form-control mx-1" name="point">
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
            <input type="file" value="upload" id="uploadFile" name="gambar" class="uploadFile"  accept="image/*" />
        </div>
    </form>
    <div class="text-right mt-4">
        <button type="button" class="btn btn-primary rounded-pill btnsubmit tanya"><i class="fa fa-check"></i> Tanyakan Pertanyaanmu</button>
    </div>


<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{ asset('assets/custom-form.js') }}"></script>
<script>
    
$(function () {
    $('#blah').hide();
    $("#uploadIcon").click(function(){
        console.log("asdf");
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

    $('.tanya').click(function() {
		postTanya();
	});

    function postTanya(){
        let myForm = document.getElementById('main_form');
        let formData = new FormData(myForm);

        $.ajax({
            url: $("#main_form").attr('action'),
            method: $("#main_form").attr('method'),
            data: formData,
            processData: false,
            dataType: 'json',
            contentType: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            beforeSend: function () {
                reset_button(0);
                $(document).find('small.text-danger').text('');
                $(document).find('input').removeClass('is-invalid');
            },
            success: function (data) {
                reset_button(1);
                if (data.status == false) {
                    if (data.type == 'validation') {
                        $.each(data.message, function (prefix, val) {
                            $("input[name=" + prefix + "]").addClass('is-invalid');
                            $('small.' + prefix + '_error').text(val[0]);
                        });
                    }
                    if (data.type == 'alert') {
                        swal.fire("Gagal!", data.message, "error");
                    }
                } else {
                    reset_button(1);
                    swal.fire("Berhasil!", data.message, "success").then(function () {
                        $("#modal").modal('hide');

                    });
                }
            },
            error: function () {
                swal.fire("Gagal!", "Terjadi kesalahan.", "error");
            },
        });
 
    }
    
});
</script>

