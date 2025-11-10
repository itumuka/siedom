@extends('layouts.master_sidebar')

@section('title', 'Kelas Overview')

@section('css')
    <style type="text/css">
    </style>
@stop

@section('content')
    <div class="container">
        <h1>Daftar Kelas</h1>
        <!-- DataTable -->
        <div class="table-responsive">
            <table id="kelasTable" class="table table-hover table-sm text-nowrap" width="100%">
                <thead class="bg-dark">
                    <tr>
                        <th>Aksi</th>
                        <th>Kode Mata Kuliah</th>
                        <th>Mata Kulah</th>
                        <th>Nama Dosen</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>Total Mahasiswa</th>
                        <th>Total Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script-master')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $("#kelasTable").DataTable({
                destroy: true,
                processing: true,
                lengthChange: true,
                ajax: {
                    url: "{{ route('kelas.data') }}",
                    type: "GET",
                    dataSrc: "data" 
                },
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return `
                            <button type="button" class="btn btn-sm btn-info" onclick="window.location.href='/admin/kelas/detail/${row.id_kelas}'">
                                <i class="fa fa-eye"></i>
                            </button>
                            `;
                        }
                    },
                    { data: 'kode_matakuliah' },
                    { data: 'nama_matakuliah' },
                    { data: 'nama' },
                    { data: 'nama_program_studi' },
                    { data: 'smt_matakuliah' ,
                    className: 'text-center'},
                    { data: 'total_mahasiswa',
                    className: 'text-center' },
                    { data: 'total_jawaban',
                    className: 'text-center' }
                ],
                order: []
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection
