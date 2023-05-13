<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{ website_config('main')->meta_author }}" />
    <meta name="keywords" content="{{ website_config('main')->meta_keywords }}" />
    <meta name="description" content="{{ website_config('main')->meta_description}}" />
    <title>{{ website_config('main')->website_name }}</title>
    <link rel="shortcut icon" href="{{ website_config('main')->website_favicon }}">
    <link href="{{ asset('assets/landing/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/landing/css/magnific-popup.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/landing/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/landing/css/pe-icon-7-stroke.css') }}" rel="stylesheet" type="text/css" />           
    <link rel="stylesheet" href="{{ asset('assets/landing/css/owl.carousel.min.css') }}"/> 
    <link rel="stylesheet" href="{{ asset('assets/landing/css/owl.theme.css') }}"/> 
    <link rel="stylesheet" href="{{ asset('assets/landing/css/owl.transitions.css') }}"/>   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/landing/css/animate.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/landing/css/animations-delay.css') }}" rel="stylesheet" /> 
    <link href="{{ asset('assets/landing/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/landing/css/colors/green.css') }}" rel="stylesheet" id="color-opt">
</head>
<body>
    <div id="preloader">
        <div id="status">
            <div class="spinner">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg fixed-top navbar-custom navbar-light sticky">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="text-white" height="30" alt=""> {{ website_config('main')->website_name }} </span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="mdi mdi-menu"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#home">Utama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#product_type">Jenis Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('page/product/list') }}">Daftar Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('auth/login') }}">Masuk</a>
                    </li>
                    @if (website_config('main')->is_register_enabled)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('auth/register') }}">Daftar</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <section class="home-slider position-relative" id="home">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item align-items-center active" style="background-image:url('{{ asset('assets/landing/images/business/02.jpg') }}');">
                    <div class="bg-overlay"></div>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-12 text-center">
                                <div class="title-heading mt-4">
                                    <h1 class="heading mb-3 text-white title-dark animated fadeInUpBig animation-delay-3">{{ website_config('main')->website_name }} | Distributor dan Server Pulsa Termurah</h1>
                                    <p class="para-desc mx-auto text-white-50 para-dark animated fadeInUpBig animation-delay-7">{{ website_config('main')->website_name }} adalah sebuah website distributor dan server pulsa terlengkap, termurah, dan berkualitas.</p>
                                    <div class="mt-4 pt-2 animated fadeInUpBig animation-delay-11">
                                        <a href="{{ url('auth/login') }}" class="btn btn-custom mr-2 mb-2">Masuk</a>
                                        @if (website_config('main')->is_register_enabled)
                                        <a href="{{ url('auth/register') }}" class="btn btn-outline-custom mb-2">Daftar</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section bg-light" id="about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="section-title mb-4 pb-2">
                        <h4 class="title mb-4">Tentang Kami</h4>
                        <p class="text-muted">{!! website_config('main')->about_us !!}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 mt-4 pt-2">
                    <div class="service-container shadow rounded p-4 text-center">
                        <div class="number-icon position-relative">
                            <div class="icon-2 d-inline-block border rounded-pill">  
                                <i class="pe-7s-ticket text-custom"></i>
                            </div>
                            <div class="number text-center rounded-pill bg-white border">
                                <span class="font-weight-bold">01</span>
                            </div>
                        </div>                           
                        <div class="content mt-3">
                            <h5 class="title text-dark">Produk Terbaik</h5>
                            <p class="text-muted">Kami menyediakan berbagai produk terbaik untuk kebutuhan Anda.<br /><br /></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mt-4 pt-2">
                    <div class="service-container shadow rounded p-4 text-center">
                        <div class="number-icon position-relative">
                            <div class="icon-2 d-inline-block border rounded-pill">  
                                <i class="pe-7s-like text-custom"></i>
                            </div>
                            <div class="number text-center rounded-pill bg-white border">
                                <span class="font-weight-bold">02</span>
                            </div>
                        </div>                           
                        <div class="content mt-3">
                            <h5 class="title text-dark">Pelayanan Bantuan</h5>
                            <p class="text-muted">Kami selalu siap membantu jika Anda membutuhkan kami dalam penggunaan layanan kami.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mt-4 pt-2">
                    <div class="service-container shadow rounded p-4 text-center">
                        <div class="number-icon position-relative">
                            <div class="icon-2 d-inline-block border rounded-pill">  
                                <i class="pe-7s-display1 text-custom"></i>
                            </div>
                            <div class="number text-center rounded-pill bg-white border">
                                <span class="font-weight-bold">03</span>
                            </div>
                        </div>                           
                        <div class="content mt-3">
                            <h5 class="title text-dark">Desain Responsive</h5>
                            <p class="text-muted">Kami menggunakan desain website yang dapat diakses dari berbagai device, baik smartphone maupun PC.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section" id="product_type">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="section-title mb-4 pb-2">
                        <h4 class="title mb-4">Jenis Produk</h4>
                        <p class="text-muted mx-auto para-desc mb-0">Berikut adalah daftar jenis produk yang tersedia pada website kami.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach (\App\Models\ProductCategory::where('status', '1')->get() as $item)
                <div class="col-lg-4 col-md-6 col-12 mt-4 pt-2">
                    <div class="service-container shadow rounded p-4 text-center">
                        <div class="number-icon position-relative">
                            <div class="icon-2 d-inline-block border rounded-pill">  
                                {!! $item->icon <> null ? '<i class="'.$item->icon.' text-custom"></i>' : '<i class="fa fa-globe text-custom"></i>' !!}
                            </div>
                            <div class="number text-center rounded-pill bg-white border">
                                <span class="fw-bold">{{ $loop->iteration }}</span>
                            </div>
                        </div>                           
                        <div class="content mt-3">
                            <h5 class="title text-dark">{{ $item->name }}</h5>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <footer class="footer py-5 bg-dark">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 mt-4 pt-2">
                    <h3 class="mt-0 m-b-15">
                        <a href="{{ url('/') }}" class="logo logo-admin">
                            <span class="text-white text-uppercase" style="font-size: 30px; font-weight: Bold;">
                                {{ website_config('main')->website_name }}
                            </span>
                        </a>
                    </h3>
                    {{-- <img src="{{ asset('assets/images/'.website_config('main')->website_logo) }}" height="30" alt=""> --}}
                    <p class="text-foot mt-4">{!! website_config('main')->about_us !!}</p>
                    <ul class="list-unstyled social-icon social mb-0">
                        @if (website_config('socials')->facebook)
                        <li class="list-inline-item"><a href="{{ sprintf('https://facebook.com/%s', website_config('socials')->facebook) }}" target="_blank"><i class="mdi mdi-facebook" title="facebook"></i></a></li>
                        @endif
                        @if (website_config('socials')->whatsapp)
                        <li class="list-inline-item"><a href="{{ sprintf('https://wa.me/%s', website_config('socials')->whatsapp) }}" target="_blank"><i class="mdi mdi-whatsapp" title="whatsapp"></i></a></li>
                        @endif
                        @if (website_config('socials')->instagram)
                        <li class="list-inline-item"><a href="{{ sprintf('https://instagram.com/%s', website_config('socials')->instagram) }}" target="_blank"><i class="mdi mdi-instagram" title="instagram"></i></a></li>
                        @endif
                        @if (website_config('socials')->twitter)
                        <li class="list-inline-item"><a href="{{ sprintf('https://twitter.com/%s', website_config('socials')->twitter) }}" target="_blank"><i class="mdi mdi-twitter" title="twitter"></i></a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mt-4 pt-2">
                    <h4 class="text-light text-uppercase footer-head">Tautan</h4>
                    <ul class="list-unstyled footer-list mt-4">
                        @if ($page['contact_us'] == true)
                        <li><a href="{{ url('page/site/'.$page['contact_us']->slug.'') }}" class="text-foot"><i class="mdi mdi-chevron-right mr-2"></i> Kontak Kami</a></li>
                        @endif
                        @if ($page['faq'] == true)
                        <li><a href="{{ url('page/site/'.$page['faq']->slug.'') }}" class="text-foot"><i class="mdi mdi-chevron-right mr-2"></i> Pertanyaan Umum</a></li> 
                        @endif
                        @if ($page['tos'] == true)
                        <li><a href="{{ url('page/site/'.$page['tos']->slug.'') }}" class="text-foot"><i class="mdi mdi-chevron-right mr-2"></i> Pertanyaan Umum</a></li> 
                        @endif
                        <li><a href="{{ url('page/product/list') }}" class="text-foot"><i class="mdi mdi-chevron-right mr-2"></i> Daftar Produk</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mt-4 pt-2">
                    <h4 class="text-light text-uppercase footer-head">Tautan</h4>
                    <ul class="list-unstyled footer-list mt-4">
                        <li><a href="{{ url('auth/login') }}" class="text-foot"><i class="mdi mdi-chevron-right mr-2"></i> Masuk</a></li>
                        @if (website_config('main')->is_register_enabled)
                        <li><a href="{{ url('auth/register') }}" class="text-foot"><i class="mdi mdi-chevron-right mr-2"></i> Pendaftaran Baru</a></li>
                        @endif
                        @if (website_config('main')->is_reset_password_enabled)
                        <li><a href="{{ url('auth/reset') }}" class="text-foot"><i class="mdi mdi-chevron-right mr-2"></i> Atur Ulang Kata Sandi</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <footer class="footer footer-bar py-4 bg-dark text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <p class="mb-0 text-foot">{{ date('Y') }} © {{ website_config('main')->website_name }} <span class="d-none d-sm-inline-block"> - Crafted with <i class="mdi mdi-heart text-danger"></i> by {{ website_config('main')->meta_author }}.</span></p>
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ asset('assets/landing/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/scrollspy.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/owlcarousel.init.js') }}"></script>
    <script src="{{ asset('assets/landing/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/landing/js/magnific.init.js') }}"></script> 
    <script src="{{ asset('assets/landing/js/parallax.js') }}"></script>
    <script src="{{ asset('assets/landing/js/counter.init.js') }}"></script>
    <script src="{{ asset('assets/landing/js/switcher.js') }}"></script>
    <script src="{{ asset('assets/landing/js/app.js') }}"></script>
</body>
</html>