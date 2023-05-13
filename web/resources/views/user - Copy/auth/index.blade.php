@extends('user.layouts.app')
{{-- @section('breadcrumb-first', 'Dasbor')
@section('breadcrumb-second', website_config('main')->website_name) --}}
@section('content')
@if (Auth::check() == true)
@php
if (website_config('template')->number == 'first-template') {
    $card_title_class = 'card-title';
} elseif (website_config('template')->number == 'second-template') {
    $card_title_class = 'header-title';
} elseif (website_config('template')->number == 'third-template') {
    $card_title_class = 'header-title';
} elseif (website_config('template')->number == 'custom-template') {
    $card_title_class = 'header-title';
} else {
    $card_title_class = 'card-title';
}
@endphp
<div class="row">
    <div class="col-xl-7">
        <h5 class="">Dasbor</h5>
        <div class="card">
            <div class="">
                <div id="carousels" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carousels" data-slide-to="0" class="active"></li>
                        <li data-target="#carousels" data-slide-to="1"></li>
                        <li data-target="#carousels" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner" role="listbox">
                        <div class="carousel-item active">
                            <img class="d-block img-fluid" src="{{ website_config('banner')->value }}" style="max-height:300px; width: 100%" alt="First slide">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carousels" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Sebelumnya</span>
                    </a>
                    <a class="carousel-control-next" href="#carousels" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Selanjutnya</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card card-white">
            <div class="card-body" style="margin-bottom: -10px;">
                <div class="float-left">
                    <h5 class="stats-number"><i class="fa fa-wallet text-primary"></i> Rp {{ number_format(Auth::user()->balance,0,',','.') }}</h5>
                </div>
                <div class="float-right">
                    <a href="{{ url('deposit/new') }}" class="btn btn-primary btn-sm" style="margin-top: -7px;">Isi Saldo</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 d-none d-sm-inline-block">
        <h5 class="">5 Informasi Terbaru</h5>
        <div class="">
			<div class="">
                @foreach($information as $value)
                <div class="alert alert-secondary text-dark">
                    <div class="float-left">
                        {!! category($value->category) !!} - {{ \Carbon\Carbon::parse($value->created_at)->translatedFormat('d F Y - H:i') }}
                    </div>
                    <br />
                    <div class="mb-2 mt-2 text-left"> 
                        {!! nl2br($value->content) !!}
                    </div>
                </div>
                @endforeach
                @if ($information->count() >= 5)
                <div class="text-center">
                    <a href="{{ url('page/information') }}" class="text-primary">Lihat semua...</a>
                </div>
                @endif       
            </div>
        </div>
    </div>
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
    <div class="col-lg-12">
        <div class="card m-b-30">
			<div class="card-body">
				<h4 class="{{ $card_title_class }} text-uppercase mb-3">
					<i class="fa fa-info-circle"></i> 5 Informasi Terbaru
				</h4>
                @foreach($information as $value)
                <div class="alert alert-secondary text-dark">
                    <div class="float-left">
                        {!! category($value->category) !!} - {{ \Carbon\Carbon::parse($value->created_at)->translatedFormat('d F Y - H:i') }}
                    </div>
                    <br />
                    <div class="mb-2 mt-2 text-left"> 
                        {!! nl2br($value->content) !!}
                    </div>
                </div>
                @endforeach
                @if ($information->count() >= 5)
                <div class="text-center">
                    <a href="{{ url('page/information') }}" class="text-primary">Lihat semua...</a>
                </div>
                @endif       
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-lg-12">
        <div class="card text-center grid-margin">
            <div class="card-body">
                <i class="fa fa-question text-green" style="font-size: 30px; margin: 10px;"></i>
                <h5 class="text-uppercase">Tentang Kami?</h5>
                <p>{!! website_config('about_us') !!}</p>
                <a href="{{ url('auth/login') }}" class="btn btn-success"><i class="fa fa-sign-in-alt fa-fw"></i> Masuk</a> 
                <a href="{{ url('auth/register') }}" class="btn btn-warning"><i class="fa fa-user-plus fa-fw"></i> Daftar</a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-body grid-margin">
            <center>
            <i class="fa fa-thumbs-up text-green" style="font-size: 40px; margin: 10px;"></i>
            <h5 class="text-uppercase">Layanan Terbaik</h5>
            Kami menyediakan berbagai layanan terbaik untuk kebutuhan sosial media & pulsa Anda.
            </center>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card card-body grid-margin">
            <center>
                <i class="fa fa-hands-helping text-green" style="font-size: 40px; margin: 10px;"></i>
                <h5 class="text-uppercase">Pelayanan Bantuan</h5>
                Kami selalu siap membantu jika Anda membutuhkan kami dalam penggunaan layanan kami.
            </center>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card card-body grid-margin">
            <center>
                <i class="fa fa-desktop" style="font-size: 40px; margin: 10px;"></i>
                <h5 class="text-uppercase">Desain Responsif</h5>
                Kami menggunakan desain website yang dapat diakses dari berbagai <i>device</i>, baik smartphone maupun PC.
            </center>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card card-body grid-margin">
            <center>
                <i class="fa fa-random text-green" style="font-size: 40px; margin: 10px;"></i>
                <h5 class="text-uppercase">Dukungan API</h5>
                Kami memiliki Dukungan API Untuk pemilik panel sehingga Anda dapat menjual kembali layanan kami dengan mudah.
            </center>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card card-body grid-margin">
            <center>
                <i class="fa fa-code text-green" style="font-size: 40px; margin: 10px;"></i>
                <h5 class="text-uppercase">Pembaruan</h5>
                Layanan Selalu Diperbarui Agar lebih ditingkatkan dan memberi Anda pengalaman terbaik.
            </center>
            <br />
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card card-body grid-margin">
            <center>
                <i class="fa fa-shopping-cart text-green" style="font-size: 40px; margin: 10px;"></i>
                <h5 class="text-uppercase">Resellers</h5>
                Anda dapat menjual kembali layanan kami dan menumbuhkan Profit Anda dengan mudah.
            </center>
            <br />
        </div>
    </div>
</div>
@endif
@endsection