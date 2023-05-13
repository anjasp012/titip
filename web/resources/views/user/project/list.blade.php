@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')

    
    <div class="col-md-12">
        <div class="card recent-sales overflow-auto">
            <div class="card-body">
                <h5 class="card-title">My Project <span> List</span></h5>

                <table class="table table-stripe datatable">
                    <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Bid Count</th>
                        <th scope="col" class="text-center">Projest Status</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($project as $item)
                        <tr>
                            <th scope="row"><a href="{{url('project/detail/'.$item->id)}}">{{Str::words(strip_tags($item->title), '5')}}</a></th>
                            <td>{{$item->bidcount}}</td>
                            <td class="text-center">{!!getstatus($item->project_status)!!}</td>
                            <td class="text-center">{!!getstatus($item->projectbid->status)!!}</td>
                            <td class="text-center">
                                @if ($item->project_status=="Active")
                                    <a class="" href="{{url("project/show_bid/".$item->id)}}"><span class="badge border-success border-1 text-success"><i class="bi bi-chat-left-text me-1"></i> Show Bids</span></a>
                                    <a class="cancel" data-id="{{$item->id}}" href="javascript:void(0)"><span class="badge border-danger border-1 text-danger"><i class="bi bi-x-square me-1"></i> Cancel Project</span></a> 
                                @endif

                                @if ($item->project_status=="Start")
                                <a href="{{url("project/show_bid/".$item->id)}}"><span class="badge border-success border-1 text-success"><i class="bi bi-chat-left-text me-1"></i> Show Bids</span></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{--
        <div class="card">
                <div class="card-body pb-5">
                    <h5 class="card-title">My Project</h5>

                    <div class="news">
                        @foreach ($project as $item)
                        <div class="post-item clearfix pb-2">
                            @if (@$item->user->avatar !="")
                                <img src="{{ url('public/avatar/'.@$item->user->avatar)}}" >
                            @else
                                <img src="https://www.gravatar.com/avatar/{{ md5(@$item->user->email)}}.jpg?s=150&d=monsterid">
                            @endif
                            <div class="t-end mb-2">
                                @if ($item->project_status=="Active")
                                    <a class="btn btn-success btn-sm" href="{{url("project/show_bid/".$item->id)}}"><i class="bi bi-chat-left-text me-1"></i> Show Bids</a>
                                    <a class="btn btn-warning btn-sm cancel" data-id="{{$item->id}}"><i class="bi bi-x-square me-1"></i> Cancel Project</a> 
                                @endif

                                @if ($item->project_status=="Start")
                                <a class="btn btn-success btn-sm" href="{{url("project/show_bid/".$item->id)}}"><i class="bi bi-chat-left-text me-1"></i> Show Bids</a>
                                @endif
                            </div>

                            <h4><a href="{{url('project/detail/'.$item->id)}}">{{$item->title}}</a></h4>
                            <div class="konten">
                                {{strip_tags($item->deskripsi)}}
                                <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Published Budget:</b>{{rupiah($item->budget_from)}} - {{rupiah($item->budget_to)}}<br>
                                            <b>Published Date:</b> {{$item->created_at}}<br>
                                            <b>Select Deadline:</b> {{$item->estimasi}} Day<br>
                                            <b>Project Status:</b> {!!getstatus($item->project_status)!!}
                                           
                                        </div>
                                        <div class="col-md-6">
                                            <b>Bid Count:</b> {{$item->bidcount}}<br>
                                            <b>Finish Days:</b> {{$item->estimasi}}<br>
                                            <b>Status :</b> {!!getstatus($item->projectbid->status)!!}
                                        </div>
                                    </div>
                                </div>
                                @if ($item->project_status=="Reject")
                                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                    {{$item->reject_note}} <br>Klik Di sini Untuk Edit <a href="{{url('project/edit/'.$item->id)}}">[Edit]</a>
                                </div>
                                @endif
                            </div>

                    
                        </div>

                        @endforeach
                    </div>
                </div>
        </div><!-- End News & Updates -->
        --}}
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
            url: '/project/cancel',
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
