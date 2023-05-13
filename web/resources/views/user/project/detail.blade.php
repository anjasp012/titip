@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')

    <div class="col-md-3 mb-3">
        @include('user.layouts.left_profile')
    </div>
    <div class="col-md-9">
        <div class="card">
            
                <div class="card-body pb-5">
                    <h5 class="card-title">{{$target->title}}</h5>

                    <div class="news">
                        {!!$target->deskripsi!!}
                        
                        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                            <div class="row">
                                <div class="col-md-6">
                                    <b>Published Budget:</b> {{rupiah($target->budget_from)}} - {{rupiah($target->budget_to)}}<br>
                                    <b>Published Date:</b> {{$target->created_at}}<br>
                                    <b>Select Deadline:</b> {{$target->estimasi}} Day<br>
                                    
                                    
                                </div>
                                <div class="col-md-6">
                                    <b>Project Status:</b> {{$target->project_status}}<br>
                                    <b>Bid Count:</b> {{$target->bidcount}}<br>
                                    <b>Finish Days:</b> {{$target->estimasi}}
                                </div>
                            </div>
                        </div>    
                        <div class="text-center">
                            @if ($target->user_id != Auth::user()->id)
                                @if (!$isbid)
                                    <a href="{{url('project/bid/'.$target->id)}}" class="btn btn-primary btn-sm w-100">Place Bid</a>
                                @endif
                                
                            @endif
                            
                        </div>
                        <hr>
                        <h5 class="card-title">Project Owner</h5>
                        <div class="news">
                            <div class="post-item clearfix">
                               
                                @if ($target->user->avatar !="")
                                    <img src="{{ url('public/avatar/'.$target->user->avatar)}}" width="40" height="40" class="img-thumbnail">
                                @else
                                    <img src="https://www.gravatar.com/avatar/{{ md5(@$target->user->email)}}.jpg?s=150&d=monsterid" width="40" height="40" class="img-thumbnail">
                                @endif
                                <h4><a href="#">{{$target->user->username}}</a></h4>
                            </div>
                        </div>
                        
                    </div>
                </div>
        </div><!-- End News & Updates -->

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Project Bids</h5>
                    <div class="row">
                        @foreach ($projectbid as $item)
                            <div class="col-2 text-center">
                                @if ($item->user->avatar !="")
                                    <img src="{{ url('public/avatar/'.$item->user->avatar)}}" class="img-thumbnail">
                                @else
                                    <img src="https://www.gravatar.com/avatar/{{ md5(@$item->user->email)}}.jpg?s=150&d=monsterid"class="img-thumbnail">
                                @endif
                                <br>
                                <span class="text-primary">{{$item->user->username}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div><!-- End Top Selling -->


    </div>

@endsection
