@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ request()->url() }}" id="main_form">
                    @csrf
                    <div class="form-group">
                        <label>Point Jawab <text class="text-danger">*</text></label>
                        <input type="text" class="form-control" name="jawab" value="{{website_config('bonus_point')->jawab}}">
                        <small class="text-danger name_error"></small>
                    </div>
                    <div class="form-group">
                        <label>Point Affiliasi <text class="text-danger">*</text></label>
                        <input type="text" class="form-control" name="upline" value="{{website_config('bonus_point')->upline}}">
                        <small class="text-danger name_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Admin Withdraw <text class="text-danger">*</text></label>
                        <input type="text" class="form-control" name="adminwd" value="{{website_config('bonus_point')->adminwd}}">
                        <small class="text-danger name_error"></small>
                    </div>

                    <div class="form-group">
                        <label>Minimum Withdraw <text class="text-danger">*</text></label>
                        <input type="text" class="form-control" name="minwd" value="{{website_config('bonus_point')->minwd}}">
                        <small class="text-danger name_error"></small>
                    </div>
                    
                    <div class="text-right">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
        
	</div>
</div>
<script>
    
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
				
				success: function(data) {
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
					
				},
				error:function() {
					swal.fire("Gagal!", "Terjadi kesalahan..", "error");
					
				},
			});
			e.preventDefault();
		});
	});

	

</script>
@endsection
