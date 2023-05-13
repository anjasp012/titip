@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead>
                            <tr>
                                <th scope="col">TANGGAL</th>
                                <th scope="col">USER</th>
                                <th scope="col">Bank</th>
                                <th scope="col" class="text-center">No.Rekening</th>
                                <th scope="col" class="text-center">Nama</th>
								<th scope="col" class="text-center">Amount</th>
                                <th scope="col" class="text-center">Jenis</th>
                                <th scope="col" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $item)
                            <tr>
                                <th scope="row">{{formatTgl($item->created_at)}}</th>
                                <td>{{$item->user->username}}</td>
                                <td>{{$item->bank}}</td>
                                <td class="text-center">{{$item->rekening}}</td>
                                <td class="text-center">{{$item->nama}}</td>
                                <td class="text-center">{{rupiah($item->amount)}}</td>
                                <td class="text-center">{{$item->jenis}}</td>
                                <td class="text-center">
                                   <a href="javascript:void(0)" class="btn btn-sm btn-info approve" data-id="{{$item->id}}">Approve</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
        $('.approve').click(function() {
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
                url: '/admin/user/withdraw/approve',
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
