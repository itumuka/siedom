@extends('layouts.master_sidebar')

@section('title','Detail Mahasiswa Jawaban')

@section('css')
<style type="text/css">
    .table-responsive {
        margin-top: 20px;
    }
</style>
@stop

@section('content')
<div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="page-title">Detail Mahasiswa Jawaban</h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="#">Mahasiswa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Jawaban</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-litle">Nama : {{ $mahasiswa['nama'] }}</h4>
                <h4 class="box-litle">NIM : {{ $mahasiswa['nim'] }}</h4>
                <h4 class="box-litle">Prodi : {{ $mahasiswa['nama_program_studi'] }}</h4>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <h5>Total Matakuliah diisi: {{ $total_matkul }}</h5>
                
                <div class="table-responsive">
                    <table id="detailJawabanTable" class="table table-hover table-sm text-nowrap" width="100%">
                        <thead class="bg-dark">
                            <tr>
                                <th>No.</th>
                                <th>Matakuliah</th>
                                <th>Total Jawaban</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail_jawaban as $index => $jawaban)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $jawaban->nama_matakuliah }}</td>
                                <td>{{ $jawaban->total_jawaban }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('script-master')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#detailJawabanTable').DataTable({
                "lengthChange": true,
                "autoWidth": false
            });
        });
    </script>
@stop
