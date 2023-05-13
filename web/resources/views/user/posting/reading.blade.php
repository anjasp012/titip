@extends('user.layouts.app')
@section('content')
<div class="col-md-9">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-row p-3 rowquest">
                <div class="d-flex justify-content-between">
                    <div class="me-3 mt-1">
                        @if ($target->user->avatar !="")
                            <img src="{{ url('public/avatar/'.$target->user->avatar)}}" width="40" height="40" class="rounded-circle">
                        @else
                            <img src="https://www.gravatar.com/avatar/{{ md5($target->user->email)}}.jpg?s=40&d=monsterid" width="40" height="40" class="rounded-circle">
                        @endif
                        
                    </div>
                    <div class="namepost d-grid">
                        <span class="post_name">{{$target->user->username}}</span>
                        <span class="post_date">{{time_elapsed_string($target->created_at)}} </span>
                    </div>
                </div>
                <div class="ms-auto">
                    @if (Auth::check() == true)
                    <a href="javascript:void(0)" class="btn btn-sm btn-warning laporkan" data-id="{{$target->id}}" title="Laporkan Postingan"><i class="bi bi-exclamation-circle"></i></a>
                    @endif
                </div>
            </div>
            
            <div class="mt-4">
                {!!$target->konten!!}
            </div>
            @if (@$target->gambar !="")
                <img src="{{ url('public/images/'.@$target->gambar)}}" class="img-fluid">
            @endif
            @if (Auth::check() == true)
            <div>
                @if ($target->user->id !=Auth::user()->id && $target->jawab=="0")
                <a href="{{url('posting/answer/'.$target->id)}}" class="btn rounded-pill btn-sm btn-outline-dark mt-4 post_btn">Tambahkan Jawaban <span>+{{$target->point}}</span></a>
                @endif
            </div>
            @endif
        </div>
        <hr>
        <div class="text-center">
 <!-- Go to www.addthis.com/dashboard to customize your tools -->
 <div class="addthis_inline_share_toolbox_8lb5"></div>
        </div>
               
            
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="head_jawab">JAWABAN</h5>
        </div>
        <div class="card-body">
        @foreach ($answer as $item)
            <div class="d-flex flex-row p-3">
                <div class="me-3 mt-1">
                    @if (@$item->user->avatar !="")
                        <img src="{{ url('public/avatar/'.@$item->user->avatar)}}" width="40" height="40" class="rounded-circle">
                    @else
                        <img src="https://www.gravatar.com/avatar/{{ md5($item->user->email)}}.jpg?s=40&d=monsterid" width="40" height="40" class="rounded-circle">
                    @endif
                </div>
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="namepost d-grid">
                        <span class="post_name">{{@$item->user->username}}</span>
                        <span class="post_date">{{time_elapsed_string($item->created_at)}} </span>
                    </div>
                    @if (Auth::check() == true)
                        @if ($target->user->id==Auth::user()->id && $target->jawab=="0")
                        <button class="btn btn-primary btn-sm rounded-pill btnterbaik" data-item="{{$item->id}}">
                            <span> + Jawaban Terbaik</span>
                        </button>
                        @endif
                    @endif
                   
                </div>
            </div>
            <div class="">
                {!!$item->jawaban!!}
                
                @if (@$item->gambar !="")
                    <br>
                    <img src="{{ url('public/images/'.@$item->gambar)}}" class="img-fluid">
                @endif
                @if (Auth::check() == true)
                    <a href="javasript:void(0)" data-id="{{$item->id}}" class="balas">Balas</a>
                @endif
                @php
                    $blsan = \App\Models\PostingReplay::where('answer_id',$item->id)->get();
                @endphp
                @if ($blsan)
                    @foreach ($blsan as $item)
                        
                    <div class="d-flex flex-row p-3">
                            <div class="me-3 mt-1">
                                @if (@$item->user->avatar !="")
                                    <img src="{{ url('public/avatar/'.@$item->user->avatar)}}" width="30" height="30" class="rounded-circle">
                                @else
                                    <img src="https://www.gravatar.com/avatar/{{ md5($item->user->email)}}.jpg?s=40&d=monsterid" width="30" height="30" class="rounded-circle">
                                @endif
                            </div>
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="namepost d-grid">
                                    <span class="post_name">{{@$item->user->username}}</span>
                                    <span class="post_date">{{@$item->jawaban}} </span>
                                </div>
                                
                            
                            </div>
                        </div>
                        
                    @endforeach
                @endif
            </div>
        
        <hr>

        @endforeach
</div>
</div>
</div>
@include('user.layouts.right')

<div id="balasmodal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <form method="post" action="{{ url('posting/replay') }}" id="main_form">
                <input type="hidden" name="idjawab" id="idjawab">
                @csrf
                <div class="modal-body">                
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea name="message" class="form-control"></textarea>
                    </div>                    
                </div>
                <div class="modal-footer">                    
                    <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm btn-block">Balas</button>
                </div>
            </form>
        </div>
    </div>
</div>    
@endsection

@section('script')
<script>
     $('.balas').click(function(){
        $("#idjawab").val($(this).attr("data-id"));
        $('#balasmodal').modal('show');
    });
    $('.laporkan').click(function(){
        var items = $(this).attr("data-id") ;
        Swal.fire({
                title: 'Laporkan Postingan Ini ?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
        }).then((result) => {
           if(result.value){
            laporkan(items);
           }
        });

    });
    function laporkan(id)
    {
        var postForm = { 
            'id'     :id 
        };

        $.ajax
        ({ 
            url: '/posting/laporkan',
            data: JSON.stringify({"id":id}),
            method:'post',
            data:postForm,
            dataType  : 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data) {
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

    }
    $('.btnterbaik').click(function(){
        var items = $(this).attr("data-item") ;
        
        var postForm = { 
            'id'     :items 
        };

        $.ajax
        ({ 
            url: '<?=request()->url()?>',
            data: JSON.stringify({"itemsx":items}),
            method:'post',
            data:postForm,
            dataType  : 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data) {
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
    

$(function() {
    
    function reset_button(value = 0) {
        if (value == 0) {
            $('button[type="submit"]').attr('disabled', 'true');
            $('button[type="submit"]').text('');
            $('button[type="submit"]').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
        } else {
            $('button[type="submit"]').removeAttr('disabled');
            $('button[type="submit"]').removeAttr('span');
            $('button[type="submit"]').text('Balas');
        }
    }

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
                    if (data.type == 'validation') {
                        swal.fire("Gagal!", "Silahkan Isi Data Dengan Benar", "error");
                    }
                    if (data.type == 'alert') {
                        swal.fire("Gagal!", data.message, "error");
                    }
                } else {
                    swal.fire("Berhasil!", data.message, "success").then(function () {
                        window.location.reload();
                    });
                    
                }
                reset_button(1);
            },
            error:function() {
                swal.fire("Gagal!", "Terjadi kesalahan..", "error");
                reset_buttons(1);
            },
        });
        e.preventDefault();
    });
});
    </script>
    <!-- Go to www.addthis.com/dashboard to customize your tools -->
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-625abcf369116825"></script>
@endsection
