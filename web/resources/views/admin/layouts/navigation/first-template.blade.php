@if (Auth::guard('admin')->check() == true)
@php
$ticket_waiting = \App\Models\Ticket::where('is_read_admin', '0')->count();
@endphp
<li class="menu-title">Menu</li>
<li>
    <a href="{{ url('admin/') }}" class="waves-effect">
        <i class='fa fa-home'></i>
        <span>Dasbor</span>
    </a>
</li>
<li>
    <a href="{{ url('admin/admin/list') }}" class="waves-effect">
        <i class='fa fa-user-secret'></i>
        <span>Admin</span>
    </a>
</li>
<li>
    <a href="{{ url('admin/user/list') }}" class="waves-effect">
        <i class='fa fa-users'></i>
        <span>Pengguna</span>
    </a>
</li>

<li>
    <a href="{{ url('admin/user/list') }}" class="waves-effect">
        <i class='fa fa-users'></i>
        <span>Pengguna</span>
    </a>
</li>



<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-file"></i> <span>Kategori</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('admin/posting/kategori') }}">Soal</a></li>
        <li><a href="{{ url('admin/project/kategori') }}">Project</a></li>
    </ul>
</li>

<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-file"></i> <span>Posting</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('admin/posting/list') }}">List Postingan</a></li>
        <li><a href="{{ url('admin/posting/bermasalah') }}">Postingan Bermasalah</a></li>
    </ul>
</li>

<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-money-bill"></i> <span>Deposit</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('admin/deposit/method/list') }}">Metode</a></li>
        <li><a href="{{ url('admin/deposit/list') }}">List</a></li>
        <li><a href="{{ url('admin/deposit/report') }}">Report</a></li>
    </ul>
</li>

<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-money-bill"></i> <span>Witdraw</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('admin/user/withdraw/pending') }}">Pending</a></li>
        <li><a href="{{ url('admin/user/withdraw/history') }}">History</a></li>
    </ul>
</li>

<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-folder-open"></i> <span>Project</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('admin/project/list') }}">List Project</a></li>
        <li><a href="{{ url('admin/project/pending') }}">Pending Project</a></li>
        <li><a href="{{ url('admin/project/gugatan') }}">Gugatan</a></li>
    </ul>
</li>


<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-file-alt"></i> <span>Log</span></a>
    <ul class="sub-menu" aria-expanded="true">
        <li><a href="{{ url('admin/log/user_register') }}">Pendaftaran</a></li>
        <li><a href="{{ url('admin/log/user_login') }}">Pengguna Masuk</a></li>
        <li><a href="{{ url('admin/log/admin_login') }}">Admin Masuk</a></li>
        @if (Auth::guard('admin')->user()->level == 'Admin')
            <li><a href="{{ url('admin/log/user_balance') }}">Saldo Pengguna</a></li>
            {{--<li><a href="{{ url('admin/log/bank_mutation') }}">Mutasi Bank</a></li>--}}
        @endif
    </ul>
</li>

<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fa fa-cogs"></i> <span>Pengaturan</span></a>
    <ul class="sub-menu" aria-expanded="true">
        @if (Auth::guard('admin')->user()->level == 'Developer')
        {{--<li><a href="{{ url('admin/settings/bank_account/list') }}">Akun Bank</a></li>--}}
        @endif
        <li><a href="{{ url('admin/settings/website_information/list') }}">Informasi</a></li>
        {{--<li><a href="{{ url('admin/settings/website_page/list') }}">Halaman</a></li>--}}
        <li><a href="{{ url('admin/settings/website_configs') }}">Konfigurasi Website</a></li>
        <li><a href="{{ url('admin/settings/point') }}">Point & withdraw</a></li>
    </ul>
</li>
@endif
