<a href="javascript:;" data-toggle="modal" data-target="#modal-filter" class="btn btn-primary btn-sm">
	<i class="fa fa-filter fa-fw"></i> Filter Akun Bank
</a>
<div id="modal-filter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-filterLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="modal-filterLabel"><i class="fa fa-filter"></i> Filter Akun Bank</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="GET" id="filter-form">
                    <div class="form-group">
                        <label>Cari</label>
                        <div class="input-group">							
                            <input type="text" class="form-control" name="search" id="search" placeholder="Ketik sesuatu..." value="{{ old('search') }}">
                            <span class="input-group-prepend">
                                <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </div>
				</form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#filter-form').on('submit', function(e) {
        $('#modal-filter').modal('hide');
        window.LaravelDataTables["data-table"].draw();
        e.preventDefault();
    });
</script>