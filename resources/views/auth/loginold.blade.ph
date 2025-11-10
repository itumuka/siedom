<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ URL::asset('images/favicon.ico') }}">

    <title> Log in </title>

    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ url('semidark/css/vendors_css.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ url('semidark/css/style.css') }}">
    <link rel="stylesheet" href="{{ url('semidark/css/skin_color.css') }}">
    <style>
        /* .bg-img{
        filter: blur(8px);
    } */
    </style>
</head>

<body class="hold-transition theme-primary bg-img" style="background-color: #172B4C;">
    {{-- <body class="hold-transition theme-primary bg-img"
    style="background-image: url(../imageup45/up45covercoklat.jpg);background-repeat: no-repeat;background-size: 100% 100%;"> --}}

    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100">

            <div class="col-12">
                <div class="row justify-content-center no-gutters">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="bg-white rounded30 shadow-lg">
                            <div class="content-top-agile p-20 pb-0">
                                <img src="{{ url('images/logo_siedom_login.png') }}" alt="User Image"
                                    class="h-100 align-self-end"><br><br>
                                {{-- <b class="mb-0">UNIVERSITAS MUHAMMADIYAH KARANGANYAR</b>
                                <h2 style="color: #172B4C;">Kuisioner Evaluasi</h2> --}}
                            </div>
                            <div class="p-40">
                                {{-- <form method="post"> --}}
                                <p class="notiferror"></p>
                                <div class="form-group">
                                    <div class="input-group mb-3" style="height: 50px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent"><i
                                                    class="fa fa-user "></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="username" id="username"
                                            placeholder="Username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-3" style="height: 50px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text  bg-transparent"><i
                                                    class="fa fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" name="password" id="password"
                                            placeholder="Password">
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- <div class="col-6">
										  <div class="checkbox">
											<input type="checkbox" id="basic_checkbox_1" >
											<label for="basic_checkbox_1">Remember Me</label>
										  </div>
										</div>
										<!-- /.col -->
										<div class="col-6">
										 <div class="fog-pwd text-right">
											<a href="javascript:void(0)" class="hover-warning"><i class="ion ion-locked"></i> Forgot pwd?</a><br>
										  </div>
										</div> --}}
                                    <!-- /.col -->
                                    <div class="col-12 text-center">
                                        <button type="button" id="login_enter" onclick="aksilogin();" class="btn"
                                            style="width: 100%;background-color:#172B4C;color:#fff;">Login</button>
                                    </div>
                                    <!-- /.col -->
                                    {{-- <a href="http://up45.ac.id">UP45</a>
                                    © 2023 --}}
                                </div>

                                {{-- </form> --}}
                                {{-- <div class="text-center">
									<p class="mt-15 mb-0">Don't have an account? <a href="auth_register.html" class="text-warning ml-5">Sign Up</a></p>
								</div> --}}
                            </div>
                        </div>
                        <br>
                        <div class="text-center">
                            <p><a class="mt-20 text-white" href="http://sia.umuka.ac.id">UMUKA © 2024</a></p>
                        </div>
                        {{-- <div class="text-center">
						  <p class="mt-20 text-white">- Sign With -</p>
						  <p class="gap-items-2 mb-20">
							  <a class="btn btn-social-icon btn-round btn-facebook" href="#"><i class="fa fa-facebook"></i></a>
							  <a class="btn btn-social-icon btn-round btn-twitter" href="#"><i class="fa fa-twitter"></i></a>
							  <a class="btn btn-social-icon btn-round btn-instagram" href="#"><i class="fa fa-instagram"></i></a>
							</p>	
						</div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Vendor JS -->
    <script src="{{ url('semidark/js/vendors.min.js') }}"></script>
    <script src="{{ url('semidark/js/pages/chat-popup.js') }}"></script>
    <script src="{{ url('assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ url('assets/vendor_components/jquery-toast-plugin-master/src/jquery.toast.js') }}"></script>
    <script>
        // $("#password").keypress(function(event) {
        //     if (event.keyCode === 13) {
        //         $("#login_enter").click();
        //     }
        // });
        function showToastr(type, title, message) {
            let body;
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


        $(document).keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                $("#login_enter").click();
            }
        });

        function startSpinner() {
            $("#login_enter").prop("disabled", true);
            $("#login_enter").html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
            );
        }

        function stopSpinner() {
            $("#login_enter").prop("disabled", false);
            $("#login_enter").html('<i class="fas fa-sync"></i> Login');
        }


        function aksilogin() {
            var username = $("input[name=username]").val();
            var password = $("input[name=password]").val();
            startSpinner();
            $.ajax({
                type: 'POST',
                url: "{{ config('setting.second_url') }}auth-login",
                data: {
                    username: username,
                    password: password
                },
                success: function(result) {
                    if (result.success == 'Pegawai') {
                         console.log(result.data);
                        $.ajax({
                            type: 'GET',
                            url: "{{ url('makesession-pegawai') }}",
                            data: {
                                username: username,
                                nama: result.data.nama,
                                jabatan: result.data.jabatan,
                                nm_module: result.data.nm_module,
                                kode_fakultas: result.data.kode_fakultas,
                                semester: result.smtta[0].semester,
                                tahun: result.smtta[0].tahun,
                                tahun_ajaran: result.smtta[0].tahun_ajaran,
                                token: result.token,
                                id_mreg: result.smtta[0].id_mreg
                            },
                            success: function(result) {
                                showToastr('success', 'Berhasil!', 'Berhasil Login');
                                document.location.href = "{{ url('/admin/dashboard') }}";
                            }
                        })
                    } else if (result.success == 'Mahasiswa') {
                        $.ajax({
                            type: 'GET',
                            url: "{{ url('makesession-mahasiswa') }}",
                            data: {
                                username: username,

                                id_mhs: result.data.id_mhs,
                                id_mreg: result.smtta[0].id_mreg,

                                nama: result.data.nama_mahasiswa,
                                gender: result.data.jenis_kelamin,
                                nim: result.data.nim,
                                kode_program_studi: result.data.kode_program_studi,
                                semester: result.smtta[0].semester,
                                tahun: result.smtta[0].tahun,
                                tahun_ajaran: result.smtta[0].tahun_ajaran,
                                token: result.token
                            },
                            success: function(result) {
                                showToastr('success', 'Berhasil!', 'Berhasil Login');
                                console.log(result);
                                document.location.href = "{{ route('dashboard') }}";
                            }
                        })

                    } else if (result.success == 'Dosen') {
                        $.ajax({
                            type: 'GET',
                            url: "{{ url('makesession-dosen') }}",
                            data: {
                                username: result.data.email_login,
                                // userlogin: result.data.email_login,
                                nama: result.data.nama_dosen,
                                kode_program_studi: result.data.kode_prodi,
                                dosen_wali: result.data.dosen_wali,
                                id_dosen: result.data.id_pegawai,
                                semester: result.smtta[0].semester,
                                tahun: result.smtta[0].tahun,
                                tahun_ajaran: result.smtta[0].tahun_ajaran,
                                token: result.token
                            },
                            success: function(result) {
                                console.log(result);
                                document.location.href = "{{ route('home') }}";
                            }
                        })

                    } else {
                        // notif({
                        // 	msg: result.message + ", Silahkan coba lagi!!",
                        // 	type: "error",
                        // 	position: "center",
                        // 	fade: true
                        // });
                        // console.log(result.error);
                        $(".notiferror").html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Gagal Login!! </strong><br> ' +
                            result.error +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                        );
                        // showToastr('error', 'Error!', result.error);
                    }
                    // console.log(result);
                    stopSpinner();
                }
            })
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

</body>

</html>
