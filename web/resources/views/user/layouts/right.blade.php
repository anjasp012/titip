<div class="col-lg-3 profile">
    <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
            @if (Auth::check() == true)
                @if (Auth::user()->avatar !="")
                    <img src="{{ url('public/avatar/'.Auth::user()->avatar)}}" alt="Profile" class="rounded-circle" width="100" height="100">
                @else
                    <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email)}}.jpg?s=150&d=monsterid" alt="Profile" class="rounded-circle" width="100" height="100">
                @endif

                
                <h2>{{ Auth::user()->username }}</h2>
                <h3>{{ Auth::user()->level }}</h3>
                <span class="badge bg-light rounded-pill profilecrown"><i class="ri-vip-crown-line"></i> {{ getLevel(Auth::user()->point)}}</span>
                <br>
                <span class="badge bg-dark rounded-pill">Point : {{ Auth::user()->point}}</span>
            @endif
            {{--<div class="social-links mt-2">
                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>--}}
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="txt-navi mt-3">Best Rank</h5>
            <hr>
            @php 
                $ranks = \App\Models\User::orderBy("point","DESC")->take(5)->get();
            @endphp
            @foreach ($ranks as $item)
           
                <div class="d-flex text-muted pt-3">
                    @if ($item->avatar !="")
                        <img src="{{ url('public/avatar/'.$item->avatar)}}" alt="Profile" class="rounded-circle" width="32" height="32">
                    @else
                        <img src="https://www.gravatar.com/avatar/{{ md5($item->email)}}.jpg?s=40&d=monsterid" alt="Profile" class="rounded" width="32" height="32">
                    @endif
                    <div class="pt-2 mb-0 small lh-sm w-100">
                        <div class="d-flex justify-content-between mx-2">
                            <strong class="txt-navi">{{$item->username}}</strong>
                            <a href="#">{{$item->point}} Point</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
