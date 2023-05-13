@extends('user.layouts.app')
{{-- @section('breadcrumb-first', 'Dasbor')
@section('breadcrumb-second', website_config('main')->website_name) --}}
@section('content')
@include('user.layouts.sidebar')
<div class="col-lg-7">
	 
	<div class="row height d-flex justify-content-center align-items-center">
		<div class="col-md-12">
			<div class="card">
				<div class="mt-2">
                    <div class="d-flex flex-row p-3 rowquest">
                     <h5>Search : {{$keyword}}</h5>
                    </div>
				@foreach ($listdata as $item)
					<div class="d-flex flex-row p-3 rowquest">
						<div class="me-3">
							@if ($item->user->avatar !="")
								<img src="{{ url('public/avatar/'.$item->user->avatar)}}" width="40" height="40" class="rounded-circle">
							@else
								<img src="https://www.gravatar.com/avatar/{{ md5($item->user->email)}}.jpg?s=40&d=monsterid" width="40" height="40" class="rounded-circle">
							@endif
						</div>
						<div class="w-100 boxtext">
							<div class="d-flex justify-content-between align-items-center">
								<div class="d-flex flex-row align-items-center"> 
									<span class="mr-2 itemmapel">{{@$item->user->username}}</span> &nbsp;-&nbsp;<small class="timefeed">{{time_elapsed_string($item->created_at)}}</small>
								</div>
								<span class="badge bg-dark rounded-pill">+{{$item->point}} Point</span>
							</div>
							<p class="text-justify comment-text my-3"><a href="{{url('posting/read/'.$item->id)}}" class="textlink">{{Str::words(strip_tags($item->konten), '25')}}</a></p>
							<div class="d-flex flex-row user-feed justify-content-between">
							<div class="d-flex">
								<span class="mr-2 itemmapel text-primary">{{@$item->category->name}}</span>
								
							</div>

							@if (Auth::check() == true)
								<a href="{{url('posting/read/'.$item->id)}}" class="btn rounded-pill btn-sm btn-outline-dark px-3">Jawab</a>
							@else
								<a href="{{ url('auth/login') }}" class="btn rounded-pill btn-sm btn-outline-dark px-3">Jawab</a>
							@endif
							</div>
						</div>
					</div>
			 
				@endforeach
				</div>
			</div>
			{{ $listdata->links() }}
		</div>
	</div>

</div>
@include('user.layouts.right')
@endsection
