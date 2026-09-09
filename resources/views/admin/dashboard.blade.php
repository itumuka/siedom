@extends('layouts.master_sidebar')

@section('title', 'Dashboard Eksekutif EDOM')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    :root {
        --edom-primary: #1e40af;
        --edom-success: #10b981;
        --edom-info: #0ea5e9;
        --edom-warning: #f59e0b;
        --edom-danger: #ef4444;
        --edom-slate: #64748b;
        --edom-bg-soft: #f8fafc;
    }

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
        font-size: 24px;
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

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
    }
    .filter-group-inner {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .filter-item {
        min-width: 170px;
        flex: 1 1 auto;
    }
    .filter-item label {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-item select, .filter-item .form-control {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        height: 38px;
    }

    /* KPI Cards */
    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px 22px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
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
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        float: right;
    }
    .kpi-primary .kpi-icon { background: #eff6ff; color: #2563eb; }
    .kpi-success .kpi-icon { background: #ecfdf5; color: #10b981; }
    .kpi-info .kpi-icon { background: #f0f9ff; color: #0ea5e9; }
    .kpi-warning .kpi-icon { background: #fffbeb; color: #f59e0b; }

    .kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .kpi-value {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 6px;
    }
    .kpi-sub {
        font-size: 12px;
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

    /* Distribution Table */
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

    /* Decision-Making Lecturer Cards */
    .lecturer-item {
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 12px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.15s ease;
    }
    .lecturer-item:last-child {
        margin-bottom: 0;
    }
    .lecturer-item:hover {
        background: #f8fafc;
    }
    .lecturer-item.top-rank-1 {
        background: #fffdf5;
        border-color: #fde68a;
    }
    .lecturer-item.coach-priority {
        background: #fff8f8;
        border-color: #fecaca;
    }
    .lecturer-rank-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        flex-shrink: 0;
    }
    .rank-gold { background: #fef08a; color: #854d0e; }
    .rank-silver { background: #e2e8f0; color: #334155; }
    .rank-bronze { background: #fed7aa; color: #9a3412; }
    .rank-coach { background: #fee2e2; color: #991b1b; }

    .lecturer-info {
        margin-left: 12px;
        flex-grow: 1;
    }
    .lecturer-name {
        font-weight: 600;
        font-size: 13.5px;
        color: #0f172a;
        margin-bottom: 2px;
    }
    .lecturer-meta {
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .lecturer-score-box {
        text-align: right;
        flex-shrink: 0;
        margin-left: 12px;
    }
    .score-value {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
    }
    .score-percent {
        font-size: 11.5px;
        font-weight: 600;
        margin-top: 3px;
    }

    .badge-chip {
        font-size: 10.5px;
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

    .loading-overlay {
        position: relative;
        min-height: 200px;
    }
    .spinner-box {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px;
        color: #64748b;
        font-size: 14px;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper">
    <!-- Header Banner -->
    <div class="exec-header">
        <div>
            <h2><i class="fas fa-chart-line mr-2"></i> Dashboard Eksekutif Evaluasi Dosen (EDOM)</h2>
            <p>Ringkasan Indikator Mutu Kinerja Pembelajaran, Kepuasan Mahasiswa, & Rekomendasi Pembinaan Akademik</p>
        </div>
        <div>
            <span class="ta-badge">
                <i class="fas fa-calendar-check"></i>
                <span>Tahun Akademik: <strong>{{ $tahun_ajaran ?: 'Belum Aktif' }}</strong></span>
            </span>
        </div>
    </div>

    <!-- Alert Session Belum Aktif -->
    @if(!$tahun_ajaran)
    <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-triangle-exclamation mr-2"></i>
        <strong>Perhatian:</strong> Sesi Tahun Akademik belum dipilih. Silakan pilih tahun akademik melalui menu navigasi atas agar data evaluasi tampil sempurna.
    </div>
    @endif

    <!-- Filter Card -->
    <div class="filter-card">
        <div class="filter-group-inner">
            <div class="filter-item" style="max-width: 220px;">
                <label><i class="fas fa-layer-group mr-1"></i> Tingkat Evaluasi</label>
                <select id="filter-type" class="form-control">
                    <option value="universal">Universal (Seluruh Kampus)</option>
                    <option value="fakultas">Tingkat Fakultas</option>
                    <option value="prodi">Tingkat Program Studi</option>
                </select>
            </div>
            <div class="filter-item" id="container-fakultas" style="display:none; max-width: 280px;">
                <label><i class="fas fa-university mr-1"></i> Pilih Fakultas</label>
                <select id="filter-fakultas" class="form-control">
                    <option value="">-- Memuat Fakultas... --</option>
                </select>
            </div>
            <div class="filter-item" id="container-prodi" style="display:none; max-width: 280px;">
                <label><i class="fas fa-graduation-cap mr-1"></i> Pilih Program Studi</label>
                <select id="filter-prodi" class="form-control">
                    <option value="">-- Memuat Program Studi... --</option>
                </select>
            </div>
            <div class="filter-item" style="flex: 0 0 auto; min-width: auto; align-self: flex-end;">
                <button class="btn btn-primary px-3" id="btn-filter" onclick="fetchDashboardData()" title="Terapkan Filter">
                    <i class="fas fa-filter mr-1"></i> Terapkan
                </button>
                <button class="btn btn-outline-secondary px-3 ml-1" id="btn-reset" onclick="resetFilter()" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div class="row">
        <!-- KPI 1: Indeks Mutu Rata-rata -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-primary">
                <div class="kpi-icon"><i class="fas fa-award"></i></div>
                <div class="kpi-label">Indeks Mutu EDOM</div>
                <div class="kpi-value" id="kpi-score">0.00 <span style="font-size: 14px; font-weight: normal; color: #64748b;">/ 4.00</span></div>
                <p class="kpi-sub">
                    <span class="badge-chip chip-primary" id="kpi-percent">0.00% Capaian</span>
                    <span id="kpi-predikat" class="font-weight-bold" style="color: #2563eb;">-</span>
                </p>
            </div>
        </div>
        <!-- KPI 2: Total Respon Jawaban -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-success">
                <div class="kpi-icon"><i class="fas fa-list-check"></i></div>
                <div class="kpi-label">Total Respon Masuk</div>
                <div class="kpi-value" id="kpi-total-jawaban">0</div>
                <p class="kpi-sub">
                    <span id="kpi-valid-detail">0 Valid</span> &bull; <span id="kpi-na-detail" style="color: #94a3b8;">0 N/A</span>
                </p>
            </div>
        </div>
        <!-- KPI 3: Partisipasi Mahasiswa -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-info">
                <div class="kpi-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="kpi-label">Partisipasi Mahasiswa</div>
                <div class="kpi-value" id="kpi-total-mhs">0</div>
                <p class="kpi-sub"><i class="fas fa-check-circle text-info"></i> Responden aktif berpartisipasi</p>
            </div>
        </div>
        <!-- KPI 4: Cakupan Dosen Dievaluasi -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-warning">
                <div class="kpi-icon"><i class="fas fa-chalkboard-user"></i></div>
                <div class="kpi-label">Dosen Tervalidasi</div>
                <div class="kpi-value" id="kpi-total-dosen">0</div>
                <p class="kpi-sub"><i class="fas fa-user-check text-warning"></i> Dosen menerima umpan balik</p>
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
                <div class="box-body">
                    <div id="general-chart" style="height: 330px;"></div>
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
                    <div id="pie-explanation" class="table-responsive"></div>
                    <div class="methodology-note">
                        <i class="fas fa-circle-info text-primary mr-1"></i>
                        <strong>Prinsip Pengukuran Mutu:</strong> Indeks evaluasi EDOM dihitung secara murni dari respon evaluatif valid (Skala 1 s/d 4). Respon <em>"0 - Tidak Berlaku" (N/A)</em> dipisahkan dan diexclude dari pembagi rata-rata agar tidak mendevaluasi skor dosen secara keliru.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategic Decision-Making: Top & Bottom Performers -->
    <div class="row">
        <!-- Top 3 Dosen Berkinerja Terbaik -->
        <div class="col-lg-6">
            <div class="dashboard-box">
                <div class="box-header">
                    <h5><i class="fas fa-trophy text-warning"></i> Top Kinerja Tertinggi (Apresiasi & Benchmark)</h5>
                    <span class="badge-chip chip-success"><i class="fas fa-thumbs-up"></i> Teladan Pedagogik</span>
                </div>
                <div class="box-body">
                    <div id="top-list-container">
                        <div class="spinner-box"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data dosen...</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bottom 3 Rekomendasi Pembinaan -->
        <div class="col-lg-6">
            <div class="dashboard-box">
                <div class="box-header">
                    <h5><i class="fas fa-bullseye text-danger"></i> Prioritas Pendampingan & Pembinaan (Monev)</h5>
                    <span class="badge-chip chip-warning"><i class="fas fa-clipboard-check"></i> Rencana Tindak Lanjut</span>
                </div>
                <div class="box-body">
                    <div id="bottom-list-container">
                        <div class="spinner-box"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data pembinaan...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footnote Kebijakan -->
    <div class="text-muted text-center mt-2 mb-4" style="font-size: 11.5px;">
        <i class="fas fa-shield-halved mr-1"></i> Sistem Evaluasi Dosen Oleh Mahasiswa (SIEDOM) &bull; Penjaminan Mutu Internal Berbasis Data Objektif
    </div>
</div>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
<script>
var facultiesCache = null;
var prodiCache = {};
var generalChart = null;

var categoryColors = {
    0: { color: '#94a3b8', bg: '#f1f5f9', text: 'Tidak Berlaku (N/A)' },
    1: { color: '#ef4444', bg: '#fee2e2', text: 'Sangat Tidak Sesuai' },
    2: { color: '#f59e0b', bg: '#fef3c7', text: 'Tidak Sesuai' },
    3: { color: '#3b82f6', bg: '#dbeafe', text: 'Sesuai' },
    4: { color: '#10b981', bg: '#dcfce7', text: 'Sangat Sesuai' }
};

function getPredikat(score) {
    if (score >= 3.50) return { label: 'Sangat Memuaskan', color: '#10b981' };
    if (score >= 3.00) return { label: 'Memuaskan', color: '#2563eb' };
    if (score >= 2.00) return { label: 'Cukup', color: '#f59e0b' };
    return { label: 'Kurang / Perlu Pembinaan', color: '#ef4444' };
}

function fetchDashboardData() {
    var type = $('#filter-type').val();
    var fakultas = $('#filter-fakultas').val();
    var prodi = $('#filter-prodi').val();

    $('#btn-filter').html('<i class="fas fa-spinner fa-spin mr-1"></i> Memuat...').prop('disabled', true);

    $.get('/dashboard/general-dashboard', {
        type: type,
        fakultas: fakultas,
        prodi: prodi
    }, function(res) {
        $('#btn-filter').html('<i class="fas fa-filter mr-1"></i> Terapkan').prop('disabled', false);

        // Update KPI Summary
        updateKpiCards(res);

        // Render Chart & Explanation
        renderGeneralChart(res.pieData, res.total);
        renderPieExplanation(res.pieData, res.total, res.overall_avg, res.overall_percent);

        // Render Top & Bottom Performance Cards
        renderDecisionLists(res.topList, res.bottomList, res.quorum_applied);
    }).fail(function() {
        $('#btn-filter').html('<i class="fas fa-filter mr-1"></i> Terapkan').prop('disabled', false);
        alert('Terjadi kendala saat mengambil data dashboard. Pastikan koneksi dan session Anda masih aktif.');
    });
}

function updateKpiCards(res) {
    var score = res.overall_avg || 0;
    var percent = res.overall_percent || 0;
    var predikat = getPredikat(score);

    $('#kpi-score').html(score.toFixed(2) + ' <span style="font-size: 14px; font-weight: normal; color: #64748b;">/ 4.00</span>');
    $('#kpi-percent').text(percent.toFixed(2) + '% Capaian');
    $('#kpi-predikat').text(predikat.label).css('color', predikat.color);

    $('#kpi-total-jawaban').text(Number(res.total || 0).toLocaleString('id-ID'));
    $('#kpi-valid-detail').text(Number(res.total_valid || 0).toLocaleString('id-ID') + ' Butir Valid');
    $('#kpi-na-detail').text(Number(res.total_na || 0).toLocaleString('id-ID') + ' N/A');

    $('#kpi-total-mhs').text(Number(res.total_mhs || 0).toLocaleString('id-ID'));
    $('#kpi-total-dosen').text(Number(res.total_dosen || 0).toLocaleString('id-ID'));
}

function renderGeneralChart(pieData, total) {
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
            data: pieData.map(function(item, idx) {
                return {
                    value: item.value,
                    name: item.name
                };
            })
        }]
    };

    generalChart.setOption(option);
}

function renderPieExplanation(pieData, total, overallAvg, overallPercent) {
    var html = '<table class="table dist-table table-hover mb-0"><thead><tr>' +
        '<th>Kategori Evaluasi</th>' +
        '<th class="text-right">Frekuensi</th>' +
        '<th style="width: 35%;">Proporsi Jawaban</th>' +
        '</tr></thead><tbody>';

    pieData.forEach(function(item) {
        var key = item.key !== undefined ? item.key : 0;
        var style = categoryColors[key] || { color: '#64748b' };
        var pct = Number(item.percentage || 0);

        html += `<tr>
            <td>
                <span class="cat-pill">
                    <span class="cat-dot" style="background: ${style.color};"></span>
                    <span>${item.name}</span>
                </span>
            </td>
            <td class="text-right font-weight-bold">${Number(item.value).toLocaleString('id-ID')}</td>
            <td>
                <div class="d-flex align-items-center justify-content-between" style="font-size: 11.5px;">
                    <span class="font-weight-bold" style="color: ${style.color};">${pct.toFixed(2)}%</span>
                </div>
                <div class="mini-progress">
                    <div class="mini-progress-bar" style="width: ${pct}%; background: ${style.color};"></div>
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

function renderDecisionLists(topList, bottomList, quorumApplied) {
    // Render Top List
    var topContainer = $('#top-list-container');
    topContainer.empty();

    if (!topList || topList.length === 0) {
        topContainer.html('<div class="p-3 text-center text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada data evaluasi dosen pada filter ini.</div>');
    } else {
        var medals = [
            { icon: '🥇', class: 'rank-gold' },
            { icon: '🥈', class: 'rank-silver' },
            { icon: '🥉', class: 'rank-bronze' }
        ];

        topList.forEach(function(item, idx) {
            var medal = medals[idx] || { icon: (idx + 1), class: 'rank-silver' };
            var cardClass = (idx === 0) ? 'lecturer-item top-rank-1' : 'lecturer-item';

            topContainer.append(`
                <div class="${cardClass}">
                    <div class="lecturer-rank-badge ${medal.class}">${medal.icon}</div>
                    <div class="lecturer-info">
                        <div class="lecturer-name">${item.nama}</div>
                        <div class="lecturer-meta">
                            <span><i class="fas fa-id-card-clip"></i> NIP: ${item.nip}</span>
                            <span class="badge-chip chip-primary"><i class="fas fa-users"></i> ${item.total_responden} Responden</span>
                        </div>
                    </div>
                    <div class="lecturer-score-box">
                        <div class="score-value text-primary">${item.nilai}</div>
                        <div class="score-percent text-success"><i class="fas fa-star text-warning mr-1"></i>${item.persen}%</div>
                    </div>
                </div>
            `);
        });
    }

    // Render Bottom List
    var bottomContainer = $('#bottom-list-container');
    bottomContainer.empty();

    if (!bottomList || bottomList.length === 0) {
        bottomContainer.html(`
            <div class="p-4 text-center text-muted" style="background: #f8fafc; border-radius: 8px;">
                <i class="fas fa-user-shield fa-2x mb-2 text-info d-block"></i>
                <div class="font-weight-bold text-dark">Data Pembanding Terbatas</div>
                <small>Jumlah dosen pada filter ini &le; 3 dosen, sehingga tidak dipisahkan ke daftar prioritas pembinaan untuk mencegah bias.</small>
            </div>
        `);
    } else {
        bottomList.forEach(function(item, idx) {
            bottomContainer.append(`
                <div class="lecturer-item coach-priority">
                    <div class="lecturer-rank-badge rank-coach"><i class="fas fa-triangle-exclamation"></i></div>
                    <div class="lecturer-info">
                        <div class="lecturer-name">${item.nama}</div>
                        <div class="lecturer-meta">
                            <span><i class="fas fa-id-card-clip"></i> NIP: ${item.nip}</span>
                            <span class="badge-chip chip-warning"><i class="fas fa-users"></i> ${item.total_responden} Responden</span>
                        </div>
                    </div>
                    <div class="lecturer-score-box">
                        <div class="score-value text-danger">${item.nilai}</div>
                        <div class="score-percent text-danger">${item.persen}%</div>
                    </div>
                </div>
            `);
        });
    }
}

// Cascading Filter Dropdown Handlers
function loadFakultas(callback) {
    if (facultiesCache) {
        populateFakultas(facultiesCache);
        if (callback) callback();
        return;
    }
    $.get('/api/fakultas', function(res) {
        facultiesCache = res;
        populateFakultas(res);
        if (callback) callback();
    });
}

function populateFakultas(data) {
    var select = $('#filter-fakultas');
    select.empty().append('<option value="">-- Semua Fakultas --</option>');
    data.forEach(function(item) {
        select.append(`<option value="${item.kode_fakultas}">${item.nama_fakultas}</option>`);
    });
}

function loadProdi(kodeFakultas) {
    var select = $('#filter-prodi');
    select.empty().append('<option value="">-- Memuat Prodi... --</option>');

    var cacheKey = kodeFakultas || 'ALL';
    if (prodiCache[cacheKey]) {
        populateProdi(prodiCache[cacheKey]);
        return;
    }

    var params = kodeFakultas ? { kode_fakultas: kodeFakultas } : {};
    $.get('/api/prodi', params, function(res) {
        prodiCache[cacheKey] = res;
        populateProdi(res);
    });
}

function populateProdi(data) {
    var select = $('#filter-prodi');
    select.empty().append('<option value="">-- Semua Program Studi --</option>');
    data.forEach(function(item) {
        select.append(`<option value="${item.kode_program_studi}">${item.nama_program_studi}</option>`);
    });
}

$('#filter-type').on('change', function() {
    var val = $(this).val();
    if (val === 'universal') {
        $('#container-fakultas').hide();
        $('#container-prodi').hide();
    } else if (val === 'fakultas') {
        $('#container-fakultas').fadeIn(150);
        $('#container-prodi').hide();
        loadFakultas();
    } else if (val === 'prodi') {
        $('#container-fakultas').fadeIn(150);
        $('#container-prodi').fadeIn(150);
        loadFakultas(function() {
            var selectedFakultas = $('#filter-fakultas').val();
            loadProdi(selectedFakultas);
        });
    }
});

$('#filter-fakultas').on('change', function() {
    if ($('#filter-type').val() === 'prodi') {
        var kodeFakultas = $(this).val();
        loadProdi(kodeFakultas);
    }
});

function resetFilter() {
    $('#filter-type').val('universal').trigger('change');
    $('#filter-fakultas').val('');
    $('#filter-prodi').val('');
    fetchDashboardData();
}

$(document).ready(function() {
    fetchDashboardData();
});
</script>
@endsection