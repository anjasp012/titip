<div class="card">
    <div class="card-body pt-3">
    <div class="d-flex flex-column align-items-center text-center profiles">
            
        @if (Auth::user()->avatar !="")
            <img src="{{ url('public/avatar/'.Auth::user()->avatar)}}" alt="Admin" class="rounded-circle mt-3" width="150" height="150">
        @else
            <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email)}}.jpg?s=150&d=monsterid" alt="Admin" class="rounded-circle mt-3" width="150" height="150">
        @endif

        <div class="mt-3">
        <h4>{{ Auth::user()->username }}</h4>
        <span class="badge bg-light rounded-pill profilecrown"><i class="ri-vip-crown-line"></i> {{ getLevel(Auth::user()->point)}}</span>
        <br>
        <span class="badge bg-dark rounded-pill">Point : {{ Auth::user()->point}}</span>    
        </div>
    </div>
    </div>
</div>
<div class="card mt-3">
    <ul class="list-group list-group-flush">
    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="mb-0"></svg>Jawaban</h6>
        <span class="badge bg-primary rounded-pill">{{ Auth::user()->jawaban}}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="mb-0">Tercerdas</h6>
        <span class="badge bg-success rounded-pill">{{ Auth::user()->tercerdas}}</span>
    </li>
    @php
        $refuser = \App\Models\User::where('upline', Auth::user()->id)->count();
    @endphp
    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="mb-0">Referral User</h6>
        <span class="badge bg-success rounded-pill">{{ $refuser}}</span>
    </li>

    
    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="mb-0">Referral Bonus</h6>
        <span class="badge bg-success rounded-pill">{{ Auth::user()->bonus_rp}}</span>
    </li>
    
    </ul>
</div>
