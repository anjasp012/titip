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
                    <h5 class="card-title">My Bids</h5>

                    <div class="news" id="profile-overview">
                        @foreach ($project as $item)
                        <div class="post-item clearfix pb-2">
                            <span  class="card-title">{{$item->project->title}}</span>
                            <br>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="col-lg-4 col-md-4 label "><b>Project Status</b>&nbsp;</div>
                                        <div class="col-lg-8 col-md-8"> : {!!getstatus($item->project->project_status)!!}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-lg-4 col-md-4 label "><b>Budget</b>&nbsp;</div>
                                        <div class="col-lg-8 col-md-8"> : Rp.{{rupiah($item->project->budget_from)}} -  Rp.{{rupiah($item->project->budget_to)}}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-lg-4 col-md-4 label "><b>Status</b>&nbsp;</div>
                                        <div class="col-lg-8 col-md-8"> : {!!getstatus($item->status)!!}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="col-lg-4 col-md-4 label "><b>Date</b>&nbsp;</div>
                                        <div class="col-lg-8 col-md-8"> : {{$item->created_at}}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-lg-4 col-md-4 label "><b>Amount</b>&nbsp;</div>
                                        <div class="col-lg-8 col-md-8"> : Rp.{{rupiah($item->amount)}}</div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            @php 
                            $msg = \App\Models\ProjectBidMessage::where("user_id", Auth::user()->id)->where("project_id", $item->project->id)->first();
                            @endphp
                            <div class=""><b>Message:</b>&nbsp;</div>
                            <div class="">{!!$msg->message!!}</div>

                            <div class="t-end mb-2">
                                @if ($item->status=="Active")
                                <a class="btn btn-warning btn-sm cancel fsmall" data-id="{{$item->project->id}}"><i class="bi bi-x-square me-1"></i> Cancel Bid</a>
                                @endif

                                @if ($item->status=="Approve")
                                <a class="btn btn-success btn-sm fsmall" href="{{url("project/show_conversation/".$item->id)}}"><i class="bi bi-chat-left-text me-1"></i> Open Project</a>
                                @endif
                            </div>
                            <hr>
                        </div>

                        @endforeach
                    </div>
                </div>
        </div><!-- End News & Updates -->

    </div>

@endsection
@section('script')
<script>
$(document).ready(function() {
    $(".cancel").click(function(){
        //alert($(this).data("id"));
        var id = $(this).data("id");
        Swal.fire({
            title: 'Apa anda yakin ?',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            }).then((result) => {
            console.log(result);
            if (result.value) {
               cancel(id);
            } 
        })
    });

    function cancel(id){
        $.ajax({
            url: '/project/bid-history',
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
