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
								<th>USER</th>
								<th>TITLE</th>
								<th>BUDGET</th>
								<th>DEADLINE</th>
							</tr>
						</thead>
						<tbody> 
						@foreach ($list as $item)
							<tr>
								<td>{{ $item->id }}</td>
								<td>{{ $item->user->username }}</td>
								<td><a href="{{url('/admin/project/detail/'.$item->id)}}">{{ Str::limit(strip_tags($item->title), '50') }}</a></td>
								<td>{{ rupiah($item->budget_from) }}-{{ rupiah($item->budget_to) }}</td>
								<td>{{ $item->estimasi }}</td>
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
