@extends('layouts.master_sidebar')

@section('title', 'Detail Evaluasi Kelas')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    .detail-wrapper {
        padding: 4px 12px 30px;
    }

    /* Action Nav Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* Class Profile Banner */
    .class-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .class-banner h2 {
        font-weight: 700;
        font-size: 22px;
        margin: 0 0 6px 0;
        color: #ffffff;
    }
    .class-banner .meta-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }
    .meta-chip {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .lecturer-chip {
        background: #ffffff;
        color: #1e3a8a;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .lecturer-chip .avatar-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    /* KPI Cards */
    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .kpi-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    .kpi-primary::before { background: #2563eb; }
    .kpi-success::before { background: #10b981; }
    .kpi-info::before { background: #0ea5e9; }
    .kpi-warning::before { background: #f59e0b; }

    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        float: right;
    }
    .kpi-primary .kpi-icon { background: #eff6ff; color: #2563eb; }
    .kpi-success .kpi-icon { background: #ecfdf5; color: #10b981; }
    .kpi-info .kpi-icon { background: #f0f9ff; color: #0ea5e9; }
    .kpi-warning .kpi-icon { background: #fffbeb; color: #f59e0b; }

    .kpi-label {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .kpi-value {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 4px;
    }
    .kpi-sub {
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    /* Content Cards */
    .dashboard-box {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }
    .dashboard-box .box-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dashboard-box .box-header h5 {
        font-size: 15px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dashboard-box .box-body {
        padding: 20px;
    }

    /* Table Styles */
    .dist-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        border-top: none;
    }
    .dist-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 10px 12px;
    }
    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        font-size: 12.5px;
    }
    .cat-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .mini-progress {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 4px;
    }
    .mini-progress-bar {
        height: 100%;
        border-radius: 4px;
    }

    .badge-chip {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .chip-primary { background: #e0f2fe; color: #0369a1; }
    .chip-success { background: #dcfce7; color: #15803d; }
    .chip-warning { background: #fef3c7; color: #b45309; }

    .methodology-note {
        font-size: 11.5px;
        color: #64748b;
        background: #f8fafc;
        border-left: 3px solid #3b82f6;
        padding: 10px 14px;
        border-radius: 0 6px 6px 0;
        margin-top: 14px;
        line-height: 1.5;
    }

    .no-data-card {
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px dashed #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="detail-wrapper">
    <!-- Action Bar -->
    <div class="action-bar">
        <div>
            <a href="{{ route('kelas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Kelas
            </a>
        </div>
        <div>
            <a href="{{ route('kelas.soal.overview', $id_kelas) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-list-check mr-1"></i> Analisis Per Butir Soal
            </a>
        </div>
    </div>

    <!-- Class Profile Banner -->
    <div class="class-banner">
        <div>
            <div class="meta-tags mb-2">
                <span class="meta-chip"><i class="fas fa-barcode"></i> <span id="matakuliah-kode">Memuat...</span></span>
                <span class="meta-chip"><i class="fas fa-door-open"></i> <span id="kelas-nama">-</span></span>
                <span class="meta-chip"><i class="fas fa-graduation-cap"></i> <span id="prodi-nama">-</span></span>
                <span class="meta-chip"><i class="fas fa-calendar-alt"></i> Semester <span id="semester-nama">-</span></span>
            </div>
            <h2 id="matakuliah-nama">Memuat Informasi Mata Kuliah...</h2>
        </div>
        <div>
            <div class="lecturer-chip">
                <div class="avatar-icon"><i class="fas fa-chalkboard-user"></i></div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">Dosen Pengampu</div>
                    <div id="dosen-nama" style="font-size: 14px; font-weight: 700; color: #0f172a;">-</div>
                    <div id="dosen-nip" style="font-size: 11.5px; color: #64748b; font-weight: normal;">NIP: -</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Executive KPI Cards -->
    <div class="row">
        <!-- KPI 1: Indeks Evaluasi Kelas -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-primary">
                <div class="kpi-icon"><i class="fas fa-award"></i></div>
                <div class="kpi-label">Indeks Evaluasi Kelas</div>
                <div class="kpi-value" id="kpi-score">0.00 <span style="font-size: 13px; font-weight: normal; color: #64748b;">/ 4.00</span></div>
                <p class="kpi-sub">
                    <span class="badge-chip chip-primary" id="kpi-percent">0.00% Capaian</span>
                    <span id="kpi-predikat" class="font-weight-bold" style="color: #2563eb;">-</span>
                </p>
            </div>
        </div>
        <!-- KPI 2: Responden Mahasiswa -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-success">
                <div class="kpi-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="kpi-label">Mahasiswa Responden</div>
                <div class="kpi-value" id="kpi-students">0</div>
                <p class="kpi-sub"><i class="fas fa-check-circle text-success"></i> Mahasiswa telah mengisi</p>
            </div>
        </div>
        <!-- KPI 3: Total Butir Jawaban -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-info">
                <div class="kpi-icon"><i class="fas fa-list-check"></i></div>
                <div class="kpi-label">Total Butir Jawaban</div>
                <div class="kpi-value" id="kpi-responses">0</div>
                <p class="kpi-sub">
                    <span id="kpi-valid-detail">0 Valid</span> &bull; <span id="kpi-na-detail" style="color: #94a3b8;">0 N/A</span>
                </p>
            </div>
        </div>
        <!-- KPI 4: Status Keterwakilan -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-warning">
                <div class="kpi-icon"><i class="fas fa-scale-balanced"></i></div>
                <div class="kpi-label">Status Keterwakilan</div>
                <div class="kpi-value" id="kpi-status-text" style="font-size: 18px; line-height: 1.5;">Evaluasi Aktif</div>
                <p class="kpi-sub" id="kpi-status-sub">Berdasarkan partisipasi kelas</p>
            </div>
        </div>
    </div>

    <!-- Charts & Breakdown Table Section -->
    <div class="row">
        <!-- Visualisasi Donut Chart -->
        <div class="col-lg-5">
            <div class="dashboard-box">
                <div class="box-header">
                    <h5><i class="fas fa-chart-pie text-primary"></i> Proporsi Jawaban Mahasiswa</h5>
                </div>
                <div class="box-body" id="chart-card-body">
                    <div id="basic-pie" style="height: 330px; width:100%;"></div>
                </div>
            </div>
        </div>
        <!-- Tabel Distribusi Mutu -->
        <div class="col-lg-7">
            <div class="dashboard-box">
                <div class="box-header">
                    <h5><i class="fas fa-table-list text-info"></i> Rincian Distribusi & Frekuensi Skala</h5>
                </div>
                <div class="box-body">
                    <div id="table-container" class="table-responsive">
                        <!-- Diisi via JavaScript -->
                    </div>
                    <div class="methodology-note">
                        <i class="fas fa-circle-info text-primary mr-1"></i>
                        <strong>Catatan Metodologi:</strong> Nilai rata-rata indeks kelas dihitung murni dari jawaban valid (Skala 1 sampai 4). Jawaban <em>"0 - Tidak Berlaku" (N/A)</em> dipisahkan dan diexclude dari pembagi rata-rata agar tidak menurunkan skor kelas secara keliru.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
<script type="text/javascript">
var pieChart = null;

var categoryConfig = {
    0: { color: '#94a3b8', label: 'Tidak Berlaku (N/A)' },
    1: { color: '#ef4444', label: 'Sangat Tidak Sesuai' },
    2: { color: '#f59e0b', label: 'Tidak Sesuai' },
    3: { color: '#3b82f6', label: 'Sesuai' },
    4: { color: '#10b981', label: 'Sangat Sesuai' }
};

function getPredikat(score) {
    if (score >= 3.50) return { label: 'Sangat Memuaskan', color: '#10b981' };
    if (score >= 3.00) return { label: 'Memuaskan', color: '#2563eb' };
    if (score >= 2.00) return { label: 'Cukup', color: '#f59e0b' };
    return { label: 'Kurang / Perlu Pembinaan', color: '#ef4444' };
}

function fetchChartData() {
    $.ajax({
        url: "{{ url('/admin/chart/data/jawaban-kelas/' . $id_kelas) }}",
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            // 1. Render Informasi Master Kelas (selalu ada)
            if (response.kelas_info) {
                var info = response.kelas_info;
                $('#matakuliah-nama').text(info.nama_matakuliah || 'Mata Kuliah');
                $('#matakuliah-kode').text(info.kode_matakuliah || '-');
                $('#kelas-nama').text(info.nama_kelas || 'Kelas');
                $('#prodi-nama').text(info.nama_program_studi || '-');
                $('#semester-nama').text(info.semester || '-');
                $('#dosen-nama').text(info.nama_dosen || 'Dosen Belum Ditentukan');
                $('#dosen-nip').text('NIP: ' + (info.nip_dosen || '-'));
            }

            // 2. Render KPI Summary
            var avgScore = response.avg_score || 0;
            var percent = response.percent || 0;
            var predikat = getPredikat(avgScore);
            var totalResponses = response.total_responses || 0;
            var totalStudents = response.total_students || 0;
            var totalValid = response.total_valid || 0;
            var totalNa = response.total_na || 0;

            $('#kpi-score').html(avgScore.toFixed(2) + ' <span style="font-size: 13px; font-weight: normal; color: #64748b;">/ 4.00</span>');
            $('#kpi-percent').text(percent.toFixed(2) + '% Capaian');
            $('#kpi-predikat').text(predikat.label).css('color', predikat.color);
            $('#kpi-students').text(totalStudents.toLocaleString('id-ID'));
            $('#kpi-responses').text(totalResponses.toLocaleString('id-ID'));
            $('#kpi-valid-detail').text(totalValid.toLocaleString('id-ID') + ' Valid');
            $('#kpi-na-detail').text(totalNa.toLocaleString('id-ID') + ' N/A');

            if (totalStudents >= 10) {
                $('#kpi-status-text').html('<span class="text-success"><i class="fas fa-check-double mr-1"></i> Sangat Representatif</span>');
                $('#kpi-status-sub').text('Jumlah sampel responden memadai (≥ 10)');
            } else if (totalStudents >= 3) {
                $('#kpi-status-text').html('<span class="text-primary"><i class="fas fa-check mr-1"></i> Cukup Memadai</span>');
                $('#kpi-status-sub').text('Memenuhi kuorum minimal (≥ 3)');
            } else {
                $('#kpi-status-text').html('<span class="text-warning"><i class="fas fa-hourglass-half mr-1"></i> Partisipasi Rendah</span>');
                $('#kpi-status-sub').text('Kurang dari 3 mahasiswa mengisi');
            }

            // 3. Render Chart & Table
            var answers = [0, 0, 0, 0, 0];
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(item) {
                    var key = Number(item.jawaban);
                    if (key >= 0 && key <= 4) {
                        answers[key] = Number(item.count || 0);
                    }
                });
            }

            if (totalResponses === 0) {
                showNoDataMessage();
            } else {
                renderCharts(answers);
                populateTable(answers, totalResponses);
            }
        },
        error: function(xhr) {
            console.error('Error fetching data:', xhr);
            showNoDataMessage();
        }
    });
}

function renderCharts(answers) {
    var chartDom = document.getElementById('basic-pie');
    if (!chartDom) return;

    if (!pieChart) {
        pieChart = echarts.init(chartDom);
        window.addEventListener('resize', function() {
            pieChart.resize();
        });
    }

    var chartColors = ['#94a3b8', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];

    var option = {
        tooltip: {
            trigger: 'item',
            formatter: '{b}<br/><b>{c} respon</b> ({d}%)'
        },
        legend: {
            bottom: '0%',
            left: 'center',
            itemWidth: 10,
            itemHeight: 10,
            textStyle: { fontSize: 11 }
        },
        color: chartColors,
        series: [{
            name: 'Jawaban Kelas',
            type: 'pie',
            radius: ['45%', '72%'],
            center: ['50%', '46%'],
            avoidLabelOverlap: true,
            itemStyle: {
                borderRadius: 6,
                borderColor: '#ffffff',
                borderWidth: 2
            },
            label: {
                show: false
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 12,
                    fontWeight: 'bold',
                    formatter: '{d}%'
                }
            },
            data: [
                { value: answers[0], name: categoryConfig[0].label },
                { value: answers[1], name: categoryConfig[1].label },
                { value: answers[2], name: categoryConfig[2].label },
                { value: answers[3], name: categoryConfig[3].label },
                { value: answers[4], name: categoryConfig[4].label }
            ]
        }]
    };

    pieChart.setOption(option);
}

function populateTable(answers, totalResponses) {
    var tableHtml = '<table class="table dist-table table-hover mb-0"><thead><tr>' +
        '<th>Kategori Evaluasi</th>' +
        '<th class="text-right">Frekuensi</th>' +
        '<th style="width: 35%;">Proporsi Jawaban</th>' +
        '</tr></thead><tbody>';

    var keys = [0, 1, 2, 3, 4];
    keys.forEach(function(key) {
        var count = answers[key] || 0;
        var cfg = categoryConfig[key];
        var pct = totalResponses > 0 ? (count / totalResponses * 100) : 0;

        tableHtml += `<tr>
            <td>
                <span class="cat-pill">
                    <span class="cat-dot" style="background: ${cfg.color};"></span>
                    <span>${cfg.label}</span>
                </span>
            </td>
            <td class="text-right font-weight-bold">${count.toLocaleString('id-ID')}</td>
            <td>
                <div class="d-flex align-items-center justify-content-between" style="font-size: 11.5px;">
                    <span class="font-weight-bold" style="color: ${cfg.color};">${pct.toFixed(2)}%</span>
                </div>
                <div class="mini-progress">
                    <div class="mini-progress-bar" style="width: ${pct}%; background: ${cfg.color};"></div>
                </div>
            </td>
        </tr>`;
    });

    // Baris Total Akumulatif (100% Persentase Distribusi)
    tableHtml += `<tr class="table-light font-weight-bold" style="border-top: 2px solid #cbd5e1;">
        <td><strong>Total Respons Terkumpul</strong></td>
        <td class="text-right"><strong>${totalResponses.toLocaleString('id-ID')}</strong></td>
        <td>
            <div class="d-flex align-items-center justify-content-between" style="font-size: 11.5px;">
                <span class="text-success font-weight-bold">100.00%</span>
                <span class="text-muted" style="font-size: 10.5px;">Akumulatif</span>
            </div>
            <div class="mini-progress">
                <div class="mini-progress-bar bg-success" style="width: 100%;"></div>
            </div>
        </td>
    </tr>`;

    tableHtml += '</tbody></table>';
    $('#table-container').html(tableHtml);
}

function showNoDataMessage() {
    $('#chart-card-body').html(`
        <div class="no-data-card">
            <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i>
            <h5 class="text-dark font-weight-bold mb-1">Belum Ada Jawaban Masuk</h5>
            <p class="mb-0 text-muted" style="font-size: 13px;">Mahasiswa pada kelas ini belum mengisi kuesioner evaluasi pembelajaran.</p>
        </div>
    `);
    $('#table-container').html(`
        <div class="no-data-card">
            <i class="fas fa-table-list fa-3x mb-3 text-secondary d-block"></i>
            <h5 class="text-dark font-weight-bold mb-1">Data Distribusi Kosong</h5>
            <p class="mb-0 text-muted" style="font-size: 13px;">Tabel frekuensi dan proporsi jawaban akan tampil otomatis saat respon mahasiswa masuk.</p>
        </div>
    `);
}

$(document).ready(function() {
    fetchChartData();
});
</script>
@endsection
