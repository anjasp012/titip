<header id="header" class="header fixed-top">
    <div class="container d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{url('/')}}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets/titiptugas/images/logo.png')}}" alt="">
            </a>
        </div><!-- End Logo -->

        <div class="search-bar flex-fill">
            <form class="search-form d-flex align-items-center" method="GET" action="/search">
                <input type="text" name="search" placeholder="Search" title="Enter search keyword">
                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
            </form>
        </div><!-- End Search Bar -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li><!-- End Search Icon-->

                <li class="nav-item">
                    @if (Auth::check() == true)
                        <a class="ajukan d-none d-sm-block" href="javascript:;" onclick="modal('send', 'Pertanyaan', '{{ url('posting/send') }}')">
                            Ajukan Pertanyaan
                        </a><!-- End Messages Icon -->
                    @else
                        <a class="ajukan d-none d-sm-block" href="{{url('auth/login')}}">
                            Ajukan Pertanyaan
                        </a>
                    @endif
                </li>

                <li class="nav-item dropdown d-none d-sm-block">
                    {{--<a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-chat-left-text"></i>
                        <span class="badge bg-success badge-number">3</span>
                    </a><!-- End Messages Icon -->
                    
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                        <li class="dropdown-header">
                            You have 3 new messages
                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="message-item">
                            <a href="#">
                                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                                <div>
                                    <h4>Maria Hudson</h4>
                                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                    <p>4 hrs. ago</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="message-item">
                            <a href="#">
                                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                                <div>
                                    <h4>Anna Nelson</h4>
                                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                    <p>6 hrs. ago</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="message-item">
                            <a href="#">
                                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                                <div>
                                    <h4>David Muldon</h4>
                                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                    <p>8 hrs. ago</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="dropdown-footer">
                            <a href="#">Show all messages</a>
                        </li>

                    </ul>--}}

                </li><!-- End Messages Nav -->

                <li class="nav-item dropdown pe-3">
					@if (Auth::check() == true)
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">

                        @if (Auth::user()->avatar !="")
                            <img src="{{ url('public/avatar/'.Auth::user()->avatar)}}" alt="Profile" class="rounded-circle" width="40" height="40">
                        @else
                            <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email)}}.jpg?s=40&d=monsterid" alt="Profile" class="rounded-circle" width="40" height="40">
                        @endif

                       
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->username }}</span>
                    </a><!-- End Profile Iamge Icon -->
					
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ Auth::user()->username }}</h6>
                            <span>{{ Auth::user()->level }}</span>
                        </li>
                        
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('account/profile') }}">
                                <i class="ri-fingerprint-line""></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                       
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('downline/summary') }}">
                                <i class="ri-fingerprint-line""></i>
                                <span>Affiliate</span>
                            </a>
                        </li>
                      
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('account/settings') }}">
                                <i class="bi bi-question-circle"></i>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('page/information') }}">
                                <i class="bi bi-question-circle"></i>
                                <span>Informasi</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('project/list') }}">
                                <i class="ri-bill-line"></i>
                                <span>My Project</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('project/browse') }}">
                                <i class="ri-bill-line"></i>
                                <span>Browse Project</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('project/create') }}">
                                <i class="ri-bill-line"></i>
                                <span>Create Project</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('project/bid-history') }}">
                                <i class="ri-pages-line"></i>
                                <span>My Bids</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('deposit/new') }}">
                                <i class="ri-wallet-3-line"></i>
                                <span>Deposit</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('deposit/history') }}">
                                <i class="ri-wallet-3-line"></i>
                                <span>Deposit History</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('account/withdraw') }}">
                                <i class="ri-wallet-3-line"></i>
                                <span>Withdraw</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('account/log/balance') }}">
                                <i class="ri-wallet-3-line"></i>
                                <span>Mutasi Saldo</span>
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('auth/logout') }}">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->
                @else
                    <li class="nav-item">
                        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="{{url('auth/login')}}">
                            Login
                        </a><!-- End Profile Iamge Icon -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="{{url('auth/register')}}">
                            Register
                        </a><!-- End Profile Iamge Icon -->
                    </li>
				@endif

            </ul>
        </nav><!-- End Icons Navigation -->
    </div>
</header><!-- End Header -->
