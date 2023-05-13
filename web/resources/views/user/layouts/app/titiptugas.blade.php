

<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="{{ website_config('main')->meta_author }}" />
    <meta name="keywords" content="{{ website_config('main')->meta_keywords }}" />
    <meta name="description" content="{{ website_config('main')->meta_description }}" />
    <title>{{ website_config('main')->website_name }}</title>
	<meta name="google-site-verification" content="VasU3yCyK8Zueh1iyCLxB9IZzAntjjQdBrKb6I25vE0" />
    <meta content='all' name='audience' />
    <meta content='general' name='rating' />
    <meta content='all' name='robots' />
    <meta content='index,follow' name='robots' />
    <meta content='id' name='language' />
    <meta content='id' name='geo.country' />
    <meta content='global' name='distribution' />
    <meta content='1 days' name='revisit-after' />
    <meta content='Indonesia' name='geo.placename' />
    <meta property="og:title" content="{{ website_config('main')->website_name }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ request()->url() }}banners.png" />
    <meta property="og:url" content="{{ request()->url() }}" />
    <meta property="og:description" content='{{ website_config('main')->meta_description }}' />
    <meta property="og:site_name" content="{{ website_config('main')->website_name }}" />

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
    <link href="{{ asset('assets/titiptugas/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="{{ asset('assets/titiptugas/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/titiptugas/vendor/quill/quill.snow.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/titiptugas/css/style.css')}}?v=<?= date("ymdhis") ?>" rel="stylesheet">
	<link href="{{ asset('assets/titiptugas/css/custom.css')}}?v=<?= date("ymdhis") ?>" rel="stylesheet">
    <link href="{{ asset('assets/titiptugas/vendor/simple-datatables/style.css')}}" rel="stylesheet">
    <script src="{{ asset('assets/titiptugas/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/titiptugas/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-HSCNPV2NQW"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'G-HSCNPV2NQW');
	</script>

</head>

<body>
  @include('user.layouts.header')
  <main id="main" class="container">
    <div class="modal fade" id="modal" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content postmodal">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="modal-detail-body">
                </div>
                {{--<div class="modal-footer hide" id="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" >Tutup</button>
                </div>--}}
            </div>
        </div>
    </div>
    <section class="section">
      <div class="row dashboard">
        
		{{--
		@include('user.layouts.alert')
        --}}
		
        @yield('content')
        
      

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="mt-5">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          {{--<div class="col-lg-3 col-md-6 footer-links">
            <h4>Useful Links</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
            </ul>
          </div>
          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
            </ul>
          </div>--}}
          <div class="col-lg-3 col-md-6 footer-contact">
            <h4>Contact Us</h4>
            <p> Jl. Raya Juanda, <br> Sidoarjo<br> Jawa Timur <br><br> <strong>Phone:</strong> 0878.2299.2662<br> <strong>Email:</strong> info@titiptugas.com<br> </p>
          </div>
          <div class="col-lg-3 col-md-6 footer-info">
            <h3>About Titip Tugas</h3>
            <p>website untuk solusi tugas sekolah maupun kuliah. Kalian bisa menanyakan semua soal pelajaran di sekolah</p>
            <div class="social-links mt-3"> <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a> <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a> <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a> <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="copyright"> © Copyright <strong><span>Titip Tugas</span></strong>. All Rights Reserved </div>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>
  

  <script src="{{ asset('assets/titiptugas/plugins/alertify/alertify.min.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
  <script src="{{ asset('assets/titiptugas/plugins/select2/select2.min.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/plugins/autonumeric/autoNumeric-min.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/plugins/datatables/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/plugins/datatables/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/titiptugas/js/main.js')}}"></script>
  <script>
    /**
   * Initiate Datatables
   */
  const datatables = select('.datatable', true)
  datatables.forEach(datatable => {
    new simpleDatatables.DataTable(datatable);
  })
  </script>
  @yield('script')


</body>

</html>
