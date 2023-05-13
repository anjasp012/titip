<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="{{ website_config('main')->meta_author }}" />
    <meta name="keywords" content="{{ website_config('main')->meta_keywords }}" />
    <meta name="description" content="{{ website_config('main')->meta_description }}" />
    <title>{{ website_config('main')->website_name }}</title>
    <link rel="shortcut icon" href="{{ website_config('main')->website_favicon }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/jvectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/'.website_config('template')->number.'/css/theme.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/custom-header.js') }}"></script>
</head>
<body>
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex align-items-left">
                    <button type="button" class="btn btn-sm mr-2 d-lg-none px-3 font-size-16 header-item waves-effect"
                        id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                </div>
                @if (Auth::guard('admin')->check() == true)
                <div class="d-flex align-items-center">
                    <div class="dropdown d-inline-block ml-2">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user"></i>
                            <span class="d-none d-sm-inline-block ml-1">
                                {{ Auth::guard('admin')->user()->full_name }}
                            </span>
                            <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ url('admin/auth/logout') }}">
                                <span>Keluar</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </header>
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <div class="navbar-brand-box">
                    <a href="{{ url('/') }}" class="logo">
                        {{ website_config('main')->website_name }}
                    </a>
                </div>
                <div id="sidebar-menu">
                    <ul class="metismenu list-unstyled" id="side-menu">
                        @include('admin.layouts.navigation')
                    </ul>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="modal fade" id="modal" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title mt-0" id="modal-title"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body" id="modal-detail-body">
                                </div>
                                <div class="modal-footer hide" id="modal-footer">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0 font-size-18">@yield('breadcrumb-first')</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">@yield('breadcrumb-second')</a></li>
                                        <li class="breadcrumb-item active d-none d-sm-inline-block">@yield('breadcrumb-first')</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        @hasSection('button')
                        <div class="col-lg-12" style="margin-bottom: 20px; margin-top: -10px;">
                            @yield('button')
                        </div>
                        @endif
                    </div>
                    @include('admin.layouts.alert')
                    @yield('content')
                </div> 
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            {{ date('Y') }} © {{ website_config('main')->website_name }}
                            <span class="d-none d-sm-inline-block"></span>
                        </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <div class="menu-overlay"></div>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/waves.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/simplebar.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/morris-js/morris.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/autonumeric/autoNumeric-min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/datatables/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/'.website_config('template')->number.'/js/theme.js') }}"></script>
    <script src="{{ asset('assets/custom-footer.js') }}"></script>
</body>
</html>
