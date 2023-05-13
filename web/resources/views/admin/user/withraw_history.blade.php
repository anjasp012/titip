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
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">USER</th>
                                <th scope="col">Bank</th>
                                <th scope="col" class="text-center">No.Rekening</th>
                                <th scope="col" class="text-center">Nama</th>
                                <th scope="col" class="text-center">Amount</th>
                                <th scope="col" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $item)
                            <tr>
                                <th scope="row">{{formatTgl($item->created_at)}}</th>
                                <td>{{$item->user->username}}</td>
                                <td>{{$item->bank}}</td>
                                <td class="text-center">{{$item->rekening}}</td>
                                <td class="text-center">{{$item->nama}}</td>
                                <td class="text-center">{{rupiah($item->amount)}}</td>
                                <td class="text-center">
                                    {!!getStatus($item->status)!!}
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
