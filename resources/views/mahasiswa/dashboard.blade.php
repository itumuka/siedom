@extends('layouts.master_sidebar')

@section('title','Dashboard')

@section('css')
<style type="text/css">
</style>
@stop

@section('content')
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">{{ $title }}</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item" aria-current="page">{{ $parent_breadcrumb }}</li>
                                <li class="breadcrumb-item active" aria-current="page"></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            {{-- <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Selamat Datang {{ Session::get('nama') }}</h3>
                </div>
            </div> --}}
            <div class="col-12">
                <div class="box pull-up">
                    <div class="box-body bg-img bg-primary-light">
                        <div class="d-lg-flex align-items-center justify-content-between">
                            <div class="d-lg-flex align-items-center mb-30 mb-xl-0 w-p100">
                                <img src="../images/svg-icon/color-svg/custom-14.svg" class="img-fluid max-w-250" alt="" />
                                <div class="ms-30">
                                    <h2 class="mb-10">Selamat Datang {{ Session::get('nama') }}</h2>
                                    <p class="mb-0 text-fade fs-18">Di Sistem Informasi Evaluasi Dosen Oleh Mahasiswa (SIEDOM), lakukan Penilaian Kuisioner Mata Kuliah {{ Session::get('session_nama_tahunakademik') }} </p>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('home') }}" class="waves-effect waves-light w-p100 btn btn-primary btn-lg" style="white-space: nowrap;">Start Now!</a>
                            </div>
                        </div>							
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('script-master')
@stop
