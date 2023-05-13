@extends('user.layouts.app')
@section('content')

    <div class="col-md-3 mb-3">
        @include('user.layouts.left_profile')
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-body pb-5">
                <h5 class="card-title">{{$project->title}}</h5>

                <div class="news">
                    {!!$project->deskripsi!!}
                    
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <div class="row">
                            <input type="hidden" name="id" id="target_id" value="{{$target->id}}">
                            <div class="col-md-6">
                                <b>Published Budget:</b> {{rupiah($project->budget_from)}} - {{rupiah($project->budget_to)}}<br>
                                <b>Published Date:</b> {{$project->created_at}}<br>
                                <b>Select Deadline:</b> {{$project->estimasi}} Day<br>
                                
                                
                            </div>
                            <div class="col-md-6">
                                <b>Project Status:</b> {{$project->project_status}}<br>
                                <b>Bid Count:</b> {{$project->bidcount}}<br>
                                <b>Finish Days:</b> {{$project->estimasi}}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                    @if (($target->status=="Approve" or $target->status=="Finish") && $project->user->id == Auth::user()->id)
                        <a class="btn btn-primary w-100 btn-sm finish" href="javascript:void(0)" data-id="{{$target->id}}"><i class="ri-checkbox-circle-line"></i> Finish</a>&nbsp;
                        <button type="button" class="btn btn-warning btnsubmit btn-sm fsmall w-100" data-bs-toggle="modal" data-bs-target="#basicModal">Gugatan</button>
                        @endif
                    @if ($target->status=="Approve" && $target->user->id == Auth::user()->id)
                        {{--<button type="button" class="btn btn-primary btnsubmit btn-sm fsmall w-100" id="ufinish">Finish</button>&nbsp;--}}
                        <button type="button" class="btn btn-warning btnsubmit btn-sm fsmall w-100" data-bs-toggle="modal" data-bs-target="#basicModal">Gugatan</button>
                    @endif
                    </div>
                    <hr>
                    
                    <div class="news">
                        @foreach ($chat as $item)
                        @if ($item->tipe=="0")
                            <div class="post-item clearfix pb-2">
                                @if (@$item->user->avatar !="")
                                    <img src="{{ url('public/avatar/'.@$item->user->avatar)}}" >
                                @else
                                    <img src="https://www.gravatar.com/avatar/{{ md5(@$item->user->email)}}.jpg?s=100&d=monsterid">
                                @endif

                                <h4><a href="{{url('project/detail/'.$item->id)}}">{{$item->title}}</a></h4>
                                <div class="konten">
                                    <div>
                                        <span class="txt-navi"><b>{{$item->user->username}}</b></span><br>
                                        <span>{{formatTgl($item->created_at)}}</span>
                                    </div>
                                    <div>
                                        {!!$item->message!!}
                                    </div>
                                    
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning post-item clearfix pb-2">
                                <div>
                                    <span class="txt-navi"><b>GUGATAN DARI {{$item->user->username}} [ {{$item->status}} ]</b></span><br>
                                    <span>{{formatTgl($item->created_at)}}</span>
                                </div>
                                <div>
                                    {!!$item->message!!}
                                </div>
                            </div>
                        @endif
                        
                        <hr>
                        @endforeach
                    </div>
                    

                </div>
            </div>
        </div><!-- End News & Updates -->
        @if ($target->status=="Approve")
        <div class="card">
            <div class="card-body">
                <div class="mt-4">
                    <form method="post" action="{{ request()->url() }}" id="main_form">
                        @csrf
                        <div class="form-group">
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
                        <button type="submit" class="btn btn-success btnsubmit btn-sm"><i class="ri-mail-send-fill"></i>  Send Message</button>
                        
                    </form>
                    
                </div>
            </div>
        </div>
        @endif
        
        

    </div>

    <div class="modal fade" id="basicModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Gugatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <textarea name="gugatan" class="form-control" id="gugatan"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendgugatan">Send</button>
            </div>
            </div>
        </div>
    </div><!-- End Basic Modal-->

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

    
    
});
</script>
@if ($project->user->id == Auth::user()->id)
<script>
$(document).ready(function() {
    $('.finish').click(function() {
        var id = $(this).data("id");
        Swal.fire({
            title: 'Apa anda yakin ?',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            }).then((result) => {
            console.log(result);
            if (result.value) {
            finish(id);
            } 
        })
	});

    function finish(id){
        $.ajax({
            url: '/project/finish',
            method: "POST",
            data: {"dataid": id},
            dataType:'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data) {
                
                if (data.status == false) {
                    
                    if (data.type == 'alert') {
                        swal.fire("Gagal!", data.message, "error");
                    }
                } else {
                    swal.fire("Berhasil!", data.message, "success").then(function () {
                        location.reload();
                    });
                }
            },
            error:function() {
                swal.fire("Gagal!", "Terjadi kesalahan.", "error");
            },
        });
    }
});
</script>
@endif

<script>
$(document).ready(function() {
    $('#sendgugatan').click(function() {
        var id = $("#target_id").val();
        var msg = $("#gugatan").val();
        Swal.fire({
            title: 'Apa anda yakin akan mengirim gugatan?',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            }).then((result) => {
            console.log(result);
            if (result.value) {
                gugatan(id,msg);
            } 
        })
	});
    function gugatan(id,msg){
        var host = window.location.protocol + "//" + window.location.host;
        $.ajax({
            url: host+'/project/gugatan',
            method: "POST",
            data: {"dataid": id,"msg":msg},
            dataType:'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data) {
                
                if (data.status == false) {
                    
                    if (data.type == 'alert') {
                        swal.fire("Gagal!", data.message, "error");
                    }
                } else {
                    swal.fire("Berhasil!", data.message, "success").then(function () {
                        location.reload();
                    });
                }
            },
            error:function() {
                swal.fire("Gagal!", "Terjadi kesalahan.", "error");
            },
        });
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
							location.reload();
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
