@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <td align="center" colspan="2">
                                <strong>INFORMASI PROJECT</strong>
                            </td>
                        </tr>
                        <tr>
                            <th width="30%">ID</th>
                            <td>{{ $project->id }}</td>
                        </tr>
                        <tr>
                            <th width="30%">DIBUAT</th>
                            <td>
                                {{ \Carbon\Carbon::parse($project->created_at)->translatedFormat('d/m/Y H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <th width="30%">DIPERBARUI</th>
                            <td>
                                {{ \Carbon\Carbon::parse($project->updated_at)->translatedFormat('d/m/Y H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <th>PENGGUNA</th>
                            <td>
                                @if ($project->user == true)
                                {{$project->user->username }} ({{$project->user->full_name }})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>TITLE</th>
                            <td>{{ $project->title }}</td>
                        </tr>
                        
                        <tr>
                            <th>BUDGET</th>
                            <td>{{ number_format($project->budget_from,0,',','.') }} - {{ number_format($project->budget_to,0,',','.') }}</td>
                        </tr>
                        
                        <tr>
                            <th>STATUS</th>
                            <td>{!! getstatus($project->project_status) !!}</td>
                        </tr>
                        
                        
                        
                        <tr>
                            <th colspan="2">DESKRIPSI</th>
                        </tr>
                        <tr>
                            <th colspan="2">{!! $project->deskripsi !!}</th>
                        </tr>
                    </table>
                   
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td align="center" colspan="2">
                                        <strong>PROJECT OWNER</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="30%">USERNAME</th>
                                    <td>{{ $tergugat->username }}</td>
                                </tr>
                                <tr>
                                    <th width="30%">EMAIL</th>
                                    <td>{{ $tergugat->email }}</td>
                                </tr>
                                <tr>
                                    <th width="30%">PHONE NUMBER</th>
                                    <td>{{ $tergugat->phone_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <td align="center" colspan="2">
                                    <strong>BIDER</strong>
                                </td>
                            </tr>
                            <tr>
                                <th width="30%">USERNAME</th>
                                <td>{{ $penggugat->username }}</td>
                            </tr>
                            <tr>
                                <th width="30%">EMAIL</th>
                                <td>{{ $penggugat->email }}</td>
                            </tr>
                            <tr>
                                <th width="30%">PHONE NUMBER</th>
                                <td>{{ $penggugat->phone_number }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div>
                    @if ($target->status=="Gugatan")
                    <button type="button" class="btn btn-primary" id="finish" data-id="{{$target->id}}">Finish</button>
                    <button type="button" class="btn btn-danger" id="cancel" data-id="{{$target->id}}">Cancel</button>
                    @endif
                    
                </div>
                <br>
                <div>
                    @foreach ($bidmsg as $item)
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
                                    <span class="txt-navi"><b>GUGATAN [ {{$item->status}} ]</b></span><br>
                                    <span>{{formatTgl($item->created_at)}}</span>
                                </div>
                                <div>
                                    {!!$item->message!!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
			</div>
		</div>

        
	</div>
</div>
<script>
    $('#cancel').click(function(){
        var items = $(this).attr("data-id") ;
        Swal.fire({
                title: 'Cancel Project',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
        }).then((result) => {
           if(result.value){
            reject(items);
           }
        });
    });

    $('#finish').click(function(){
        var items = $(this).attr("data-id") ;
        Swal.fire({
                title: 'Finish Project',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
        }).then((result) => {
           if(result.value){
            approve(items);
           }
        });
    });

    function reject(id)
    {
        var note = $("#rejectnote").val();
        var postForm = { 
            'id':id,
            'note':note 
        };

        $.ajax
        ({ 
            url: '/admin/project/cancel/'+id,
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
                        location.reload();
                    });
                }
            },
            error:function() {
                swal.fire("Gagal!", "Terjadi kesalahan.", "error");
            },
        });

    }

    function approve(id)
    {
        var note = $("#rejectnote").val();
        var postForm = { 
            'id':id,
            'note':note
        };

        $.ajax
        ({ 
            url: '/admin/project/finish/'+id,
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
                        location.reload();
                    });
                }
            },
            error:function() {
                swal.fire("Gagal!", "Terjadi kesalahan.", "error");
            },
        });

    }
</script>
@endsection
