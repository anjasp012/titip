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
<body>
    <div class="account-pages my-3 w-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center">
                        <div class="">
                            <a href="javascript:void(0);" class="logo text-uppercase" style="font-size: 20px; font-weight: Bold;">
                                <span clas="text-uppercase">{{ website_config('main')->website_name }}</span>
                            </a>
                        </div>
                        <h4 class="">Situs sedang dalam pengembangan</h4>
                        <p>Jangan khawatir, kami akan segera kembali.</p>
                    </div>
                </div>
            </div>
            <div class="row pt-4 align-items-center justify-content-center">
                <div class="col-sm-5">
                    <div class="">
                        <img src="{{ asset('assets/template/images/maintenance.png') }}" alt="" class="img-fluid mx-auto d-block">
                    </div>
                </div>
                <div class="col-lg-6 ml-lg-auto">
                    <div class="mt-5 mt-lg-0">
                        <div class="card maintenance-box">
                            <div class="card-body p-4">
                                <div class="media">
                                    <div class="avatar-xs mr-3">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            01
                                        </span>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="font-size-15 text-uppercase">Apakah anda membutuhkan bantuan?</h5>
                                        <p class="text-muted mb-0">
                                            @if (website_config('socials')->whatsapp)
                                                <a href="{{ sprintf('https://wa.me/%s', website_config('socials')->whatsapp) }}" target="_blank" class="text-muted"><i class="mdi mdi-whatsapp"></i> Whatsapp: {{ website_config('socials')->whatsapp }}</a><br />
                                            @endif
                                            @if (website_config('socials')->telegram)
                                                <a href="{{ sprintf('https://wa.me/%s', website_config('socials')->telegram) }}" target="_blank" class="text-muted"><i class="mdi mdi-telegram"></i> Telegram: {{ website_config('socials')->telegram }}</a><br />
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
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
