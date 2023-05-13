
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
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/css/theme.min.css') }}" rel="stylesheet" type="text/css" />
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
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/waves.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/theme.js') }}"></script>
    @yield('script')
</body>
</html>
