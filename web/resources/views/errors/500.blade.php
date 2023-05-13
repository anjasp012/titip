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
    <link href="{{ asset('assets/template/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/template/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/template/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body class="bg-primary bg-pattern">
    <div class="account-pages my-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="mt-4 text-center">
                        <div>
                            <img src="{{ asset('assets/template/images/error-img.png') }}" alt="" class="img-fluid mx-auto d-block">
                        </div>
                        <h3 class="mt-5 text-uppercase text-white mb-3">Terjadi Kesalahan</h3>
                        <h5 class="text-white-50">Kami mengalami masalah server internal, coba lagi nanti.</h5>
                        <div class="mt-5">
                            <a class="btn btn-success waves-effect waves-light" href="{{ url('/') }}">Kembali ke Halaman Utama</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/template/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/template/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/template/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/template/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/template/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/template/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/template/js/app.js') }}"></script>
</body>
</html>
