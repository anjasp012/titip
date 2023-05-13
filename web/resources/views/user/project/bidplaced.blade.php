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

                    
                    <h5 class="card-title">Bid Placed</h5>
<p align="left">Terimakasih atas penawaran Anda.</p>
<p align="justify">Penawaran Anda telah kami simpan dalam database kami. Anda dapat melakukan lebih dari satu kali penawaran pada project yang sama, jika Anda merasa memiliki penawaran yang lebih menarik agar owner mau merespon penawaran Anda.</p>
<p align="justify">Kami akan memberitahukan Anda melalui email Anda dan website hasil dari penawaran project ini.</p>
<p align="justify">Jika Anda memenangkan project ini, Anda bersedia menyerahkan seluruh hasil kerja Anda termasuk source code yang Anda tulis.</p>
<p align="justify">Keputusan untuk menerima atau menolak tawaran Anda sepenuhnya merupakan hak owner.</p>
<h3 align="center">Anda dilarang membuat kesepakatan dengan owner di luar titiptugas.com berkaitan dengan project ini dengan menawarkan transaksi secara langsung, seperti mentransfer langsung ke rekening Anda tanpa melalui rekening titiptugas.com.<br>
Terhadap pelanggaran ini, Anda akan dikenakan sanksi tegas oleh titiptugas.com.</h3>
<p align="center">Silahkan baca kembali
<a class="more" href="#"><strong>Syarat Layanan</strong></a> titiptugas.com</p>
                    
                    </div>
                </div>
        </div><!-- End News & Updates -->

    </div>

@endsection


