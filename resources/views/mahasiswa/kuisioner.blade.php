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
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Kuisioner Mata Kuliah {{ Session::get('session_nama_tahunakademik') }}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <input class="form-control" type="hidden" name="nim" id="nim" value="{{ Session::get('session_nim') }}">
                    <input class="form-control" type="hidden" name="tahun" id="tahun" value="{{ Session::get('session_tahun') }}">
                    <input class="form-control" type="hidden" name="semester" id="semester" value="{{ Session::get('session_semester') }}">
                    <div class="table-responsive">
                        <table id="tbjadwalmakul" class="table table-hover table-sm text-nowrap" width="100%">
                            <thead class="bg-dark">
                                <tr>
                                    <th>Aksi</th>
                                    <th>Matakuliah</th>
                                    <th>Kode</th>
                                    <th>SMT</th>
                                    <th>Dosen</th>
                                </tr>
                            </thead>
                            <tbody>
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
        var token = "{{ Session::get('token') }}";
        var userlogin = "{{ Session::get('username') }}";
        var nim = $('#nim').val();
        var tahun = $('#tahun').val();
        var semester = $('#semester').val();
        var idMhs = "{{ Session::get('id_mhs') }}";
        var idMreg = "{{ Session::get('id_mreg') }}";
        var completedClasses = [];
        
        function initializeDataTable() {
            console.log('Initializing DataTable with completedClasses:', completedClasses);
        
            var table = $("#tbjadwalmakul").DataTable({
                destroy: true,
                processing: true,
                lengthChange: true,
                ajax: {
                    type: "GET",
                    url: "{{ config('setting.second_url') }}mahasiswa/tampil-presensi-makul",
                    headers: {
                        "Authorization": 'Bearer ' + token,
                        "username": userlogin
                    },
                    data: {
                        nim: nim,
                        tahun: tahun,
                        semester: semester
                    },
                    dataSrc: function(json) {
                        window.allMatkulData = json;
                        localStorage.setItem('allMatkulData', JSON.stringify(json));
                        return json;
                    }
                },
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            var rowIdKelas = String(row.id_kelas);
                            var isCompleted = completedClasses.includes(rowIdKelas);
        
                            var buttonClass = isCompleted ? 'btn-success' : 'btn-primary';
                            var buttonText = isCompleted ? 'Terisi' : 'Isi';
                            var classIsi = isCompleted ? '' : 'btn-detail';
        
                            console.log('Rendering row:', row);
                            console.log('row.id_kelas:', row.id_kelas, 'Type:', typeof row.id_kelas);
                            console.log('isCompleted:', isCompleted);
        
                            return `<button type="button" class="btn btn-sm ${buttonClass} ${classIsi}" data-id_kelas="${row.id_kelas}">${buttonText}</button>`;
                        }
                    },
                    { data: 'nama_matakuliah' },
                    { data: 'kode_matakuliah' },
                    { data: 'semester' },
                    { data: 'dosen' }
                ],
                order: []
            });
        }
        
        function fetchCompletedClasses() {
            return $.ajax({
                url: "{{ route('check.kuisioner.status') }}",
                type: "GET",
                data: { nim: nim },
                success: function(response) {
                    // Ensure the data type is consistent
                    completedClasses = response.completedClasses.map(String);
                    console.log('Completed Classes fetched:', completedClasses);
                },
                error: function(xhr) {
                    console.error('Failed to fetch completed classes');
                }
            });
        }
        
        fetchCompletedClasses().then(initializeDataTable).catch(function(error) {
            console.error('Error in fetchCompletedClasses or initializeDataTable:', error);
        });

        $(document).on('click', '.btn-detail', function(event) {
            event.preventDefault();
            var idKelas = $(this).data('id_kelas');
            var selectedMatkulData = {
                id_kelas: idKelas,
                nama_matakuliah: $(this).data('makul') || '',
                kode_matakuliah: $(this).data('kode') || '',
                semester: $(this).data('semester') || '',
                dosen: $(this).data('dosen') || ''
            };

            localStorage.setItem('selectedMatkulId', idKelas);
            localStorage.setItem('selectedMhsId', idMhs);
            localStorage.setItem('selectedMregId', idMreg);

            window.location.href = "/soal";
        });

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
    });


    </script>
@stop
