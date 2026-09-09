@extends('layouts.master_sidebar')

@section('title', 'Laporan Evaluasi Program Studi')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    .report-prodi-wrapper {
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

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    /* Program Studi Banner */
    .prodi-banner {
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
    .prodi-banner h2 {
        font-weight: 700;
        font-size: 22px;
        margin: 0 0 6px 0;
        color: #ffffff;
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
    .score-badge-card {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 10px;
        padding: 12px 20px;
        text-align: right;
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

    /* Tab Custom Styling */
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 20px;
    }
    .nav-tabs-custom .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        padding: 12px 20px;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #2563eb;
    }
    .nav-tabs-custom .nav-link.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background: transparent;
    }

    /* Data Table Styling */
    .custom-table th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-top: none;
        vertical-align: middle;
    }
    .custom-table td {
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
    }
    .custom-table tr:hover {
        background: #f8fafc;
    }

    /* Mini Progress Bar */
    .progress-score {
        height: 6px;
        border-radius: 3px;
        background-color: #f1f5f9;
        margin-top: 4px;
    }

    /* Monev Collapsible Card */
    .card-monev {
        border: 1px solid #fed7aa;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .card-monev-header {
        background: #fff7ed;
        padding: 14px 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }
    .empty-state i {
        font-size: 44px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    @media print {
        .action-bar, .filter-card, .nav-tabs-custom, .sidebar, .main-header, .btn, .no-print {
            display: none !important;
        }
        .tab-pane {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .prodi-banner {
            background: #1e3a8a !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection

@section('content')
<div class="report-prodi-wrapper">

    <!-- Action Bar -->
    <div class="action-bar">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-chart-line text-primary mr-2"></i>Laporan Evaluasi Program Studi
            </h4>
            <small class="text-muted">Analisis mutu pembelajaran dosen, kepuasan mahasiswa, dan evaluasi per mata kuliah pada tingkat prodi.</small>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                <i class="fa fa-print mr-1"></i> Cetak Laporan Mutu Prodi
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form id="filter-form" class="row align-items-center">
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label class="font-weight-bold text-muted mb-1" style="font-size: 12px;">Fakultas</label>
                <select id="select-fakultas" class="form-control form-control-sm">
                    <option value="">-- Semua Fakultas --</option>
                    @foreach($fakultas as $f)
                        <option value="{{ $f->kode_fakultas }}">{{ $f->nama_fakultas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                <label class="font-weight-bold text-muted mb-1" style="font-size: 12px;">Program Studi</label>
                <select id="select-prodi" class="form-control form-control-sm">
                    @foreach($prodi as $p)
                        <option value="{{ $p->kode_program_studi }}" data-fakultas="{{ $p->kode_fakultas }}">
                            {{ $p->nama_program_studi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label class="font-weight-bold text-muted mb-1" style="font-size: 12px;">Tahun Akademik</label>
                <select id="select-mreg" class="form-control form-control-sm">
                    @foreach($mregList as $m)
                        <option value="{{ $m->id_mreg }}" {{ Session::get('id_mreg') == $m->id_mreg ? 'selected' : '' }}>
                            {{ $m->tahun }} ({{ $m->semester == '1' ? 'Ganjil' : 'Genap' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6 mt-3 mt-md-4">
                <button type="button" id="btn-apply-filter" class="btn btn-primary btn-sm btn-block">
                    <i class="fa fa-magnifying-glass mr-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Master Banner Program Studi -->
    <div class="prodi-banner">
        <div>
            <div class="mb-2">
                <span class="meta-chip"><i class="fa fa-graduation-cap"></i> <span id="banner-fakultas">-</span></span>
                <span class="meta-chip"><i class="fa fa-tag"></i> Kode: <span id="banner-kode">-</span></span>
                <span class="meta-chip"><i class="fa fa-calendar-alt"></i> TA: <span id="banner-ta">-</span></span>
            </div>
            <h2 id="banner-nama-prodi">Memuat Data Program Studi...</h2>
            <p class="mb-0 text-white-50" style="font-size: 13px;">
                <i class="fa fa-info-circle mr-1"></i> Rekapitulasi agregasi kuesioner Evaluasi Dosen Oleh Mahasiswa (EDOM). Skala 0 (Tidak Berlaku) dieksklusikan dari perhitungan rata-rata.
            </p>
        </div>
        <div class="score-badge-card">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85;">Indeks Mutu Prodi</div>
            <div id="banner-score" style="font-size: 26px; font-weight: 800; line-height: 1.1;">-</div>
            <div id="banner-percent" style="font-size: 12px; font-weight: 600; opacity: 0.9;">-</div>
            <span id="banner-predikat" class="badge badge-light mt-1" style="font-size: 11px;">-</span>
        </div>
    </div>

    <!-- 4 KPI Executive Summary Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-info">
                <h6>Indeks Mutu Prodi</h6>
                <div class="kpi-num" id="kpi-avg">-</div>
                <div class="kpi-sub" id="kpi-percent">-</div>
            </div>
            <div class="kpi-icon" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa fa-star"></i>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <h6>Mahasiswa Responden</h6>
                <div class="kpi-num" id="kpi-mhs">-</div>
                <div class="kpi-sub" id="kpi-answers">- Jawaban Masuk</div>
            </div>
            <div class="kpi-icon" style="background: #ecfdf5; color: #059669;">
                <i class="fa fa-user-check"></i>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <h6>Dosen Pengampu</h6>
                <div class="kpi-num" id="kpi-dosen">-</div>
                <div class="kpi-sub">Aktif Mengajar di Prodi</div>
            </div>
            <div class="kpi-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa fa-chalkboard-teacher"></i>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <h6>Kelas Terlaksana</h6>
                <div class="kpi-num" id="kpi-kelas">-</div>
                <div class="kpi-sub">Total Kelas Kuliah</div>
            </div>
            <div class="kpi-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa fa-book-open"></i>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <ul class="nav nav-tabs nav-tabs-custom" id="reportTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-dosen-link" data-toggle="tab" href="#pane-dosen" role="tab">
                <i class="fa fa-user-tie mr-1"></i> Kinerja Dosen Prodi (<span id="count-dosen">0</span>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-kelas-link" data-toggle="tab" href="#pane-kelas" role="tab">
                <i class="fa fa-chalkboard mr-1"></i> Rekapitulasi Kelas & Matkul (<span id="count-kelas">0</span>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-persepsi-link" data-toggle="tab" href="#pane-persepsi" role="tab">
                <i class="fa fa-chart-pie mr-1"></i> Persepsi Mahasiswa & Aspek Mutu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-monev-link" data-toggle="tab" href="#pane-monev" role="tab">
                <i class="fa fa-shield-halved mr-1"></i> Monev & Pembinaan Mutu
            </a>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="reportTabContent">

        <!-- TAB 1: Kinerja Dosen Prodi -->
        <div class="tab-pane fade show active" id="pane-dosen" role="tabpanel">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fa fa-ranking-star text-primary mr-1"></i> Peringkat Kinerja Evaluasi Dosen di Program Studi
                    </h5>
                    <small class="text-muted">Diurutkan berdasarkan rata-rata skor evaluasi mahasiswa</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover custom-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Dosen Pengampu</th>
                                    <th style="width: 130px;">NIP</th>
                                    <th style="width: 100px;" class="text-center">Kelas</th>
                                    <th style="width: 110px;" class="text-center">Responden</th>
                                    <th style="width: 180px;">Rata-rata Skor</th>
                                    <th style="width: 140px;" class="text-center">Predikat Mutu</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-dosen">
                                <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Memuat data dosen...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Rekapitulasi Kelas & Matkul -->
        <div class="tab-pane fade" id="pane-kelas" role="tabpanel">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fa fa-table-list text-info mr-1"></i> Rekapitulasi Evaluasi Seluruh Kelas Kuliah
                    </h5>
                    <small class="text-muted">Klik pada butir soal untuk membuka detail kuesioner per kelas</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover custom-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th style="width: 110px;">Kode</th>
                                    <th>Mata Kuliah & Kelas</th>
                                    <th>Dosen Pengampu</th>
                                    <th style="width: 80px;" class="text-center">Smt</th>
                                    <th style="width: 90px;" class="text-center">Responden</th>
                                    <th style="width: 160px;">Rata-rata Skor</th>
                                    <th style="width: 120px;" class="text-center">Predikat</th>
                                    <th style="width: 90px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-kelas">
                                <tr><td colspan="9" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Memuat data kelas...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Persepsi Mahasiswa & Aspek Mutu -->
        <div class="tab-pane fade" id="pane-persepsi" role="tabpanel">
            <div class="row">
                <!-- Donut Chart Distribusi Skala -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 font-weight-bold text-dark">
                                <i class="fa fa-chart-pie text-success mr-1"></i> Distribusi Skala Jawaban Mahasiswa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="chart-pie-prodi" style="width: 100%; height: 320px;"></div>
                            <div class="table-responsive mt-3">
                                <table class="table table-sm custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Skala Jawaban</th>
                                            <th class="text-right">Respon</th>
                                            <th class="text-right">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-skala"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Capaian Aspek Komponen Penilaian -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 font-weight-bold text-dark">
                                <i class="fa fa-chart-bar text-primary mr-1"></i> Capaian Nilai Per Aspek / Komponen Penilaian
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="chart-komponen-prodi" style="width: 100%; height: 320px;"></div>
                            <div class="table-responsive mt-3">
                                <table class="table table-sm custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Komponen Penilaian</th>
                                            <th class="text-center">Respon</th>
                                            <th class="text-right">Rata-rata</th>
                                            <th class="text-right">Mutu</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-komponen"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Monev & Pembinaan Mutu -->
        <div class="tab-pane fade" id="pane-monev" role="tabpanel">
            <!-- Dosen Kinerja Unggul -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fa fa-trophy mr-2"></i> Dosen Kinerja Unggul (Apresiasi / Teladan Prodi)
                    </h5>
                    <small class="text-white-50">Dosen dengan rata-rata skor $\ge 3.50$ dan memenuhi kuorum keterwakilan ($\ge 3$ responden)</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover custom-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Dosen Pengampu</th>
                                    <th style="width: 140px;">NIP</th>
                                    <th style="width: 110px;" class="text-center">Kelas</th>
                                    <th style="width: 120px;" class="text-center">Responden</th>
                                    <th style="width: 140px;" class="text-right">Skor Rata-rata</th>
                                    <th style="width: 120px;" class="text-center">Mutu</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-monev-best">
                                <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data dosen yang memenuhi kriteria unggul.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Dosen Prioritas Pembinaan (Collapsible demi privasi rapat) -->
            <div class="card-monev">
                <div class="card-monev-header" data-toggle="collapse" data-target="#collapseMonevProdi" aria-expanded="false" aria-controls="collapseMonevProdi">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 36px; height: 36px; border-radius: 8px; background: #ea580c; color:#fff; display:flex; align-items:center; justify-content:center;">
                            <i class="fa fa-user-shield"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-dark">
                                Dosen Prioritas Pembinaan & Pendampingan Mutu (Monev)
                            </h6>
                            <small class="text-muted">Skor rata-rata $< 3.00$ dengan kuorum $\ge 3$ responden. <i>Klik untuk membuka/tutup rincian (rahasia).</i></small>
                        </div>
                    </div>
                    <span class="badge badge-warning"><i class="fa fa-eye mr-1"></i> Buka / Tutup</span>
                </div>
                <div id="collapseMonevProdi" class="collapse">
                    <div class="card-body p-0 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Dosen Pengampu</th>
                                        <th style="width: 140px;">NIP</th>
                                        <th style="width: 110px;" class="text-center">Kelas</th>
                                        <th style="width: 120px;" class="text-center">Responden</th>
                                        <th style="width: 140px;" class="text-right">Skor Rata-rata</th>
                                        <th style="width: 120px;" class="text-center">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-monev-support">
                                    <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada dosen yang memerlukan prioritas pembinaan pada prodi ini.</td></tr>
                                </tbody>
                            </table>
                        </div>
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
$(function(){
    var chartPie = null;
    var chartBar = null;

    var colorMap = {
        4: '#10b981', // Sangat Sesuai (Emerald)
        3: '#3b82f6', // Sesuai (Blue)
        2: '#f59e0b', // Tidak Sesuai (Amber)
        1: '#ef4444', // Sangat Tidak Sesuai (Rose)
        0: '#94a3b8'  // Tidak Berlaku (Slate)
    };

    // Filter cascading Fakultas -> Prodi
    $('#select-fakultas').on('change', function(){
        var fak = $(this).val();
        $('#select-prodi option').each(function(){
            var optFak = $(this).data('fakultas');
            if(!fak || optFak == fak){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        // Select first visible option
        var firstVisible = $('#select-prodi option:visible').first();
        if(firstVisible.length){
            $('#select-prodi').val(firstVisible.val());
        }
    });

    $('#btn-apply-filter').on('click', function(){
        loadProdiData();
    });

    function loadProdiData() {
        var kodeProdi = $('#select-prodi').val();
        var idMreg = $('#select-mreg').val();

        if(!kodeProdi){
            alert('Silakan pilih Program Studi terlebih dahulu.');
            return;
        }

        // Tampilkan loading states
        $('#tbody-dosen').html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Mengambil data dosen...</td></tr>');
        $('#tbody-kelas').html('<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Mengambil data kelas...</td></tr>');

        $.getJSON("{{ route('admin.report.prodi.data') }}", {
            kode_prodi: kodeProdi,
            id_mreg: idMreg
        }, function(res){
            var info = res.prodi_info || {};
            var kpi = res.summary_kpi || {};
            var dosenList = res.dosen_list || [];
            var kelasList = res.kelas_list || [];
            var distSkala = res.distribusi_skala || [];
            var komponen = res.komponen_scores || [];
            var monev = res.monev_summary || {};

            // 1. Update Banner & Header
            $('#banner-nama-prodi').text(info.nama_program_studi || 'Program Studi');
            $('#banner-fakultas').text(info.nama_fakultas || 'Fakultas');
            $('#banner-kode').text(info.kode_program_studi || '-');
            $('#banner-ta').text(info.tahun_akademik || '-');

            if(kpi.total_mhs > 0){
                $('#banner-score').text(Number(kpi.overall_avg).toFixed(2) + ' / 4.00');
                $('#banner-percent').text('Tingkat Mutu: ' + kpi.overall_percent + '%');
                $('#banner-predikat').text(kpi.predikat).attr('class', 'badge badge-' + kpi.badge + ' mt-1');
            } else {
                $('#banner-score').text('0.00 / 4.00');
                $('#banner-percent').text('Belum ada respon kuesioner');
                $('#banner-predikat').text('Belum Ada Data').attr('class', 'badge badge-secondary mt-1');
            }

            // 2. Update KPI Cards
            var avgColor = kpi.overall_avg >= 3.0 ? '#059669' : (kpi.overall_avg >= 2.0 ? '#d97706' : '#dc2626');
            $('#kpi-avg').html('<span style="color:' + avgColor + '">' + Number(kpi.overall_avg).toFixed(2) + '</span> <span style="font-size:13px; color:#64748b;">/ 4.00</span>');
            $('#kpi-percent').html('Mutu: <b>' + kpi.overall_percent + '%</b> (' + kpi.predikat + ')');
            $('#kpi-mhs').text(kpi.total_mhs + ' Mahasiswa');
            $('#kpi-answers').text(kpi.total_answers + ' Butir Respon Masuk');
            $('#kpi-dosen').text(kpi.total_dosen + ' Dosen');
            $('#kpi-kelas').text(kpi.total_kelas + ' Kelas Kuliah');

            $('#count-dosen').text(dosenList.length);
            $('#count-kelas').text(kelasList.length);

            // 3. Render Tab 1: Kinerja Dosen
            renderDosenTable(dosenList);

            // 4. Render Tab 2: Rekapitulasi Kelas
            renderKelasTable(kelasList);

            // 5. Render Tab 3: Persepsi Mahasiswa & Aspek Mutu
            renderPersepsiCharts(distSkala, komponen);

            // 6. Render Tab 4: Monev Mutu
            renderMonev(monev);

        }).fail(function(){
            alert('Terjadi kesalahan saat memuat laporan program studi.');
        });
    }

    function renderDosenTable(list) {
        var tbody = $('#tbody-dosen');
        tbody.empty();

        if(!list.length){
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada dosen yang mengajar atau dievaluasi pada prodi ini.</td></tr>');
            return;
        }

        list.forEach(function(d, idx){
            var barColor = d.avg_score >= 3.5 ? '#10b981' : (d.avg_score >= 3.0 ? '#3b82f6' : (d.avg_score >= 2.0 ? '#f59e0b' : '#ef4444'));
            var tr = '<tr>' +
                '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
                '<td>' +
                    '<div class="font-weight-600 text-dark">' + d.nama + '</div>' +
                '</td>' +
                '<td>' + d.nip + '</td>' +
                '<td class="text-center font-weight-bold">' + d.total_kelas + '</td>' +
                '<td class="text-center font-weight-bold">' + d.total_responden + '</td>' +
                '<td>' +
                    '<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">' +
                        '<strong>' + Number(d.avg_score).toFixed(2) + ' <span class="text-muted" style="font-size:10px;">/ 4.00</span></strong>' +
                        '<span class="font-weight-600" style="color:' + barColor + '">' + d.percent + '%</span>' +
                    '</div>' +
                    '<div class="progress progress-score">' +
                        '<div class="progress-bar" style="width:' + d.percent + '%; background-color:' + barColor + ';"></div>' +
                    '</div>' +
                '</td>' +
                '<td class="text-center">' +
                    '<span class="badge badge-' + d.badge + '" style="font-size:11px; padding:4px 8px;">' + d.predikat + '</span>' +
                '</td>' +
            '</tr>';
            tbody.append(tr);
        });
    }

    function renderKelasTable(list) {
        var tbody = $('#tbody-kelas');
        tbody.empty();

        if(!list.length){
            tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">Belum ada kelas kuliah yang dibuka pada semester ini.</td></tr>');
            return;
        }

        list.forEach(function(k, idx){
            var barColor = k.avg_score >= 3.5 ? '#10b981' : (k.avg_score >= 3.0 ? '#3b82f6' : (k.avg_score >= 2.0 ? '#f59e0b' : '#ef4444'));
            var tr = '<tr>' +
                '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
                '<td><span class="badge badge-light font-weight-600">' + (k.kode_matakuliah || '-') + '</span></td>' +
                '<td>' +
                    '<div class="font-weight-600 text-dark">' + k.nama_matakuliah + '</div>' +
                    '<small class="text-primary font-weight-600">Kelas ' + k.nama_kelas + '</small>' +
                '</td>' +
                '<td>' + k.nama_dosen + '</td>' +
                '<td class="text-center">' + (k.semester || '-') + '</td>' +
                '<td class="text-center font-weight-bold">' + k.total_mhs + '</td>' +
                '<td>' +
                    '<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">' +
                        '<strong>' + Number(k.avg_score).toFixed(2) + ' <span class="text-muted" style="font-size:10px;">/ 4.00</span></strong>' +
                        '<span class="font-weight-600" style="color:' + barColor + '">' + k.percent + '%</span>' +
                    '</div>' +
                    '<div class="progress progress-score">' +
                        '<div class="progress-bar" style="width:' + k.percent + '%; background-color:' + barColor + ';"></div>' +
                    '</div>' +
                '</td>' +
                '<td class="text-center">' +
                    '<span class="badge badge-' + k.badge + '" style="font-size:11px; padding:4px 8px;">' + k.predikat + '</span>' +
                '</td>' +
                '<td class="text-center">' +
                    '<a href="{{ url("/admin/kelas") }}/' + k.id_kelas + '/soal" class="btn btn-xs btn-outline-info" title="Lihat Analisis Butir Soal">' +
                        '<i class="fa fa-chart-pie mr-1"></i> Soal' +
                    '</a>' +
                '</td>' +
            '</tr>';
            tbody.append(tr);
        });
    }

    function renderPersepsiCharts(distSkala, komponen) {
        // A. Table Skala
        var tbodySkala = $('#tbody-skala');
        tbodySkala.empty();
        distSkala.forEach(function(d){
            var clr = colorMap[d.jawaban] || '#64748b';
            var tr = '<tr>' +
                '<td><span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:' + clr + '; margin-right:6px;"></span> ' + d.jawaban + ' - ' + d.name + '</td>' +
                '<td class="text-right font-weight-bold">' + d.count + '</td>' +
                '<td class="text-right font-weight-600">' + d.percentage + '%</td>' +
            '</tr>';
            tbodySkala.append(tr);
        });

        // B. Donut Chart ECharts
        var domPie = document.getElementById('chart-pie-prodi');
        if(domPie){
            if(chartPie) { try { chartPie.dispose(); } catch(e){} }
            chartPie = echarts.init(domPie);

            var pieData = distSkala.filter(function(d){ return d.count > 0; }).map(function(d){
                return {
                    value: d.count,
                    name: d.jawaban + ' - ' + d.name,
                    itemStyle: { color: colorMap[d.jawaban] || '#64748b' }
                };
            });

            if(!pieData.length){
                pieData = [{ value: 1, name: 'Belum Ada Respon', itemStyle: { color: '#e2e8f0' } }];
            }

            chartPie.setOption({
                tooltip: { trigger: 'item', formatter: '{b}<br/><b>{c} Respon ({d}%)</b>' },
                series: [{
                    type: 'pie',
                    radius: ['45%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: { borderRadius: 6, borderColor: '#ffffff', borderWidth: 2 },
                    label: { show: false, position: 'center' },
                    emphasis: {
                        label: { show: true, fontSize: '13', fontWeight: 'bold', formatter: '{b}\n{d}%' }
                    },
                    data: pieData
                }]
            });
        }

        // C. Table Komponen
        var tbodyKomp = $('#tbody-komponen');
        tbodyKomp.empty();
        komponen.forEach(function(c){
            var tr = '<tr>' +
                '<td class="font-weight-600">' + c.nama_komponen + '</td>' +
                '<td class="text-center font-weight-bold">' + c.responses + '</td>' +
                '<td class="text-right font-weight-bold">' + Number(c.avg_score).toFixed(2) + '</td>' +
                '<td class="text-right font-weight-600 text-primary">' + c.percent + '%</td>' +
            '</tr>';
            tbodyKomp.append(tr);
        });

        // D. Bar Chart Komponen
        var domBar = document.getElementById('chart-komponen-prodi');
        if(domBar){
            if(chartBar) { try { chartBar.dispose(); } catch(e){} }
            chartBar = echarts.init(domBar);

            var barNames = komponen.map(function(c){ return c.nama_komponen; });
            var barScores = komponen.map(function(c){ return c.avg_score; });

            chartBar.setOption({
                tooltip: { trigger: 'axis', formatter: '{b}: <b>{c} / 4.00</b>' },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'value', min: 0, max: 4 },
                yAxis: { type: 'category', data: barNames },
                series: [{
                    type: 'bar',
                    data: barScores,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            { offset: 0, color: '#3b82f6' },
                            { offset: 1, color: '#2563eb' }
                        ]),
                        borderRadius: [0, 4, 4, 0]
                    },
                    label: { show: true, position: 'right', formatter: '{c}' }
                }]
            });
        }
    }

    function renderMonev(monev) {
        var best = monev.best_dosen || [];
        var support = monev.need_support_dosen || [];

        var tbodyBest = $('#tbody-monev-best');
        tbodyBest.empty();
        if(!best.length){
            tbodyBest.html('<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada dosen yang mencapai kriteria unggul ($\ge 3.50$).</td></tr>');
        } else {
            best.forEach(function(d, idx){
                var tr = '<tr>' +
                    '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
                    '<td><div class="font-weight-600 text-dark">' + d.nama + '</div></td>' +
                    '<td>' + d.nip + '</td>' +
                    '<td class="text-center font-weight-bold">' + d.total_kelas + '</td>' +
                    '<td class="text-center font-weight-bold text-success">' + d.total_responden + '</td>' +
                    '<td class="text-right font-weight-800 text-success" style="font-size:15px;">' + Number(d.avg_score).toFixed(2) + ' ★</td>' +
                    '<td class="text-center"><span class="badge badge-success">' + d.percent + '%</span></td>' +
                '</tr>';
                tbodyBest.append(tr);
            });
        }

        var tbodySupport = $('#tbody-monev-support');
        tbodySupport.empty();
        if(!support.length){
            tbodySupport.html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada dosen yang memerlukan pendampingan khusus pada prodi ini.</td></tr>');
        } else {
            support.forEach(function(d, idx){
                var tr = '<tr>' +
                    '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
                    '<td><div class="font-weight-600 text-dark">' + d.nama + '</div></td>' +
                    '<td>' + d.nip + '</td>' +
                    '<td class="text-center font-weight-bold">' + d.total_kelas + '</td>' +
                    '<td class="text-center font-weight-bold text-warning">' + d.total_responden + '</td>' +
                    '<td class="text-right font-weight-800 text-danger" style="font-size:15px;">' + Number(d.avg_score).toFixed(2) + '</td>' +
                    '<td class="text-center"><span class="badge badge-' + d.badge + '">' + d.predikat + '</span></td>' +
                '</tr>';
                tbodySupport.append(tr);
            });
        }
    }

    // Auto load data pertama kali
    loadProdiData();

    // Resize handlers
    window.addEventListener('resize', function(){
        if(chartPie) chartPie.resize();
        if(chartBar) chartBar.resize();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        if(chartPie) chartPie.resize();
        if(chartBar) chartBar.resize();
    });
});
</script>
@endsection