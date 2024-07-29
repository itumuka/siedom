@extends('layouts.master_sidebar')

@section('title', 'Kuisioner')

@section('css')
<style type="text/css">
    .container {
        text-align: left;
    }

    .pagination-controls {
        margin-top: 20px;
        text-align: left;
    }
</style>
@stop

@section('content')
    <div class="container mt-sm-5 my-1">
        <h1>Kuesioner Evaluasi Dosen Oleh Mahasiswa</h1>

        <div class="box box-bordered border-primary">
            <div class="box-body">
                <div id="detail-content"></div>
            </div>
          </div>
        <form id="evaluation-form">
            <input type="hidden" name="id_mreg" id="id_mreg" value="">
			<div class="box">
                <div class="box-header with-border">
                  <h4 class="box-title"><strong>Isi Kuesioner</strong></h4>
                </div>
                <div class="box-body">
                    <div id="evaluation-content"></div>
                </div>
              </div>


            <div class="pagination-controls">
                <button type="button" class="btn btn-warning" id="prev-button">
                    <i class=""></i> Previous
                </button>
                <button type="submit" class="btn btn-success" id="next-button">
                    <i class=""></i> Next
                </button>
                <button type="submit" class="btn btn-primary" id="submit-button" style="display: none;">
                    <i class="ti-save-alt"></i> Submit
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script-master')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    var idKelas = localStorage.getItem('selectedMatkulId');
    var allMatkulData = JSON.parse(localStorage.getItem('allMatkulData')) || [];
    var idMhs = localStorage.getItem('selectedMhsId');
    var idMreg = localStorage.getItem('selectedMregId');

    if (idKelas && allMatkulData.length > 0) {
        var matkulData = allMatkulData.find(item => item.id_kelas === parseInt(idKelas, 10));
        document.getElementById('detail-content').innerHTML = `
            <p><strong>Matakuliah:</strong> ${matkulData ? matkulData.nama_matakuliah : 'N/A'}</p>
            <p><strong>Dosen:</strong> ${matkulData ? matkulData.dosen : 'N/A'}</p>
            <p><strong>Keterangan Penilaian : </strong></p>
            1 = Sangat Tidak Sesuai : bila kenyataan di lapangan sangat tidak sesuai dengan komponen yang dinilai.<br>
            2 = Tidak Sesuai : bila kenyataan di lapangan kurang sesuai dengan komponen yang dinilai<br>
            3 = Sesuai : bila kenyataan di lapangan cukup sesuai dengan komponen yang dinilai.<br>
            4 = Sangat Sesuai : bila kenyataan di lapangan sangat sesuai dengan komponen yang dinilai.<br>
            0 = Tidak Belaku : bila saudara tidak pernah dibimbing oleh dosen yang bersangkutan<br><br>
        `;
    } else {
        document.getElementById('detail-content').innerHTML = '<p>Data tidak tersedia.</p>';
    }

    document.getElementById('id_mreg').value = idMreg;

    Promise.all([
        fetch("/get-komponen-penilaian").then(response => response.json()),
        fetch("/get-soal").then(response => response.json())
    ])
    .then(([komponenData, soalData]) => {
        var groupedSoal = {};
        soalData.forEach(item => {
            if (!groupedSoal[item.id_komponen_penilaian]) {
                groupedSoal[item.id_komponen_penilaian] = {
                    nama_komponen: komponenData.find(comp => comp.id_komponen_penilaian === item.id_komponen_penilaian)?.nama_komponen || 'Unknown',
                    soal: []
                };
            }
            groupedSoal[item.id_komponen_penilaian].soal.push(item);
        });

        var questionsPerPage = 5;
        var currentPage = 1;
        var totalPages = Math.ceil(soalData.length / questionsPerPage);

        function renderQuestions(page) {
            var evaluationContent = '';
            var questionCounter = 1;
            var startIndex = (page - 1) * questionsPerPage;
            var endIndex = Math.min(page * questionsPerPage, soalData.length);

            var questionCounter = startIndex + 1;

            var paginatedSoal = soalData.slice(startIndex, endIndex);

            paginatedSoal.forEach(soal => {
                var komponen = groupedSoal[soal.id_komponen_penilaian];
                evaluationContent += `
                    <div class="form-group">
                        <label>${questionCounter}. ${soal.pertanyaan}</label>
                        <div class="form-group ichack-input">
                            <input type="radio" name="soal_${soal.id_soal}" id="radio_${soal.id_soal}_0" value="0" required>
                            <label for="radio_${soal.id_soal}_0">Tidak Berlaku</label><br> 
                            <input type="radio" name="soal_${soal.id_soal}" id="radio_${soal.id_soal}_1" value="1">
                            <label for="radio_${soal.id_soal}_1">Sangat Tidak Sesuai</label><br> 
                            <input type="radio" name="soal_${soal.id_soal}" id="radio_${soal.id_soal}_2" value="2">
                            <label for="radio_${soal.id_soal}_2">Tidak Sesuai</label><br> 
                            <input type="radio" name="soal_${soal.id_soal}" id="radio_${soal.id_soal}_3" value="3">
                            <label for="radio_${soal.id_soal}_3">Sesuai</label><br> 
                            <input type="radio" name="soal_${soal.id_soal}" id="radio_${soal.id_soal}_4" value="4">
                            <label for="radio_${soal.id_soal}_4">Sangat Sesuai</label><br> 
                        </div>
                    </div>
                `;
                questionCounter++;
            });

            document.getElementById('evaluation-content').innerHTML = evaluationContent;
            document.getElementById('submit-button').style.display = (page === totalPages) ? '' : 'none';
            document.getElementById('next-button').style.display = (page === totalPages) ? 'none' : 'inline-block';
        }

        function updatePaginationControls() {
            document.getElementById('prev-button').disabled = (currentPage === 1);
            document.getElementById('next-button').disabled = (currentPage === totalPages);
        }

        function checkAllAnswered() {
            var allAnswered = true;
            document.querySelectorAll('#evaluation-content .form-group').forEach(function(group) {
                var radios = group.querySelectorAll('input[type="radio"]');
                var oneChecked = false;
                radios.forEach(function(radio) {
                    if (radio.checked) {
                        oneChecked = true;
                    }
                });
                if (!oneChecked) {
                    allAnswered = false;
                }
            });
            return allAnswered;
        }

        renderQuestions(currentPage);
        updatePaginationControls();

        document.getElementById('prev-button').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderQuestions(currentPage);
                updatePaginationControls();
            }
        });

        document.getElementById('next-button').addEventListener('click', function() {
            if (!checkAllAnswered()) {
                showToastr('error', 'Gagal!', 'Harap Isi Semua Jawaban');
                return;
            }
            if (currentPage < totalPages) {
                currentPage++;
                renderQuestions(currentPage);
                updatePaginationControls();
            }
        });
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

    document.getElementById('evaluation-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var submitButton = document.getElementById('submit-button');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Submitting...';

        var formData = new FormData(this);
        var answers = [];

        formData.forEach((value, key) => {
            if (key.startsWith('soal_')) {
                answers.push({
                    id_soal: key.split('_')[1],
                    user_id: idMhs,
                    id_mreg: idMreg,
                    id_kelas: idKelas,
                    jawaban: value
                });
            }
        });

        var csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        if (!csrfToken) {
            console.error('CSRF token meta tag not found.');
            return;
        }

        fetch('/submit-jawaban', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ answers })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(JSON.stringify(data.errors));
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response from server:', data);
            showToastr('success', 'Berhasil!', 'Jawaban Berhasil Disimpan');
                setTimeout(function() {
                window.location.href = '/home';
            }, 4000);
        })
        .catch(error => {
            console.error('Error submitting answers:', error);
            showToastr('error', 'Error!', 'Error Saat Menyimpan Jawaban');
            submitButton.disabled = false;
            submitButton.innerHTML = 'Submit';
        });
    });

});
</script>
@endsection
