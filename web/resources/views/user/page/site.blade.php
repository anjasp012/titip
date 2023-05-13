@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card m-b-30">
			<div class="card-body">
				{!! $target->content !!}
			</div>
		</div>
	</div>
</div>
@endsection