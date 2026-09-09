@extends('layouts.master_sidebar')

@section('title', 'Analisis Per Butir Soal - Evaluasi Kelas')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    .soal-wrapper {
        padding: 6px 12px 35px;
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

    /* Class Banner */
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
    .dosen-chip-card {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 10px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dosen-avatar-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* KPI Summary Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }
    .kpi-info h6 {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }
    .kpi-info .kpi-num {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }
    .kpi-info .kpi-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        font-weight: 500;
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Section Card */
    .dashboard-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .dashboard-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid #edf2f7;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .dashboard-card-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 16px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dashboard-card-body {
        padding: 22px;
    }

    /* Rekap Table */
    .rekap-table th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-top: none;
        vertical-align: middle;
    }
    .rekap-table td {
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
    }
    .rekap-table tr:hover {
        background: #f8fafc;
    }

    /* Question Box Cards */
    .soal-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 20px;
        overflow: hidden;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .soal-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 14px rgba(0,0,0,0.07);
    }
    .soal-card-header {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }
    .soal-title-box {
        flex: 1;
        min-width: 260px;
    }
    .soal-badge-no {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        color: #ffffff;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 6px;
        margin-right: 8px;
    }
    .soal-komponen-badge {
        display: inline-flex;
        align-items: center;
        background: #e0e7ff;
        color: #4338ca;
        font-weight: 600;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .soal-text {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin-top: 8px;
        line-height: 1.5;
    }
    .soal-score-box {
        text-align: right;
        min-width: 140px;
    }
    .soal-score-val {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.1;
    }
    .soal-score-pct {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }

    /* Distribution Table inside Soal Card */
    .dist-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-top: none;
        padding: 8px 10px;
    }
    .dist-table td {
        font-size: 12px;
        padding: 8px 10px;
        vertical-align: middle;
    }
    .color-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .progress-dist {
        height: 6px;
        border-radius: 4px;
        background-color: #f1f5f9;
        margin-top: 4px;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 14px;
    }

    @media print {
        .action-bar, .filter-controls, .btn-scroll-top, .sidebar, .main-header {
            display: none !important;
        }
        .class-banner {
            background: #1e3a8a !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection

@section('content')
<div class="soal-wrapper">

    <!-- Action Bar -->
    <div class="action-bar">
        <div>
            <a href="{{ url('/admin/kelas/detail/'.$id_kelas) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left mr-1"></i> Kembali ke Detail Kelas
            </a>
            <a href="{{ url('/admin/kelas') }}" class="btn btn-outline-secondary btn-sm ml-1">
                <i class="fa fa-list mr-1"></i> Daftar Kelas
            </a>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                <i class="fa fa-print mr-1"></i> Cetak Analisis
            </button>
        </div>
    </div>

    <!-- Master Class Profile Banner -->
    <div class="class-banner">
        <div>
            <div class="meta-tags mb-2">
                <span class="meta-chip"><i class="fa fa-book"></i> <span id="banner-kode">Memuat...</span></span>
                <span class="meta-chip"><i class="fa fa-users"></i> <span id="banner-kelas">-</span></span>
                <span class="meta-chip"><i class="fa fa-graduation-cap"></i> <span id="banner-prodi">-</span></span>
                <span class="meta-chip"><i class="fa fa-calendar-alt"></i> Semester <span id="banner-smt">-</span></span>
            </div>
            <h2 id="banner-matkul">Memuat Data Mata Kuliah...</h2>
            <p class="mb-0 text-white-50" style="font-size: 13px;">
                <i class="fa fa-chart-bar mr-1"></i> Analisis Detail Evaluasi Dosen Oleh Mahasiswa (EDOM) Berdasarkan Butir Pertanyaan
            </p>
        </div>
        <div class="dosen-chip-card">
            <div class="dosen-avatar-circle">
                <i class="fa fa-chalkboard-teacher"></i>
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85;">Dosen Pengampu</div>
                <div id="banner-dosen" style="font-size: 15px; font-weight: 700;">Memuat...</div>
                <div id="banner-nip" style="font-size: 11px; opacity: 0.8;">NIP: -</div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Executive Summary Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-info">
                <h6>Rata-rata Kelas</h6>
                <div class="kpi-num" id="kpi-avg">-</div>
                <div class="kpi-sub" id="kpi-percent">-</div>
            </div>
            <div class="kpi-icon" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa fa-star"></i>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <h6>Total Responden</h6>
                <div class="kpi-num" id="kpi-mhs">-</div>
                <div class="kpi-sub" id="kpi-soal-count">- Butir Soal</div>
            </div>
            <div class="kpi-icon" style="background: #ecfdf5; color: #059669;">
                <i class="fa fa-user-check"></i>
            </div>
        </div>
        <div class="kpi-card" id="card-highest" style="cursor: pointer;" title="Klik untuk lompat ke soal tertinggi">
            <div class="kpi-info">
                <h6>Aspek Tertinggi</h6>
                <div class="kpi-num" style="color: #059669;" id="kpi-highest-score">-</div>
                <div class="kpi-sub text-truncate" style="max-width: 170px;" id="kpi-highest-text">Kekuatan Utama</div>
            </div>
            <div class="kpi-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="fa fa-arrow-trend-up"></i>
            </div>
        </div>
        <div class="kpi-card" id="card-lowest" style="cursor: pointer;" title="Klik untuk lompat ke soal evaluasi">
            <div class="kpi-info">
                <h6>Prioritas Evaluasi</h6>
                <div class="kpi-num" style="color: #d97706;" id="kpi-lowest-score">-</div>
                <div class="kpi-sub text-truncate" style="max-width: 170px;" id="kpi-lowest-text">Perlu Perhatian</div>
            </div>
            <div class="kpi-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fa fa-lightbulb"></i>
            </div>
        </div>
    </div>

    <!-- Rekapitulasi Butir Soal (Quick Overview Table) -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5><i class="fa fa-table text-primary"></i> Matriks Rekapitulasi Butir Pertanyaan</h5>
            <div class="d-flex align-items-center gap-2 filter-controls flex-wrap">
                <select id="filter-komponen" class="form-control form-control-sm" style="width: auto; font-size: 12px;">
                    <option value="">-- Semua Komponen --</option>
                </select>
                <select id="sort-soal" class="form-control form-control-sm ml-2" style="width: auto; font-size: 12px;">
                    <option value="id_asc">Urutkan: No Soal (Default)</option>
                    <option value="score_asc">Urutkan: Skor Terendah (Prioritas Evaluasi)</option>
                    <option value="score_desc">Urutkan: Skor Tertinggi</option>
                </select>
            </div>
        </div>
        <div class="dashboard-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover rekap-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th style="width: 180px;">Komponen</th>
                            <th>Pernyataan Kuesioner</th>
                            <th style="width: 90px;" class="text-center">Responden</th>
                            <th style="width: 160px;">Rata-rata Skor</th>
                            <th style="width: 120px;" class="text-center">Predikat</th>
                            <th style="width: 80px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rekap-table-body">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa fa-spinner fa-spin mr-1"></i> Mengambil rekapitulasi butir soal...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Analisis Grafik Per Butir Soal -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 font-weight-bold text-dark">
            <i class="fa fa-chart-pie text-info mr-1"></i> Rincian Distribusi & Grafik Per Butir Soal
        </h5>
        <small class="text-muted">Skala 1-4 (0 = Tidak Berlaku dieksklusikan dari rata-rata)</small>
    </div>

    <div id="soal-list-container">
        <div class="dashboard-card empty-state">
            <i class="fa fa-spinner fa-spin"></i>
            <h5>Memuat Rincian Soal...</h5>
            <p class="text-muted">Sedang menyiapkan visualisasi grafik dan frekuensi respon.</p>
        </div>
    </div>

</div>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
<script>
$(function(){
    var soalDataList = [];
    var chartInstances = [];
    var highestSoalId = null;
    var lowestSoalId = null;

    var colorMap = {
        4: '#10b981', // Sangat Sesuai (Emerald)
        3: '#3b82f6', // Sesuai (Blue)
        2: '#f59e0b', // Tidak Sesuai (Amber)
        1: '#ef4444', // Sangat Tidak Sesuai (Rose)
        0: '#94a3b8'  // Tidak Berlaku (Slate)
    };

    $.getJSON("{{ url('/admin/kelas/'.$id_kelas.'/soal/data') }}", function(res){
        var info = res.kelas_info || {};
        var summary = res.class_summary || {};
        soalDataList = res.soal_list || [];

        // 1. Tampilkan Master Banner
        $('#banner-kode').text(info.kode_matakuliah || '-');
        $('#banner-kelas').text('Kelas ' + (info.nama_kelas || '-'));
        $('#banner-prodi').text(info.nama_program_studi || '-');
        $('#banner-smt').text(info.semester || '-');
        $('#banner-matkul').text(info.nama_matakuliah || 'Mata Kuliah');
        $('#banner-dosen').text(info.nama_dosen || 'Belum Ditentukan');
        $('#banner-nip').text(info.nip_dosen ? ('NIP: ' + info.nip_dosen) : 'NIP: -');

        // 2. Tampilkan KPI
        if(summary.total_mhs > 0){
            var avgColor = summary.overall_avg >= 3.0 ? '#059669' : (summary.overall_avg >= 2.0 ? '#d97706' : '#dc2626');
            $('#kpi-avg').html('<span style="color:' + avgColor + '">' + Number(summary.overall_avg).toFixed(2) + '</span> <span style="font-size:13px; color:#64748b;">/ 4.00</span>');
            $('#kpi-percent').html('Mutu: <b>' + summary.overall_percent + '%</b>');
            $('#kpi-mhs').text(summary.total_mhs + ' Mhs');
            $('#kpi-soal-count').text(summary.total_soal + ' Butir Soal');

            if(summary.highest_soal){
                highestSoalId = summary.highest_soal.id_soal;
                $('#kpi-highest-score').text(Number(summary.highest_soal.avg_score).toFixed(2) + ' ★');
                $('#kpi-highest-text').text('#' + summary.highest_soal.id_soal + ' ' + summary.highest_soal.pertanyaan);
            }
            if(summary.lowest_soal){
                lowestSoalId = summary.lowest_soal.id_soal;
                $('#kpi-lowest-score').text(Number(summary.lowest_soal.avg_score).toFixed(2) + ' ★');
                $('#kpi-lowest-text').text('#' + summary.lowest_soal.id_soal + ' ' + summary.lowest_soal.pertanyaan);
            }
        } else {
            $('#kpi-avg').text('0.00');
            $('#kpi-percent').text('Belum ada respon');
            $('#kpi-mhs').text('0 Mhs');
            $('#kpi-soal-count').text(soalDataList.length + ' Butir Soal');
            $('#kpi-highest-score').text('-');
            $('#kpi-lowest-score').text('-');
        }

        // Quick click jumper for highest/lowest
        $('#card-highest').on('click', function(){
            if(highestSoalId) scrollToSoal(highestSoalId);
        });
        $('#card-lowest').on('click', function(){
            if(lowestSoalId) scrollToSoal(lowestSoalId);
        });

        // 3. Isi Opsi Filter Komponen
        var komponenSet = {};
        soalDataList.forEach(function(s){
            if(s.nama_komponen && !komponenSet[s.nama_komponen]){
                komponenSet[s.nama_komponen] = true;
                $('#filter-komponen').append('<option value="' + s.nama_komponen + '">' + s.nama_komponen + '</option>');
            }
        });

        // 4. Render Rekapitulasi & List Soal
        renderAllViews();

        // Event listener filter & sorting
        $('#filter-komponen, #sort-soal').on('change', function(){
            renderAllViews();
        });
    }).fail(function(){
        $('#soal-list-container').html(
            '<div class="dashboard-card empty-state text-danger">' +
                '<i class="fa fa-exclamation-triangle"></i>' +
                '<h5>Gagal Memuat Data</h5>' +
                '<p>Terjadi kendala saat mengambil data kuesioner kelas ini.</p>' +
            '</div>'
        );
    });

    function getFilteredAndSortedSoal() {
        var komp = $('#filter-komponen').val();
        var sort = $('#sort-soal').val();

        var list = soalDataList.slice();

        if(komp){
            list = list.filter(function(s){
                return s.nama_komponen === komp;
            });
        }

        if(sort === 'score_asc'){
            list.sort(function(a,b){ return a.avg_score - b.avg_score; });
        } else if(sort === 'score_desc'){
            list.sort(function(a,b){ return b.avg_score - a.avg_score; });
        } else {
            list.sort(function(a,b){ return a.id_soal - b.id_soal; });
        }

        return list;
    }

    function renderAllViews() {
        var list = getFilteredAndSortedSoal();

        // A. Render Tabel Rekapitulasi
        var tbody = $('#rekap-table-body');
        tbody.empty();

        if(!list.length){
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada butir soal yang sesuai kriteria filter.</td></tr>');
        } else {
            list.forEach(function(s, idx){
                var barColor = s.avg_score >= 3.5 ? '#10b981' : (s.avg_score >= 3.0 ? '#3b82f6' : (s.avg_score >= 2.0 ? '#f59e0b' : '#ef4444'));
                var tr = '<tr>' +
                    '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
                    '<td><span class="soal-komponen-badge">' + (s.nama_komponen || 'Umum') + '</span></td>' +
                    '<td>' +
                        '<div class="font-weight-600 text-dark">' + s.pertanyaan + '</div>' +
                        '<small class="text-muted">Soal #' + s.id_soal + '</small>' +
                    '</td>' +
                    '<td class="text-center font-weight-bold">' + s.valid_count + '</td>' +
                    '<td>' +
                        '<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">' +
                            '<strong>' + Number(s.avg_score).toFixed(2) + ' <span class="text-muted" style="font-size:10px;">/ 4.00</span></strong>' +
                            '<span class="font-weight-600" style="color:' + barColor + '">' + s.percent + '%</span>' +
                        '</div>' +
                        '<div class="progress" style="height:5px; background:#f1f5f9; border-radius:3px;">' +
                            '<div class="progress-bar" style="width:' + s.percent + '%; background-color:' + barColor + ';"></div>' +
                        '</div>' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<span class="badge badge-' + (s.badge_class || 'secondary') + '" style="font-size:11px; padding:4px 8px;">' + s.predikat + '</span>' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-xs btn-outline-primary btn-jump" data-id="' + s.id_soal + '" title="Lihat Grafik">' +
                            '<i class="fa fa-chart-pie"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
                tbody.append(tr);
            });
        }

        // B. Render Card Soal Detail + ECharts
        renderCardsAndCharts(list);
    }

    function renderCardsAndCharts(list) {
        var container = $('#soal-list-container');
        container.empty();

        // Clear existing charts
        chartInstances.forEach(function(c){
            try { c.dispose(); } catch(e){}
        });
        chartInstances = [];

        if(!list.length){
            container.html(
                '<div class="dashboard-card empty-state">' +
                    '<i class="fa fa-folder-open"></i>' +
                    '<h5>Tidak Ada Butir Soal</h5>' +
                    '<p class="text-muted">Silakan ubah filter atau kuesioner belum memiliki data butir soal.</p>' +
                '</div>'
            );
            return;
        }

        list.forEach(function(s){
            var scoreColor = s.avg_score >= 3.5 ? '#10b981' : (s.avg_score >= 3.0 ? '#2563eb' : (s.avg_score >= 2.0 ? '#d97706' : '#dc2626'));
            var cardHtml = 
            '<div class="soal-card" id="soal-target-' + s.id_soal + '">' +
                '<div class="soal-card-header">' +
                    '<div class="soal-title-box">' +
                        '<div>' +
                            '<span class="soal-badge-no">Soal #' + s.id_soal + '</span>' +
                            '<span class="soal-komponen-badge">' + (s.nama_komponen || 'Umum') + '</span>' +
                        '</div>' +
                        '<div class="soal-text">' + s.pertanyaan + '</div>' +
                    '</div>' +
                    '<div class="soal-score-box">' +
                        '<div class="soal-score-val" style="color:' + scoreColor + '">' + Number(s.avg_score).toFixed(2) + ' <small style="font-size:12px; color:#64748b;">/ 4.00</small></div>' +
                        '<div class="soal-score-pct">Mutu: <b>' + s.percent + '%</b></div>' +
                        '<span class="badge badge-' + (s.badge_class || 'secondary') + ' mt-1" style="font-size:11px;">' + s.predikat + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="p-3">' +
                    '<div class="row align-items-center">' +
                        '<div class="col-lg-5 col-md-12">' +
                            '<div id="chart-soal-' + s.id_soal + '" style="width: 100%; height: 260px;"></div>' +
                        '</div>' +
                        '<div class="col-lg-7 col-md-12">' +
                            '<div class="table-responsive">' +
                                '<table class="table table-sm dist-table mb-1">' +
                                    '<thead>' +
                                        '<tr>' +
                                            '<th>Skala Jawaban</th>' +
                                            '<th class="text-right" style="width: 70px;">Jumlah</th>' +
                                            '<th style="width: 150px;">Persentase</th>' +
                                        '</tr>' +
                                    '</thead>' +
                                    '<tbody id="table-soal-' + s.id_soal + '"></tbody>' +
                                '</table>' +
                            '</div>' +
                            '<small class="text-muted d-block mt-2" style="font-size:11px;">' +
                                '<i class="fa fa-info-circle mr-1"></i> Responden Valid: <b>' + s.valid_count + '</b> | Tidak Berlaku (N/A): <b>' + s.na_count + '</b> (dieksklusikan dari rata-rata)' +
                            '</small>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

            container.append(cardHtml);

            // Populate Table
            var tbody = $('#table-soal-' + s.id_soal);
            (s.distribution || []).forEach(function(d){
                var clr = colorMap[d.jawaban] || '#64748b';
                var tr = '<tr>' +
                    '<td>' +
                        '<span class="color-dot" style="background:' + clr + '"></span>' +
                        '<b>' + d.jawaban + '</b> - ' + d.name +
                    '</td>' +
                    '<td class="text-right font-weight-bold">' + d.count + '</td>' +
                    '<td>' +
                        '<div class="d-flex justify-content-between align-items-center" style="font-size:11px;">' +
                            '<span>' + d.percentage + '%</span>' +
                        '</div>' +
                        '<div class="progress progress-dist">' +
                            '<div class="progress-bar" style="width:' + d.percentage + '%; background-color:' + clr + ';"></div>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
                tbody.append(tr);
            });

            // Init ECharts Donut Chart
            var domChart = document.getElementById('chart-soal-' + s.id_soal);
            if(domChart){
                var chart = echarts.init(domChart);
                chartInstances.push(chart);

                var chartData = (s.distribution || []).filter(function(d){ return d.count > 0; }).map(function(d){
                    return {
                        value: d.count,
                        name: d.jawaban + ' - ' + d.name,
                        itemStyle: { color: colorMap[d.jawaban] || '#64748b' }
                    };
                });

                if(!chartData.length){
                    chartData = [{ value: 1, name: 'Belum Ada Respon', itemStyle: { color: '#e2e8f0' } }];
                }

                chart.setOption({
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}<br/><b>{c} Responden ({d}%)</b>'
                    },
                    series: [{
                        type: 'pie',
                        radius: ['45%', '70%'],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 6,
                            borderColor: '#ffffff',
                            borderWidth: 2
                        },
                        label: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '13',
                                fontWeight: 'bold',
                                formatter: '{b}\n{d}%'
                            }
                        },
                        data: chartData
                    }]
                });
            }
        });

        // Attach click handler to jump buttons
        $('.btn-jump').on('click', function(){
            var id = $(this).data('id');
            scrollToSoal(id);
        });
    }

    function scrollToSoal(id) {
        var target = $('#soal-target-' + id);
        if(target.length){
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 500);
            target.css('border-color', '#2563eb');
            setTimeout(function(){
                target.css('border-color', '#e2e8f0');
            }, 1800);
        }
    }

    window.addEventListener('resize', function(){
        chartInstances.forEach(function(c){
            c.resize();
        });
    });
});
</script>
@endsection
