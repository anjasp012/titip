{{--
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta name="author" content="{{ website_config('main')->meta_author }}" />
    <meta name="keywords" content="{{ website_config('main')->meta_keywords }}" />
    <meta name="description" content="{{ website_config('main')->meta_description }}" />
    <title>{{ website_config('main')->website_name }}</title>
    <link rel="shortcut icon" href="{{ website_config('main')->website_favicon }}">
    <link href="{{ asset('assets/titiptugas/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/titiptugas/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/titiptugas/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/titiptugas/css/theme.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center min-vh-100">
                        <div class="w-100 d-block my-5">
                            <div class="row justify-content-center">
                                <div class="col-md-8 col-lg-5">
                                    <div class="card card-animate">
                                        <div class="card-body">
                                            @if (website_config('main')->website_logo <> null)
                                            <div class="text-center mb-4 mt-3">
                                                <a href="javascript:void(0);">
                                                    <span><img src="{{ website_config('main')->website_logo }}" alt="" height="50"></span>
                                                </a>
                                            </div>
                                            @else
                                            <div class="text-center mb-3 mt-3">
                                                <a href="javascript:void(0);" class="">
                                                    <span class="text-dark text-uppercase" style="font-size: 25px; font-weight: Bold;">
                                                        {{ website_config('main')->website_name }}
                                                    </span>
                                                </a>
                                            </div>
                                            @endif
                                            @include('admin.layouts.alert')
                                            @yield('content')
                                        </div>
                                    </div>
                                    @yield('another-page')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
    <script src="{{ asset('assets/titiptugas/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/titiptugas/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/titiptugas/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('assets/titiptugas/js/waves.js') }}"></script>
    <script src="{{ asset('assets/titiptugas/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/titiptugas/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/titiptugas/js/theme.js') }}"></script>
    @yield('script')
</body>
</html>
--}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="{{ website_config('main')->meta_author }}" />
    <meta name="keywords" content="{{ website_config('main')->meta_keywords }}" />
    <meta name="description" content="{{ website_config('main')->meta_description }}" />
    <title>{{ website_config('main')->website_name }}</title>
	<!-- Favicons -->
	<link href="assets/img/favicons.png" rel="icon">
	<link href="assets/img/apple-touch-icons.png" rel="apple-touch-icon">

	<!-- Google Fonts -->
	<link href="https://fonts.gstatic.com" rel="preconnect">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

	<!-- Vendor CSS Files -->
	<link href="{{ asset('assets/titiptugas/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/titiptugas/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/titiptugas/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/titiptugas/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/titiptugas/css/style.css')}}?v=<?= date("ymdhis") ?>" rel="stylesheet">
	<link href="{{ asset('assets/titiptugas/css/custom.css')}}?v=<?= date("ymdhis") ?>" rel="stylesheet">
   
	<link href="{{ asset('assets/titiptugas/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
			  @include('admin.layouts.alert')
              @yield('content')
              
            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
	<script src="{{ asset('assets/titiptugas/js/jquery.min.js') }}"></script>
	<script src="{{ asset('assets/titiptugas/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
	<script src="{{ asset('assets/titiptugas/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
	<script src="{{ asset('assets/titiptugas/js/main.js')}}"></script>
	@yield('script')

</body>

</html>
