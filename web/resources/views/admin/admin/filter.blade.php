<a href="javascript:;" data-toggle="modal" data-target="#modal-filter" class="btn btn-primary btn-sm">
	<i class="fa fa-filter fa-fw"></i> Filter Admin
</a>
<div id="modal-filter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-filterLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="modal-filterLabel"><i class="fa fa-filter"></i> Filter Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="GET" id="filter-form">
                    <div class="form-group">
                        <label>Filter Akses</label>
                        <div class="input-group">
                            <select class="form-control" name="filter_level" id="filter_level" data-toggle="select2">
                                <option value="" selected>Semua...</option>
                                @foreach($levels as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>		
                            <span class="input-group-prepend">
                                <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Filter Status</label>
                        <div class="input-group">
                            <select class="form-control" name="filter_status" id="filter_status" data-toggle="select2">
                                <option value="" selected>Semua...</option>
                                @foreach($statuses as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>		
                            <span class="input-group-prepend">
                                <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                            </span>
                        </div>
                    </div>
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