<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="UMUKA">
    <meta name="author" content="IT UMUKA">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::asset('images/logo.png') }}">

    <title>@yield('title')</title>

    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ URL::asset('template/css/vendors_css.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ URL::asset('template/css/style.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('template/css/skin_color.css') }}">
    <link href='https://fonts.googleapis.com/css?family=Courgette' rel='stylesheet'>

    <style>
        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 4px 10px;
            /* e.g. change 8x to 4px here */
        }

        .bg-video {
            /* position: relative;
   height: 945px;
   width: 1530px;
   border-bottom: none;
   background-position: center;
   -webkit-background-size: cover;
   background-size: cover;
   background-repeat: no-repeat;
   z-index: 0; */
            position: relative;
            padding-bottom: 56.25%;
            padding-top: 30px;
            height: 0;
            overflow: hidden;
        }
    </style>
    @yield('css')

</head>

<body class="theme-primary">
    <header class="top-bar">
        <div class="topbar">

            <div class="container">
                <div class="row justify-content-end">
                    <div class="col-lg-6 col-12 d-lg-block d-none">
                        <div class="topbar-social text-center text-md-start topbar-left">

                        </div>
                    </div>
                    <div class="col-lg-6 col-12 xs-mb-10">
                        <div class="topbar-call text-right text-lg-end topbar-right">

                            <ul class="list-inline d-lg-flex justify-content-end">
                                <li class="ms-10 ps-10"><a href="#"><i
                                            class="text-white fa fa-envelope d-md-inline-block d-none"></i>
                                        humas@umuka.ac.id</a></li>
                                <li class="ms-10 ps-10"><a href="#"><i
                                            class="text-white fa fa-phone d-md-inline-block d-none"></i> 0271 6498851 |
                                        0271 4993819</a></li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav hidden class="nav-white nav-transparent">
            <div class="nav-header">
                <a href="index.html" class="brand">
                    <img src="{{ URL::asset('images/logo_umuka_dark.png') }}" alt="" />
                </a>
                <button class="toggle-bar">
                    <span class="ti-menu"></span>
                </button>
            </div>

            <ul class="menu">
                <li class="{{ Route::is('home') ? 'active' : '' }}">
                    <b><a href="{{ route('home') }}">BERANDA</a></b>
                </li>
                <li class="dropdown">
                    <b><a href="#">TENTANG</a></b>
                    <ul class="dropdown-menu">
                        <li class="{{ Route::is('profil_show') ? 'active' : '' }}"><a
                                href="{{ route('profil_show') }}">Profil</a></li>
                        <li class="{{ Route::is('campus_show') ? 'active' : '' }}"><a
                                href="{{ route('campus_show') }}">Kampus & Lokasi</a></li>
                        <li class="{{ Route::is('struktur_show') ? 'active' : '' }}"><a
                                href="{{ route('struktur_show') }}">Struktural</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <b><a href="#">Belajar di UMUKA</a></b>
                    <ul class="dropdown-menu">
                        <li class="{{ Route::is('prodi_show') ? 'active' : '' }}"><a href="{{ route('prodi_show') }}">Prodi</a></li>
                        <li class="{{ Route::is('sarjana_show') ? 'active' : '' }}"><a href="{{ route('sarjana_show') }}">Sarjana</a></li>
                        <li class="{{ Route::is('diploma_show') ? 'active' : '' }}"><a href="{{ route('diploma_show') }}">Diploma</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <b><a href="#">Kehidupan Kampus</a></b>
                    <ul class="dropdown-menu">
                        <li><a href="#">Fasilitas</a></li>
                        <li><a href="#">Kegiatan Mahasiswa</a></li>
                    </ul>
                </li>
                {{-- <li class="dropdown">
                    <b><a href="#">AKADEMIK</a></b>
                    <ul class="dropdown-menu">
                        <li class="dropdown">
                            <a href="#">Fakultas Sains dan Teknologi</a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Program Studi S1 Informatika</a></li>
                                <li><a href="#">Program Studi S1 Teknik Komputer</a></li>
                                <li><a href="#">Program Studi S1 Fisioterapi</a></li>
                                <li><a href="#">Program Studi S1 Peternakan</a></li>
                                <li><a href="#">Program Studi D3 Porduksi Ternak</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#">Fakultas Komunikasi dan Bisnis</a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Program Studi S1 Ilmu Komunikasi</a></li>
                                <li><a href="#">Program Studi S1 Akuntansi</a></li>
                                <li><a href="#">Program Studi S1 Bisnis Digital</a></li>
                                <li><a href="#">Program Studi D3 Perhotelan</a></li>
                                <li><a href="#">Program Studi D3 Bina Wisata</a></li>
                                <li><a href="#">Program Studi D3 Sekretari</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}

                <li>
                    <b><a href="https://pmb.umuka.ac.id/">ADMISI</a></b>
                </li>
            </ul>
        </nav>
    </header>

    @yield('content')

    <footer class="footer_three">
        <div class="footer-top bg-dark3 pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="widget">
                            <h4 class="footer-title">Hubungi Kami</h4>
                            <hr class="bg-primary mb-10 mt-0 d-inline-block mx-auto w-60">
                            <ul class="list list-unstyled mb-30">
                                <li> <i class="fa fa-map-marker"></i> Jl. Raya Solo-Tawangmangu Km 12, Papahan, Kec.
                                    Tasikmadu, Kabupaten Karanganyar, Jawa Tengah 57722 </li>
                                <li> <i class="fa fa-phone"></i> <span> (0271) 6498851 </span><br><span>(0271) 4993819
                                    </span></li>
                                <li> <i class="fa fa-envelope"></i>
                                    <span>humas@umuka.ac.id</span><br><span>admin@umuka.ac.id</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="widget widget_gallery clearfix">
                            <h4 class="footer-title">Galeri</h4>
                            <hr class="bg-primary mb-10 mt-0 d-inline-block mx-auto w-60">
                            <ul class="list-unstyled" id="data_gambar">
                                {{-- @foreach ($gallery as $gal)
                                    
                                
                                <li><img src="{{ URL::asset('/uploadfile_image/' . $gal->gambar) }}" alt=""></li>
                                @endforeach --}}
                                {{-- <li><img src="{{ URL::asset('images/gallery/thumb/2.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/3.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/4.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/5.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/6.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/7.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/8.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/9.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/10.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/11.jpg') }}" alt=""></li>
                                <li><img src="{{ URL::asset('images/gallery/thumb/12.jpg') }}" alt=""></li> --}}
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="widget">
                            <h4 class="footer-title">Link Terkait</h4>
                            <hr class="bg-primary mb-10 mt-0 d-inline-block mx-auto w-60">
                            <ul class="list-unstyled">
                                <li><a href="#">PMB UMUKA</a></li>
                                <li><a href="#">SIA UMUKA</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="by-1 bg-dark3 py-10 border-dark">
            <div class="container">
                <div class="text-center footer-links">
                    <a href="#" class="btn btn-link">BERANDA</a>
                    <a href="#" class="btn btn-link">TENTANG</a>
                    <a href="#" class="btn btn-link">AKADEMIK</a>
                    <a href="#" class="btn btn-link">ADMISI</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom bg-dark3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 col-12 text-md-start text-center"> <span class="text-white">All Rights
                            Reserved. © 2022 Universitas Muhammadiyah Karanganyar</span></div>
                    <div class="col-md-6 mt-md-0 mt-20">
                        <div class="social-icons">
                            <ul class="list-unstyled d-flex gap-items-1 justify-content-md-end justify-content-center">
                                <li><a href="#"
                                        class="waves-effect waves-circle btn btn-social-icon btn-circle btn-facebook"><i
                                            class="fa fa-facebook"></i></a></li>
                                <li><a href="#"
                                        class="waves-effect waves-circle btn btn-social-icon btn-circle btn-twitter"><i
                                            class="fa fa-twitter"></i></a></li>
                                <li><a href="#"
                                        class="waves-effect waves-circle btn btn-social-icon btn-circle btn-linkedin"><i
                                            class="fa fa-linkedin"></i></a></li>
                                <li><a href="#"
                                        class="waves-effect waves-circle btn btn-social-icon btn-circle btn-youtube"><i
                                            class="fa fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Vendor JS -->
    <script src="{{ URL::asset('template/js/vendors.min.js') }}"></script>
    <!-- Corenav Master JavaScript -->
    <script src="{{ URL::asset('template/corenav-master/coreNavigation-1.1.3.js') }}"></script>
    <script src="{{ URL::asset('template/js/nav.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}">
    </script>
    <script src="{{ URL::asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}">
    </script>
    <script src="{{ URL::asset('assets/vendor_components/OwlCarousel2/dist/owl.carousel.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor_components/jquery-toast-plugin-master/src/jquery.toast.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <!-- EduAdmin front end -->
    <script src="{{ URL::asset('template/js/template.js') }}"></script>

    <script type="text/javascript">
        function showToastr(type, title, message) {
            $.toast({
                heading: title,
                text: message,
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: type,
                hideAfter: 3500,
                stack: 6
            });
        }
        function data_gambar() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('gambar.data') }}",
                    success: function(result) {
                        var jml = result.length;
                        var s = '';
                        
                        for (i = 0; i < jml; i++) {
                            img = result[i].gambar;
                            //judul = judul.replace(/ /g, "+");
                            
                            s = s + '<li><img src="{{ URL::asset('uploadfile_image') }}/' + img + '" alt="' + img + '"></li>';
                        }
                        // console.log(result);
                        $('#data_gambar').html(s);

                    }
                })
            }

            data_gambar();
    </script>

    @yield('page-script')
</body>

</html>
