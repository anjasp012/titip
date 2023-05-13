@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')

<div class="col-md-12">
    <div class="card m-b-30">
        <div class="card-body">
            <h5 class="card-title">{{$target->title}}</h5>
            {!! $target->content !!}
        </div>
    </div>
</div>


</div>
@endsection
