@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('button')
<a href="javascript:;" onclick="modal('send', 'Tiket', '{{ url('admin/ticket/send') }}')" class="btn btn-success btn-sm">
	<i class="fa fa-plus fa-fw"></i> Kirim Tiket
</a>
@include('admin.ticket.filter')
@endsection
@section('content')
<div class="row">
	<div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-borderless table-hover mb-0'], false) !!}
                </div>
			</div>
		</div>
	</div>
</div>
{!! $dataTable->scripts() !!}
<script>
    document.getElementById("data-table").children[0].className = "thead-light";
    function closeTicket(elt, id, title, url) {
        swal.fire({
            title: "Tutup Tiket",
            html: 'ID Tiket <b style="font-weight: bold;">'+title+'</b>?',
            icon: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya, Tutup!",
            cancelButtonText: "Tidak, Batalkan",
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-secondary ml-2 mt-2",
            buttonsStyling: !1,
        }).then(result => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    error: function() {
                        swal.fire("Gagal", "Terjadi kesalahan.", "error");
                    },
                    success: function(result) {
                        if (result.result == false) {
                            swal.fire("Gagal", ""+result.message+"", "error");
                        } else {
                            swal.fire("Berhasil!", "Tiket berhasil ditutup.", "success").then(function () {
                                window.LaravelDataTables["data-table"].draw('page');
                            });
                        }
                    }
                });
               
            }
        });
    }
</script>
@endsection