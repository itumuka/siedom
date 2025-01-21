@extends('layouts.master_sidebar')

@section('title', 'Soal')

@section('css')
<style type="text/css">
    /* Add custom CSS styles here */
</style>
@stop

@section('content')
    <div class="container">
        <h1>Soal</h1>

        <!-- Create/Update Form -->
        <div class="clearfix">
            <button type="button" class="waves-effect waves-light btn btn-rounded btn-primary mb-5" id="add-btn">Add Soal</button>
            <button type="button" class="waves-effect waves-light btn btn-rounded btn-secondary mb-5" id="duplicate-btn">Duplikat Soal</button>
        </div>
        

        <!-- Modal for Create/Update -->
        <div class="modal fade" id="soalModal" tabindex="-1" role="dialog" aria-labelledby="soalModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="soalModalLabel">Add/Edit Soal</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" id="soal-id">
                            <textarea class="form-control" id="soal-pertanyaan" placeholder="Pertanyaan"></textarea>
                            <select class="form-control mt-2" id="soal-komponen">
                                <option value="">Pilih Komponen Penilaian</option>
                            </select>

                            <select class="form-control mt-2" id="soal-mreg">
                                <option value="">Pilih Tahun Akademik</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning me-1"  id="cancel-btn" data-bs-dismiss="modal">
                            <i class="ti-trash"></i> Cancel
                          </button>
                          <button type="button" class="btn btn-primary" id="save-btn">
                            <i class="ti-save-alt" id="save-btn"></i> Save
                          </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Duplikat -->
        <div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog" aria-labelledby="duplicateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="duplicateModalLabel">Duplikat Soal</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="source-academic-year">Tahun Akademik Asal</label>
                            <select class="form-control" id="source-academic-year">
                                <option value="">Pilih Tahun Akademik</option>
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="target-academic-year">Tahun Akademik Tujuan</label>
                            <select class="form-control" id="target-academic-year">
                                <option value="">Pilih Tahun Akademik</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal"><i class="ti-trash"></i> Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirm-duplicate-btn"><i class="ti-save-alt" id="save-btn"></i> Duplikat</button>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- DataTable -->
        <div class="table-responsive">
            <table id="soalTable" class="table table-hover table-sm text-nowrap" width="100%">
                <thead class="bg-dark">
                    <tr>
                        <th>Aksi</th>
                        <th>Pertanyaan</th>
                        <th>Komponen Penilaian</th>
                        <th>Tahun Akademik</th>
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
            var table = $("#soalTable").DataTable({
                destroy: true,
                processing: true,
                lengthChange: true,
                ajax: {
                    url: "{{ route('soal.data') }}",
                    type: "GET",
                    dataSrc:"data" 
                },
                columns: [
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return `
                            <button type="button" class="btn btn-danger me-1 btn-delete" data-id="${row.id_soal}">
                        <i class="ti-trash"></i>
                    </button>
                    <button type="button" class="btn btn-info btn-edit" data-id="${row.id_soal}">
                        <i class="fa fa-edit"></i>
                    </button>
                            `;
                        }
                    },
                    { data: 'pertanyaan' },
                    { data: 'nama_komponen' },
                    { data: 'tahun_ajaran'}
                ],
                order: []
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function loadKomponenOptions(callback) {
        $.ajax({
            url: "{{ route('komponen-penilaian.data') }}",
            type: 'GET'
        }).done(function(response) {
            var select = $("#soal-komponen");
            select.empty();
            select.append('<option value="">Pilih Komponen Penilaian</option>');
            response.data.forEach(function(item) {
                select.append(`<option value="${item.id_komponen_penilaian}">${item.nama_komponen}</option>`);
            });

            if (callback) callback();
        }).fail(function(xhr) {
            alert('Gagal memuat data komponen penilaian');
        });
    }

    function loadMregOptions(callback) {
        $.ajax({
            url: "{{ route('mreg.data') }}",
            type: 'GET'
        }).done(function(response) {
            var select = $("#soal-mreg");
            select.empty();
            select.append('<option value="">Pilih Tahun Akademik</option>');
            response.data.forEach(function(item) {
                select.append(`<option value="${item.id_mreg}">${item.tahun_ajaran}</option>`);
            });

            if (callback) callback();
        }).fail(function(xhr) {
            alert('Gagal memuat data tahun akademik');
        });
    }


            $("#cancel-btn").click(function() {
                $("#komponenModal").modal('hide');
            });

            $("#add-btn").click(function() {
                $("#soal-id").val('');
                $("#soal-pertanyaan").val('');
                $("#soal-komponen").val('');
                $("#soalModalLabel").text('Add Soal');
                $("#soalModal").modal('show');
                loadKomponenOptions();
                loadMregOptions();
            });


            $("#save-btn").click(function() {
                var id = $("#soal-id").val();
                var pertanyaan = $("#soal-pertanyaan").val();
                var id_komponen_penilaian = $("#soal-komponen").val();
                var id_mreg = $("#soal-mreg").val();

                if (id) {
                    // Update existing record
                    $.ajax({
                        url: "{{ url('/admin/soal') }}/" + id,
                        type: 'PUT',
                        data: { pertanyaan: pertanyaan, id_komponen_penilaian: id_komponen_penilaian, id_mreg: id_mreg },
                        success: function(response) {
                            table.ajax.reload();
                            $("#soalModal").modal('hide');
                            showToastr('success', 'Berhasil!', 'Data Berhasil Diperbarui');
                        },
                        error: function(xhr) {
                            showToastr('error', 'Error!', 'Gagal');
                        }
                    });
                } else {
                    // Create new record
                    $.ajax({
                        url: "{{ route('soal.store') }}",
                        type: 'POST',
                        data: { pertanyaan: pertanyaan, id_komponen_penilaian: id_komponen_penilaian, id_mreg: id_mreg },
                        success: function(response) {
                            table.ajax.reload();
                            $("#soalModal").modal('hide');
                            showToastr('success', 'Berhasil!', 'Data Berhasil Ditambahkan');
                        },
                        error: function(xhr) {
                            showToastr('error', 'Error!', 'Gagal');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ url('/admin/soal') }}/" + id,
                type: 'GET',
                success: function(response) {
            loadKomponenOptions(function() {
                loadMregOptions(function() {
                    $("#soal-id").val(response.data.id_soal);
                    $("#soal-pertanyaan").val(response.data.pertanyaan);
                    $("#soal-komponen").val(response.data.id_komponen_penilaian);
                    $("#soal-mreg").val(response.data.id_mreg);
                    $("#soalModalLabel").text('Edit Soal');
                    $("#soalModal").modal('show');
                });
            });
        },
                error: function(xhr) {
                    alert('Gagal mendapatkan data');
                }
            });
        });
        
        $("#duplicate-btn").click(function() {
    $("#source-academic-year").val('');
    $("#target-academic-year").val('');
    loadMregOptions(function() {
        $("#duplicateModal").modal('show');
    });
});

    // Duplikat
    $('#duplicateModal').on('show.bs.modal', function() {
        $.ajax({
            url: "{{ route('mreg.data') }}", // Pastikan ini rute yang sama dengan Add Soal
            type: "GET",
            success: function(response) {
                var options = '<option value="">Pilih Tahun Akademik</option>';
                response.data.forEach(function(item) {
                    options += `<option value="${item.id_mreg}">${item.tahun_ajaran}</option>`;
                });

                // Set options untuk dropdown di modal duplikasi
                $('#source-academic-year').html(options);
                $('#target-academic-year').html(options);
            },
            error: function(xhr) {
                console.error('Error memuat data tahun akademik:', xhr.responseJSON.message);
            }
        });
    });


    $("#confirm-duplicate-btn").click(function() {
        var sourceYear = $("#source-academic-year").val();
        var targetYear = $("#target-academic-year").val();

        if (!sourceYear || !targetYear) {
            alert('Silakan pilih tahun akademik asal dan tujuan');
            return;
        }

        $.ajax({
            url: "{{ route('soal.duplicate') }}",
            type: 'POST',
            data: {
                sourceYear: sourceYear,
                targetYear: targetYear,
            },
            success: function(response) {
                table.ajax.reload(); // Reload tabel setelah duplikasi
                $("#duplicateModal").modal('hide');
                showToastr('success', 'Berhasil!', response.message);
            },
            error: function(xhr) {
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menduplikat soal';
                showToastr('error', 'Error!', errorMessage);
            }
        });
    });



            // Handle delete button click
            $(document).on('click', '.btn-delete', function() {
                if (confirm('Yakin ingin menghapus data ini?')) {
                    var id = $(this).data('id');
                    $.ajax({
                        url: "{{ url('/admin/soal') }}/" + id,
                        type: 'DELETE',
                        success: function(response) {
                            table.ajax.reload();
                            showToastr('success', 'Berhasil!', 'Data Berhasil Dihapus');
                        },
                        error: function(xhr) {
                            showToastr('error', 'Error!', 'Gagal');
                        }
                    });
                }
            });
        });
    </script>
@endsection
