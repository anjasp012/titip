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
                            <td>{{ $target->id }}</td>
                        </tr>
                        <tr>
                            <th width="30%">DIBUAT</th>
                            <td>
                                {{ \Carbon\Carbon::parse($target->created_at)->translatedFormat('d/m/Y H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <th width="30%">DIPERBARUI</th>
                            <td>
                                {{ \Carbon\Carbon::parse($target->updated_at)->translatedFormat('d/m/Y H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <th>PENGGUNA</th>
                            <td>
                                @if ($target->user == true)
                                {{$target->user->username }} ({{$target->user->full_name }})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>PRODUK</th>
                            <td>{{ $target->title }}</td>
                        </tr>
                        
                        <tr>
                            <th>BUDGET</th>
                            <td>{{ number_format($target->budget_from,0,',','.') }} - {{ number_format($target->budget_to,0,',','.') }}</td>
                        </tr>
                        
                        <tr>
                            <th>STATUS</th>
                            <td>{!! getstatus($target->project_status) !!}</td>
                        </tr>
                        
                        
                        
                        <tr>
                            <th colspan="2">DESKRIPSI</th>
                        </tr>
                        <tr>
                            <th colspan="2">{!! $target->deskripsi !!}</th>
                        </tr>
                        @if ($target->project_status=="Pending")
                            <tr>
                                <th>REJECT NOTE</th>
                                <td>
                                    <textarea class="form-control" rows="5" name="rejectnote" id="rejectnote"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2">
                                    <button type="button" class="btn btn-danger" id="reject" data-id="{{ $target->id }}">Reject</button>
                                    <button type="button" class="btn btn-success" id="approve" data-id="{{ $target->id }}">Approve</button>
                                </th>
                            </tr>
                        @endif
                        
                    </table>
                    <table>
                    @if ($target->project_status=="Start")
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>BIDER</th>
                                        <th>AMOUNT</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @php 
                                    $pbid = \App\Models\ProjectBid::where("project_id", $target->id)->get();
                                    @endphp
                                    @foreach ( $pbid as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->user->username }}</td>
                                        <td>{{ rupiah($item->amount) }}</td>
                                        <td>{!!getstatus($item->status)!!}</td>
                                    </tr>
                                    @endforeach
                                
                                </tbody>
                            </table>
                        </div>
                    
                       
                    @endif
                    </table>
                </div>
			</div>
		</div>

        
	</div>
</div>
<script>
    $('#reject').click(function(){
        var items = $(this).attr("data-id") ;
        Swal.fire({
                title: 'Reject Project',
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

    $('#approve').click(function(){
        var items = $(this).attr("data-id") ;
        Swal.fire({
                title: 'Approve Project',
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
            url: '/admin/project/reject',
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
            url: '/admin/project/approve',
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
