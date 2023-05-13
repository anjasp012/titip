@extends('user.layouts.app')
@section('content')

    <div class="col-md-3 mb-3">
        @include('user.layouts.left_profile')
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-body pb-5">
                <h5 class="card-title">{{$target->title}}</h5>

                <div class="news">
                    {!!$target->deskripsi!!}
                    
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <div class="row">
                            <div class="col-md-6">
                                <b>Published Budget:</b> {{rupiah($target->budget_from)}} - {{rupiah($target->budget_to)}}<br>
                                <b>Published Date:</b> {{$target->created_at}}<br>
                                <b>Select Deadline:</b> {{$target->estimasi}} Day<br>
                                
                                
                            </div>
                            <div class="col-md-6">
                                <b>Project Status:</b> {!!getstatus($target->project_status)!!}<br>
                                <b>Bid Count:</b> {{$target->bidcount}}<br>
                                <b>Finish Days:</b> {{$target->estimasi}}
                            </div>
                        </div>
                    </div>    
                    <hr>
                    
                    <div class="news">
                        @foreach ($projectbid as $item)
                       
                        <div class="post-item clearfix pb-2">
                            @if (@$item->user->avatar !="")
                                <img src="{{ url('public/avatar/'.@$item->user->avatar)}}" >
                            @else
                                <img src="https://www.gravatar.com/avatar/{{ md5(@$item->user->email)}}.jpg?s=100&d=monsterid">
                            @endif

                            <div class="konten">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href=""><b>{{$item->user->username}}</b></a>
                                    </div>
                                    <div class="ml-auto">
                                        @if ($item->status=="Active")
                                        <a class="btn btn-outline-success btn-sm fsmall" href="{{url("project/show_conversation/".$item->id)}}"><i class="bi bi-chat-left-text me-1"></i> Show Conversation</a>
                                        <a class="btn btn-sm btn-outline-secondary approvebid fsmall" data-id="{{$item->id}}"><i class="ri-checkbox-line"></i> Accecpt Bid</a>
                                        @endif

                                        @if ($item->status=="Approve")
                                        <a class="btn btn-outline-success btn-sm fsmall" href="{{url("project/show_conversation/".$item->id)}}"><i class="bi bi-chat-left-text me-1"></i> Show Conversation</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <div>
                                        <b><span class="txt-navi">Bid Amount</span></b><br>
                                        {{rupiah($item->amount)}}
                                    </div>
                                    <div>
                                        <b class="txt-navi">Working Projects</b><br>
                                        {{$item->user->working_project}}
                                    </div>
                                    <div>
                                        <b class="txt-navi">Current Projects</b><br>
                                        {{$item->user->current_project}}
                                    </div>
                                </div>
                                <hr>
                                <div>
                                    @php 
                                    
                                    $msg = \App\Models\ProjectBidMessage::where("user_id", $item->user->id)->where("project_id", $target->id)->first();
                                    @endphp
                                    {{strip_tags(@$msg->message)}}
                                </div>
                                
                            </div>
                        </div>
                        
                        <hr>
                        @endforeach
                    </div>
                    

                </div>
            </div>
        </div><!-- End News & Updates -->
        
    </div>

@endsection


@section('script')
<script>
$(document).ready(function() {
    $(".approvebid").click(function(){
        //alert($(this).data("id"));
        var id = $(this).data("id");
        Swal.fire({
            title: 'Apa anda yakin ?',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            }).then((result) => {
            console.log(result);
            if (result.value) {
               approves(id);
            } 
        })
    });

    function approves(id){
        $.ajax({
            url: '/project/approve',
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
@endsection
