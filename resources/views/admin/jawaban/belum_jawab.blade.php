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
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Tahun Angkatan</th>
                        <th>Program Studi</th>
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
                    url: "{{ route('jawaban.belum.data') }}",
                    type: "GET",
                    dataSrc: "data"
                },
                columns: [
                    { data: 'nim' },
                    { data: 'nama_mahasiswa' },
                    { data: 'tahun_ajaran' },
                    { data: 'nama_program_studi' }
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
