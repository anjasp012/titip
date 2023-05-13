@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('button')
<a href="javascript:;" onclick="modal('add', 'Kategori', '{{ url('admin/posting/form') }}')" class="btn btn-success btn-sm">
	<i class="fa fa-plus fa-fw"></i> Tambah Kategori
</a>
@endsection
@section('content')
<div class="row">
	<div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
					<table class="table table-borderless table-hover mb-0">
						<thead class="thead-light">
							<tr>
								<th>#</th>
								<th>NAMA</th>
								<th>SLUG</th>
								<th>AKSI</th>
							</tr>
						</thead>
						<tbody> 
						@foreach ($category as $item)
							<tr>
								<td>{{ $item->id }}</td>
								<td>{{ $item->name }}</td>
								<td>{{ $item->slug }}</td>
								<td>
                                    <a href="javascript:;"  onclick="modal('edit', 'Pengguna','{{url('admin/posting/form/'.$item->id)}}')"  class="badge badge-warning badge-sm btn-edit" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>
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

@endsection

