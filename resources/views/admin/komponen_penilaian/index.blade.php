@extends('layouts.master_sidebar')

@section('title', 'Komponen Penilaian')

@section('css')
<style type="text/css">
    /* Add custom CSS styles here */
</style>
@stop

@section('content')
    <div class="container">
        <h1>Komponen Penilaian</h1>

        <!-- Create/Update Form -->
        <div class="clearfix">
        <button type="button" class="waves-effect waves-light btn btn-rounded btn-primary mb-5" id="add-btn">Add Komponen</button>
        </div>
        <!-- Modal for Create/Update -->
        <div class="modal fade" id="komponenModal" tabindex="-1" role="dialog" aria-labelledby="komponenModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="komponenModalLabel">Add Kompone Penilaian</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" id="komponen-id">
                            <input type="text" class="form-control" id="komponen-nama" placeholder="Nama Komponen">
                        </div>
                    </div>
                    <div class="modal-footer">
                            <button type="button" class="btn btn-warning me-1"  id="cancel-btn">
                              <i class="ti-trash"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="save-btn">
                              <i class="ti-save-alt" id="save-btn"></i> Save
                            </button>
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="save-btn">Save</button> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="table-responsive">
            <table id="komponenPenilaianTable" class="table table-hover table-sm text-nowrap" width="100%">
                <thead class="bg-dark">
                    <tr>
                        <th>Aksi</th>
                        <th>Nama Komponen Penilaian</th>
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
    var table = $("#komponenPenilaianTable").DataTable({
        destroy: true,
        processing: true,
        lengthChange: true,
        ajax: {
            url: "{{ route('komponen-penilaian.data') }}",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            {
                data: null,
                render: function(data, type, row, meta) {
                    return `
                    <button type="button" class="btn btn-danger me-1 btn-delete" data-id="${row.id_komponen_penilaian}">
                        <i class="ti-trash"></i>
                    </button>
                    <button type="button" class="btn btn-info btn-edit" data-id="${row.id_komponen_penilaian}">
                        <i class="fa fa-edit"></i>
                    </button>
                    `;
                }
            },
            { data: 'nama_komponen' }
        ],
        order: []
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Show add modal
    $("#add-btn").click(function() {
        $("#komponen-id").val('');
        $("#komponen-nama").val('');
        $("#komponenModalLabel").text('Add Komponen Penilaian');
        $("#komponenModal").modal('show');
    });

    $("#cancel-btn").click(function() {
        $("#komponenModal").modal('hide');
    });

    $("#save-btn").click(function() {
        var id = $("#komponen-id").val();
        var nama = $("#komponen-nama").val();

        if (id) {
            $.ajax({
                url: "{{ url('/admin/komponen-penilaian') }}/" + id,
                type: 'PUT',
                data: { nama_komponen: nama },
                success: function(response) {
                    table.ajax.reload();
                    $("#komponenModal").modal('hide');
                    showToastr('success', 'Berhasil!', 'Data Berhasil Diperbarui');
                },
                error: function(xhr) {
                    showToastr('error', 'Error!', 'Gagal');
                }
            });
        } else {
            $.ajax({
                url: "{{ route('komponen-penilaian.store') }}",
                type: 'POST',
                data: { nama_komponen: nama },
                success: function(response) {
                    table.ajax.reload();
                    $("#komponenModal").modal('hide');
                    showToastr('success', 'Berhasil!', 'Data Berhasil Ditambahkan');
                },
                error: function(xhr) {
                    showToastr('error', 'Error!', 'Gagal');
                }
            });
        }
    });

    // Handle edit button click
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ url('/admin/komponen-penilaian') }}/" + id,
            type: 'GET',
            success: function(response) {
                $("#komponen-id").val(response.data.id_komponen_penilaian);
                $("#komponen-nama").val(response.data.nama_komponen);
                $("#komponenModalLabel").text('Edit Komponen Penilaian');
                $("#komponenModal").modal('show');
            },
            error: function(xhr) {
                alert('Gagal mendapatkan data');
            }
        });
    });

    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: "{{ url('/admin/komponen-penilaian') }}/" + id,
                type: 'DELETE',
                success: function(response) {
                    table.ajax.reload();
                    showToastr('success', 'Deleted!', 'Data Berhasil Dihapus');
                },
                error: function(xhr) {
                    var error = JSON.parse(xhr.responseText);
                    showToastr('error', 'Error!', 'Gagal');
                }
            });
        }
    });
});

    </script>
@endsection
