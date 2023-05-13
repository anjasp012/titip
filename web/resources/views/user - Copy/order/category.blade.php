@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
    @foreach (\App\Models\ProductCategory::where('status', '1')->latest('id')->get() as $item)
    <div class="col-lg-4 mb-3 grid-margin">
        <div class="card bg-primary text-center h-100">
            <div class="d-table h-100" style="margin-top: 18px;">
                <a href="{{ url('order/'.$item->slug.'') }}">
                    <div class="text-center text-white d-table-cell align-middle">
                        <h6 class="h2 text-white">{!! $item->icon <> null ? '<i class="'.$item->icon.'"></i>' : '<i class="fa fa-globe"></i>' !!}</h6>
                        <h5 class="mt-0 mb-0 text-white font-16">{{ $item->name }}</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection