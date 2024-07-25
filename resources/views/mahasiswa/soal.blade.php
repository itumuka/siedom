@extends('layouts.master_sidebar') <!-- Extend from the desired layout -->

@section('content')
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soal{{  matkulData.nama_matakuliah || 'N/A' }}</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
        }

        .options {
            position: relative;
            padding-left: 40px;
            margin-bottom: 20px;
        }

        .options label {
            display: block;
            margin-bottom: 15px;
            font-size: 14px;
            cursor: pointer;
        }

        .options input {
            opacity: 0;
        }

        .checkmark {
            position: absolute;
            top: -1px;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #DDD;
            border: 1px solid #555;
            border-radius: 50%;
        }

        .options input:checked ~ .checkmark:after {
            display: block;
        }

        .options .checkmark:after {
            content: "";
            width: 10px;
            height: 10px;
            display: block;
            background: white;
            position: absolute;
            top: 50%;
            left: 50%;
            border-radius: 50%;
            transform: translate(-50%,-50%) scale(0);
            transition: 300ms ease-in-out 0s;
        }

        .options input[type="radio"]:checked ~ .checkmark {
            background: #0C1A32;
            transition: 300ms ease-in-out 0s;
        }

        .options input[type="radio"]:checked ~ .checkmark:after {
            transform: translate(-50%,-50%) scale(1);
        }
    </style>
</head>
<body>
    <div class="container mt-sm-5 my-1">
        <h1>Detail Matakuliah</h1>
        <div id="detail-content">
            <!-- Detail content will be injected here -->
        </div>
        <form id="evaluation-form">
            <input type="hidden" name="id_mreg" id="id_mreg" value="">
            <div id="evaluation-content">
                <!-- Evaluation content will be injected here -->
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var idKelas = localStorage.getItem('selectedMatkulId');
            var allMatkulData = JSON.parse(localStorage.getItem('allMatkulData')) || [];
            var idMhs = localStorage.getItem('selectedMhsId');
            var idMreg = localStorage.getItem('selectedMregId'); // Assuming this is stored in local storage

            console.log('Retrieved selected ID:', idKelas);
            console.log('Retrieved all data:', allMatkulData);
            console.log('Retrieved user ID:', idMhs);
            console.log('Retrieved id_mreg:', idMreg);

            if (idKelas && allMatkulData.length > 0) {
                var matkulData = allMatkulData.find(item => item.id_kelas === parseInt(idKelas, 10));

                if (matkulData) {
                    document.getElementById('detail-content').innerHTML = `
                        <p><strong>Matakuliah:</strong> ${matkulData.nama_matakuliah || 'N/A'}</p>
                        <p><strong>Kode:</strong> ${matkulData.kode_matakuliah || 'N/A'}</p>
                        <p><strong>Semester:</strong> ${matkulData.semester || 'N/A'}</p>
                        <p><strong>Dosen:</strong> ${matkulData.dosen || 'N/A'}</p>
                        <p><strong>Jam Mulai:</strong> ${matkulData.jam_mulai || 'N/A'}</p>
                        <p><strong>Jam Selesai:</strong> ${matkulData.jam_selesai || 'N/A'}</p>
                    `;
                } else {
                    document.getElementById('detail-content').innerHTML = '<p>Detail tidak ditemukan.</p>';
                }
            } else {
                document.getElementById('detail-content').innerHTML = '<p>Data tidak tersedia.</p>';
            }

            // Set the id_mreg in the hidden input
            document.getElementById('id_mreg').value = idMreg;

            // Fetch and display `komponen_penilaian` and `soal` data
            Promise.all([
                fetch("http://127.0.0.1:8000/get-komponen-penilaian").then(response => response.json()),
                fetch("http://127.0.0.1:8000/get-soal").then(response => response.json())
            ])
            .then(([komponenData, soalData]) => {
                console.log('Komponen Penilaian data:', komponenData);
                console.log('Soal data:', soalData);
                
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

                var evaluationContent = '';
                for (var komponenId in groupedSoal) {
                    var komponen = groupedSoal[komponenId];
                    evaluationContent += `<h4>${komponen.nama_komponen}</h4>`;
                    komponen.soal.forEach(soal => {
                        evaluationContent += `
                            <div class="form-group">
                                <label>${soal.pertanyaan}</label>
                                <div class="options">
                                    <label class="options"><input type="radio" name="soal_${soal.id_soal}" value="0"> Tidak Berlaku<span class="checkmark"></span></label>
                                    <label class="options"><input type="radio" name="soal_${soal.id_soal}" value="1"> Sangat Tidak Sesuai<span class="checkmark"></span></label>
                                    <label class="options"><input type="radio" name="soal_${soal.id_soal}" value="2"> Tidak Sesuai<span class="checkmark"></span></label>
                                    <label class="options"><input type="radio" name="soal_${soal.id_soal}" value="3"> Sesuai<span class="checkmark"></span></label>
                                    <label class="options"><input type="radio" name="soal_${soal.id_soal}" value="4"> Sangat Sesuai<span class="checkmark"></span></label>
                                </div>
                            </div>
                        `;
                    });
                }
                document.getElementById('evaluation-content').innerHTML = evaluationContent;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });

            // Handle form submission
            document.getElementById('evaluation-form').addEventListener('submit', function(event) {
                event.preventDefault();
                
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

                console.log('Collected answers:', answers);

                // Fetch CSRF token from meta tag
                var csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

                if (!csrfToken) {
                    console.error('CSRF token meta tag not found.');
                    return;
                }

                // Send the answers to the server
                answers.forEach(answer => {
                    fetch('http://127.0.0.1:8000/save-jawaban', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(answer)
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);
                        // You can add any success message or redirection here
                    })
                    .catch(error => {
                        console.error('Error submitting answer:', error);
                    });
                });
            });
        });
    </script>
</body>
</html>
@endsection
