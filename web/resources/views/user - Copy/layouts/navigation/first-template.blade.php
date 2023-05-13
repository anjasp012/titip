@if (Auth::check() == true)
@php
$ticket_waiting = \App\Models\Ticket::where([['user_id', Auth::user()->id], ['is_read_user', '0']])->count();
@endphp
<li class="menu-title">Menu</li>
<li class="@if (request()->path() == '/') mm-active @endif">
    <a href="{{ url('/') }}" class="waves-effect @if (request()->path() == '/') active @endif">
        <i class='fa fa-home'></i>
        <span>Dasbor</span>
    </a>
</li>
<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-user-friends"></i> <span>Downline</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('downline/summary') }}">Ringkasan</a></li>
    </ul>
</li>
<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-shopping-cart"></i> <span>Pemesanan</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('order/category') }}">Pesan Baru</a></li>
        <li><a href="{{ url('order/history') }}">Riwayat</a></li>
    </ul>
</li>
<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-money-check-alt"></i> <span>Deposit</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('deposit/new') }}">Deposit Baru</a></li>
        <li><a href="{{ url('deposit/history') }}">Riwayat</a></li>
    </ul>
</li>
<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-coins"></i> <span>Tukar Poin</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('point/exchange') }}">Baru</a></li>
        <li><a href="{{ url('point/exchange/history') }}">Riwayat</a></li>
    </ul>
</li>
<li>
    <a href="{{ url('ticket/list') }}" class="waves-effect">
        <i class='fa fa-envelope'></i>
        @if ($ticket_waiting > 0)
        <span class="badge badge-pill badge-warning float-right">{{ $ticket_waiting }}</span>
        @endif
        <span>Tiket</span>
    </a>
</li>
<li class="has-submenu">
    <a href="{{ url('page/product/list') }}">
        <i class="menu-icon fa fa-tags"></i>
        <span>Daftar Produk</span>
    </a>
</li>
<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-file"></i> <span>Halaman</span></a>
    <ul class="sub-menu" aria-expanded="true">
        @foreach (\App\Models\WebsitePage::all() as $item)
            <li><a href="{{ url('page/site/'.$item->slug.'') }}">{{ $item->title }}</a></li>
        @endforeach
    </ul>
</li>
<li class="has-submenu">
    <a href="{{ url('page/api_doc') }}">
        <i class="menu-icon fa fa-book"></i>
        <span>Dokumentasi API</span>
    </a>
</li>
@else
<li class="has-submenu">
    <a href="{{ url('/') }}">
        <i class="menu-icon fa fa-home"></i>
        <span>Dasbor</span>
    </a>
</li>
<li class="has-submenu">
    <a href="{{ url('auth/login') }}"> 
        <i class="menu-icon fa fa-sign-in-alt"></i>
        <span>Masuk</span>
    </a>
</li>
@if (Route::has('user.register'))
<li class="has-submenu">
    <a href="{{ url('auth/register') }}"> 
        <i class="menu-icon fa fa-user-plus"></i>
        <span>Daftar</span>
    </a>
</li>
@endif
@if (Route::has('user.reset'))
<li class="has-submenu">
    <a href="{{ url('auth/reset') }}"> 
        <i class="menu-icon fa fa-key"></i>
        <span>Atur Ulang Kata Sandi</span>
    </a>
</li>
@endif
<li class="has-submenu">
    <a href="{{ url('page/product/list') }}">
        <i class="menu-icon fa fa-tags"></i>
        <span>Daftar Produk</span>
    </a>
</li>
<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-file"></i> <span>Halaman</span></a>
    <ul class="sub-menu" aria-expanded="true">
        @foreach (\App\Models\WebsitePage::all() as $item)
            <li><a href="{{ url('page/site/'.$item->slug.'') }}">{{ $item->title }}</a></li>
        @endforeach
    </ul>
</li>
@endif