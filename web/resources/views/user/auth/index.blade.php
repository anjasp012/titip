@extends('user.layouts.app')

@section('content')
@include('user.layouts.sidebar')
<div class="col-lg-7">
	  <div class="card">
		<div class="card-body p-3">
		  <h1>Masih Belum Yakin?</h1>
		  @if (Auth::check() == true)
		  	<a href="javascript:;" onclick="modal('send', 'Pertanyaan', '{{ url('posting/send') }}')" class="btn btn-primary rounded-pill">Buat Soal</a>
			<a href="{{ url('project/create') }}" class="btn btn-primary rounded-pill">Buat Project</a>
		  @else
		  	<a href="{{ url('auth/login') }}" class="btn btn-primary rounded-pill">Buat Soal</a>
			<a href="{{ url('auth/login') }}" class="btn btn-primary rounded-pill">Buat Project</a>
		  @endif
		  
		</div>
	  </div>

	<div class="row height d-flex justify-content-center align-items-center">
		<div class="col-md-12">
			<div class="card">
				<div class="mt-2">
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
							<div class="d-flex flex-row align-items-center"> <span class="mr-2 itemmapel">{{@$item->user->username}}</span> &nbsp;-&nbsp;<small class="timefeed">{{time_elapsed_string($item->created_at)}}</small></div>
							<span class="badge bg-dark rounded-pill">+{{$item->point}} Point</span>
							</div>
							<p class="text-justify comment-text my-3"><a href="{{url('posting/read/'.$item->id)}}" class="textlink">{{cleatags($item->konten)}}</a></p>
							<div class="d-flex flex-row user-feed justify-content-between">
							<div class="d-flex">
								<span class="mr-2 itemmapel text-primary">{{@$item->category->name}}</span>
								{{--<i class="bi bi-eye eyeview"></i>
								<div class="avatars">
								<span class="avatar">
									<img src="{{ asset('assets/titiptugas/img/profile-img.jpg')}}" width="30" height="30" />
								</span>
								<span class="avatar">
									<img src="{{ asset('assets/titiptugas/img/profile-img.jpg')}}" width="30" height="30" />
								</span>
								<span class="avatar">
									<img src="{{ asset('assets/titiptugas/img/profile-img.jpg')}}" width="30" height="30" />
								</span>
								<span class="avatar">
									<img src="{{ asset('assets/titiptugas/img/profile-img.jpg')}}" width="30" height="30" />
								</span>
								<span class="avatar">
									<img src="{{ asset('assets/titiptugas/img/profile-img.jpg')}}" width="30" height="30" />
								</span>
								</div>--}}
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
			<div class="card">
				<div class="card-body">
					<div class="mt-2">
						<h5 class="card-title">Project</h5>
					</div>
					<div class="mt-2">
						@foreach ($projectlist as $item)
					
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
									<span class="mr-2 itemmapel">{{$item->title}}</span> 
								</div>
								<span class="timefeed">{{time_elapsed_string($item->created_at)}}</span>
								</div>
								<p class="text-justify comment-text my-3"><a href="{{url('project/detail/'.$item->id)}}" class="textlink">{!!Str::words(strip_tags($item->deskripsi), '25')!!}</a></p>
								<div class="d-flex flex-row user-feed justify-content-between">
								</div>
							</div>
						</div>
					
					@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
@include('user.layouts.right')
@endsection
