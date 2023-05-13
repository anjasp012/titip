@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')

<div class="row gutters-sm">
    <div class="col-md-3 mb-3">
        @include('user.layouts.left_profile')
    </div>
    <div class="col-md-9">
        <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
            <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#bordered-home" type="button" role="tab" aria-controls="home" aria-selected="true">Jawaban</button>
            </li>
            <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#bordered-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Pertanyaan</button>
            </li>
            
        </ul>
        <div class="tab-content pt-4" id="borderedTabContent">
            <div class="tab-pane fade show active" id="bordered-home" role="tabpanel" aria-labelledby="home-tab">
            
                @foreach ($answer as $item)
                    <div class="card mb-1">
                        <div class="card-body">
                            <div class="d-flex flex-row p-3">
                                <div class="me-3 mt-1">
                                    @if ($item->user->avatar !="")
                                        <img src="{{ url('public/avatar/'.$item->user->avatar)}}" width="40" height="40" class="rounded-circle">
                                    @else
                                        <img src="https://www.gravatar.com/avatar/{{ md5($item->user->email)}}.jpg?s=40&d=monsterid" width="40" height="40" class="rounded-circle">
                                    @endif
                                </div>
                                <div class="namepost d-grid">
                                    <span class="post_name">{{$item->user->username}}</span>
                                    <span class="post_date">{{time_elapsed_string($item->created_at)}} </span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{url('posting/read/'.$item->id)}}" class="textlink">{{Str::words(strip_tags($item->jawaban), '25')}}</a>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="tab-pane fade card" id="bordered-profile" role="tabpanel" aria-labelledby="profile-tab">
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
                            <div class="d-flex flex-row align-items-center"> <span class="mr-2">{{@$item->category->name}}</span> <small class="c-badge">Top Comment</small> </div>
                            <small class="timefeed">{{time_elapsed_string($item->created_at)}}</small>
                            </div>
                            <p class="text-justify comment-text my-3"><a href="{{url('posting/read/'.$item->id)}}" class="textlink">{{Str::words(strip_tags($item->konten), '25')}}</a></p>
                            <div class="d-flex flex-row user-feed justify-content-between">
                            
                            </div>
                        </div>
					</div>
			 
				@endforeach
            </div>
            
        </div><!-- End Bordered Tabs -->

          
    </div>
  </div>
  
@endsection
