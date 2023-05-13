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
                    <h5 class="card-title">Browse</h5>

                    <div class="news">
                        @foreach ($project as $item)
                        <div class="post-item clearfix pb-2">
                            @if (@$item->user->avatar !="")
                                <img src="{{ url('public/avatar/'.@$item->user->avatar)}}" >
                            @else
                                <img src="https://www.gravatar.com/avatar/{{ md5(@$item->user->email)}}.jpg?s=100&d=monsterid">
                            @endif

                            <h4><a href="{{url('project/detail/'.$item->id)}}">{{$item->title}}</a></h4>
                            <div class="konten">
                                {{strip_tags($item->deskripsi)}}
                                <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Published Budget:</b> {{rupiah($item->budget_from)}} - {{rupiah($item->budget_to)}}<br>
                                            <b>Published Date:</b> {{$item->created_at}}<br>
                                            <b>Select Deadline:</b> {{$item->estimasi}} Day<br>
                                           
                                           
                                        </div>
                                        <div class="col-md-6">
                                            <b>Project Status:</b> {!!getstatus($item->project_status)!!}<br>
                                            <b>Bid Count:</b> {{$item->bidcount}}<br>
                                            <b>Finish Days:</b> {{$item->estimasi}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                    
                        </div>

                        @endforeach
                    </div>
                </div>
        </div><!-- End News & Updates -->

    </div>

@endsection
