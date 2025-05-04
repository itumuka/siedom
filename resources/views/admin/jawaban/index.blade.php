@extends('layouts.master_sidebar')

@section('title', 'Soal')

@section('css')
<style type="text/css">
    /* Add custom CSS styles here */
</style>
@stop

@section('content')
    <div class="container">
        <h1>Mahasiswa</h1>
        <!-- DataTable -->
        <div class="table-responsive">
            <table id="jawabanTable" class="table table-hover table-sm text-nowrap" width="100%">
                <thead class="bg-dark">
                    <tr>
                        <th>Aksi</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Tahun Angkatan</th>
                        <th>Program Studi</th>
                        <th>Total Kelas</th>
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
            var table = $("#jawabanTable").DataTable({
                destroy: true,
                processing: true,
                lengthChange: true,
                ajax: {
                    url: "{{ route('jawaban.data') }}",
                    type: "GET",
                    dataSrc:"data" 
                },
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return `
                            <button type="button" class="btn btn-sm btn-info btn-edit" onclick="window.location.href='/admin/jawaban/${row.id_mhs}'">
                                <i class="fa fa-edit"></i>
                            </button>
                            `;
                        }
                    },
                    { data: 'nim' },
                    { data: 'nama_mahasiswa' },
                    { data: 'tahun_ajaran' },
                    { data: 'nama_program_studi'},
                    { data: 'total_kelas'},
                    { data: 'total_jawaban'}
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
