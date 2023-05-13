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
						<thead class="thead-light">
							<tr>
								<th>#</th>
								<th>PELAPOR</th>
								<th>TITLE</th>
								<th>AKSI</th>
							</tr>
						</thead>
						<tbody> 
						@foreach ($list as $item)
							<tr>
								<td>{{ $item->id }}</td>
								<td>{{ $item->user->username }}</td>
								<td>{{ Str::limit(strip_tags(@$item->posting->konten), '50') }}</a></td>
								<td>
                                    <a href="javascript:;" onclick="deleteData(this,{{$item->id}},'Postingan ini ','{{url('admin/posting/delete/' .@$item->posting->id)}}')" class="badge badge-danger badge-sm" data-toggle="tooltip" title="Hapus"><i class="fa fa-trash"></i></a>
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
