@extends('layouts.master_sidebar')

@section('title', 'Dashboard Evaluasi Dosen')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    .dashboard-wrapper {
        padding: 4px 12px 30px;
    }

    /* Header Banner */
    .exec-header {
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
    .exec-header h2 {
        font-weight: 700;
        font-size: 22px;
        margin: 0;
        color: #ffffff;
    }
    .exec-header p {
        margin: 4px 0 0 0;
        opacity: 0.85;
        font-size: 13.5px;
    }
    .ta-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
<div class="dashboard-wrapper">
    <!-- Header Banner -->
    <div class="exec-header">
        <div>
            <h2><i class="fas fa-chalkboard-user mr-2"></i> Dashboard Evaluasi Dosen (EDOM)</h2>
            <p>Selamat datang, <strong>{{ Session::get('nama') ?: 'Bapak/Ibu Dosen' }}</strong> &bull; Rekapitulasi Evaluasi Pembelajaran oleh Mahasiswa</p>
        </div>
        <div>
            <span class="ta-badge">
                <i class="fas fa-calendar-check"></i>
                <span>Periode: <strong>{{ $tahun_ajaran ?: 'Tahun Akademik Aktif' }} ({{ $semester ?: 'Semester Aktif' }})</strong></span>
            </span>
        </div>
    </div>

    <!-- Alert Session Belum Aktif -->
    @if(!$tahun_ajaran)
    <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-triangle-exclamation mr-2"></i>
        <strong>Perhatian:</strong> Sesi Tahun Akademik belum diset. Silakan pilih tahun akademik di navbar atas agar rekapitulasi data tampil optimal.
    </div>
    @endif

    <!-- 4 KPI Metric Cards -->
    <div class="row">
        <!-- KPI 1: Indeks Mutu Dosen Keseluruhan -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-primary">
                <div class="kpi-icon"><i class="fas fa-award"></i></div>
                <div class="kpi-label">Indeks Rata-rata Saya</div>
                <div class="kpi-value" id="kpi-score">0.00 <span style="font-size: 13px; font-weight: normal; color: #64748b;">/ 4.00</span></div>
                <p class="kpi-sub">
                    <span class="badge-chip chip-primary" id="kpi-percent">0.00% Capaian</span>
                    <span id="kpi-predikat" class="font-weight-bold" style="color: #2563eb;">-</span>
                </p>
            </div>
        </div>
        <!-- KPI 2: Total Mahasiswa Penilai -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-success">
                <div class="kpi-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="kpi-label">Mahasiswa Responden</div>
                <div class="kpi-value" id="kpi-students">0</div>
                <p class="kpi-sub"><i class="fas fa-check-circle text-success"></i> Mahasiswa menilai kelas Anda</p>
            </div>
        </div>
        <!-- KPI 3: Total Respon Jawaban -->
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
        <!-- KPI 4: Kelas Diampu -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-warning">
                <div class="kpi-icon"><i class="fas fa-door-open"></i></div>
                <div class="kpi-label">Kelas Perkuliahan</div>
                <div class="kpi-value" id="kpi-classes">0</div>
                <p class="kpi-sub"><i class="fas fa-book-open text-warning"></i> Kelas diampu periode ini</p>
            </div>
        </div>
    </div>

    <!-- Charts & Breakdown Table Section -->
    <div class="row">
        <!-- Visualisasi Donut Chart -->
        <div class="col-lg-5">
            <div class="dashboard-box">
                <div class="box-header">
                    <h5><i class="fas fa-chart-pie text-primary"></i> Proporsi Respon Mahasiswa</h5>
                </div>
                <div class="box-body" id="chart-card-body">
                    <div id="general-chart" style="height: 330px; width: 100%;"></div>
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
                    <div id="pie-explanation" class="table-responsive">
                        <!-- Diisi via JavaScript -->
                    </div>
                    <div class="methodology-note">
                        <i class="fas fa-circle-info text-primary mr-1"></i>
                        <strong>Catatan Metodologi:</strong> Indeks evaluasi dihitung secara murni dari jawaban valid (Skala 1 s/d 4). Jawaban <em>"0 - Tidak Berlaku" (N/A)</em> dipisahkan dan diexclude dari pembagi rata-rata agar tidak menurunkan skor Anda secara keliru.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Kelas yang Diampu Beserta Skornya -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-box">
                <div class="box-header">
                    <h5><i class="fas fa-chalkboard text-primary"></i> Evaluasi Kinerja Per Kelas Perkuliahan</h5>
                    <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11.5px;">
                        <i class="fas fa-layer-group mr-1"></i> Semester Ini
                    </span>
                </div>
                <div class="box-body">
                    <div class="table-responsive" id="kelas-table-container">
                        <!-- Diisi via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
<script>
var generalChart = null;

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
    return { label: 'Kurang / Perlu Perhatian', color: '#ef4444' };
}

function fetchDashboardData() {
    $.get('/dosen/dashboard/data', function(res) {
        // 1. Update KPI Summary
        updateKpiCards(res);

        // 2. Render Donut Chart & Explanation Table
        if (res.total === 0) {
            showNoDataMessage();
        } else {
            renderGeneralChart(res.pieData);
            renderPieExplanation(res.pieData, res.total);
        }

        // 3. Render Kelas Table
        renderKelasTable(res.kelasList);
    }).fail(function() {
        showNoDataMessage();
    });
}

function updateKpiCards(res) {
    var score = res.overall_avg || 0;
    var percent = res.overall_percent || 0;
    var predikat = getPredikat(score);

    $('#kpi-score').html(score.toFixed(2) + ' <span style="font-size: 13px; font-weight: normal; color: #64748b;">/ 4.00</span>');
    $('#kpi-percent').text(percent.toFixed(2) + '% Capaian');
    $('#kpi-predikat').text(predikat.label).css('color', predikat.color);

    $('#kpi-students').text(Number(res.total_mhs || 0).toLocaleString('id-ID'));
    $('#kpi-responses').text(Number(res.total || 0).toLocaleString('id-ID'));
    $('#kpi-valid-detail').text(Number(res.total_valid || 0).toLocaleString('id-ID') + ' Valid');
    $('#kpi-na-detail').text(Number(res.total_na || 0).toLocaleString('id-ID') + ' N/A');
    $('#kpi-classes').text(Number(res.total_kelas || 0).toLocaleString('id-ID'));
}

function renderGeneralChart(pieData) {
    var chartDom = document.getElementById('general-chart');
    if (!chartDom) return;

    if (!generalChart) {
        generalChart = echarts.init(chartDom);
        window.addEventListener('resize', function() {
            generalChart.resize();
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
            name: 'Distribusi EDOM',
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
            data: pieData.map(function(item) {
                return {
                    value: item.value,
                    name: item.name
                };
            })
        }]
    };

    generalChart.setOption(option);
}

function renderPieExplanation(pieData, total) {
    var html = '<table class="table dist-table table-hover mb-0"><thead><tr>' +
        '<th>Kategori Evaluasi</th>' +
        '<th class="text-right">Frekuensi</th>' +
        '<th style="width: 35%;">Proporsi Jawaban</th>' +
        '</tr></thead><tbody>';

    pieData.forEach(function(item) {
        var key = item.key !== undefined ? item.key : 0;
        var cfg = categoryConfig[key] || { color: '#64748b', label: item.name };
        var pct = Number(item.percentage || 0);

        html += `<tr>
            <td>
                <span class="cat-pill">
                    <span class="cat-dot" style="background: ${cfg.color};"></span>
                    <span>${item.name}</span>
                </span>
            </td>
            <td class="text-right font-weight-bold">${Number(item.value).toLocaleString('id-ID')}</td>
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
    html += `<tr class="table-light font-weight-bold" style="border-top: 2px solid #cbd5e1;">
        <td><strong>Total Respons Terkumpul</strong></td>
        <td class="text-right"><strong>${Number(total || 0).toLocaleString('id-ID')}</strong></td>
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

    html += '</tbody></table>';
    $('#pie-explanation').html(html);
}

function renderKelasTable(kelasList) {
    var container = $('#kelas-table-container');
    if (!kelasList || kelasList.length === 0) {
        container.html(`
            <div class="no-data-card">
                <i class="fas fa-inbox fa-3x mb-2 text-secondary d-block"></i>
                <h5 class="text-dark font-weight-bold">Belum Ada Kelas yang Terdaftar</h5>
                <p class="text-muted mb-0">Tidak ada mata kuliah atau kelas yang diampu pada periode tahun akademik ini.</p>
            </div>
        `);
        return;
    }

    var html = '<table class="table table-hover align-middle mb-0"><thead class="table-light"><tr>' +
        '<th style="width: 5%;">#</th>' +
        '<th>Mata Kuliah & Kode</th>' +
        '<th>Kelas</th>' +
        '<th>Program Studi</th>' +
        '<th class="text-center">Responden</th>' +
        '<th class="text-center">Rata-rata Skor</th>' +
        '<th class="text-center">Persentase</th>' +
        '<th class="text-center" style="width: 15%;">Aksi</th>' +
        '</tr></thead><tbody>';

    kelasList.forEach(function(item, idx) {
        var scoreText = item.avg_score > 0 ? item.avg_score.toFixed(2) : '-';
        var percentText = item.avg_score > 0 ? (item.percent.toFixed(1) + '%') : '-';
        var scoreClass = item.avg_score >= 3.0 ? 'text-success font-weight-bold' : (item.avg_score >= 2.0 ? 'text-warning font-weight-bold' : 'text-danger font-weight-bold');

        html += `<tr>
            <td class="text-muted">${idx + 1}</td>
            <td>
                <div class="font-weight-bold text-dark">${item.nama_matakuliah}</div>
                <small class="text-muted"><i class="fas fa-barcode mr-1"></i>${item.kode_matakuliah}</small>
            </td>
            <td><span class="badge badge-light border font-weight-bold text-primary">${item.nama_kelas}</span></td>
            <td><small class="text-muted">${item.nama_program_studi || '-'}</small></td>
            <td class="text-center font-weight-bold">
                <span class="badge-chip chip-primary"><i class="fas fa-users"></i> ${item.total_mhs}</span>
            </td>
            <td class="text-center ${scoreClass}">${scoreText} <small class="text-muted font-weight-normal">/ 4.00</small></td>
            <td class="text-center">
                <span class="badge badge-pill ${item.avg_score >= 3.0 ? 'badge-success' : (item.avg_score >= 2.0 ? 'badge-warning' : 'badge-secondary')}">${percentText}</span>
            </td>
            <td class="text-center">
                <a href="/dosen/kelas/detail/${item.id_kelas}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Detail Hasil Evaluasi Kelas">
                    <i class="fas fa-chart-pie mr-1"></i> Detail
                </a>
                <a href="/dosen/kelas/${item.id_kelas}/soal" class="btn btn-sm btn-outline-secondary py-1 px-2 ml-1" title="Analisis Per Butir Soal">
                    <i class="fas fa-list"></i>
                </a>
            </td>
        </tr>`;
    });

    html += '</tbody></table>';
    container.html(html);
}

function showNoDataMessage() {
    $('#chart-card-body').html(`
        <div class="no-data-card">
            <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i>
            <h5 class="text-dark font-weight-bold mb-1">Belum Ada Jawaban Masuk</h5>
            <p class="mb-0 text-muted" style="font-size: 13px;">Mahasiswa belum mengisi kuesioner evaluasi pada periode ini.</p>
        </div>
    `);
    $('#pie-explanation').html(`
        <div class="no-data-card">
            <i class="fas fa-table-list fa-3x mb-3 text-secondary d-block"></i>
            <h5 class="text-dark font-weight-bold mb-1">Data Distribusi Kosong</h5>
            <p class="mb-0 text-muted" style="font-size: 13px;">Tabel frekuensi dan proporsi jawaban akan tampil otomatis saat respon masuk.</p>
        </div>
    `);
}

$(document).ready(function() {
    fetchDashboardData();
});
</script>
@endsection