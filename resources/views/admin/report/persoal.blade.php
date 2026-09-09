@extends('layouts.master_sidebar')

@section('title', 'Laporan Evaluasi Per Butir Soal')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    .report-soal-wrapper {
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

    /* Master Card */
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
    .dashboard-card-header h5,
    .dashboard-card-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dashboard-card-body {
        background: #ffffff;
    }

    /* Selected Soal Banner */
    .soal-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 22px 26px;
        margin-bottom: 22px;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
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
        padding: 10px 18px;
        text-align: right;
    }

    /* KPI Summary Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 18px;
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
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }
    .kpi-info .kpi-num {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }
    .kpi-info .kpi-sub {
        font-size: 11px;
        color: #64748b;
        margin-top: 3px;
        font-weight: 500;
    }
    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Table Custom */
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

    .selected-soal-row {
        background-color: #eff6ff !important;
        border-left: 4px solid #2563eb;
    }

    .progress-score {
        height: 6px;
        border-radius: 3px;
        background-color: #f1f5f9;
        margin-top: 4px;
    }

    /* Rank List Group */
    .rank-item {
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .rank-item:hover {
        background: #f8fafc;
    }
    .rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        margin-right: 12px;
    }

    @media print {
        .action-bar, .filter-card, .btn, .no-print, .sidebar, .main-header {
            display: none !important;
        }
        .soal-banner {
            background: #1e3a8a !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection

@section('content')
<div class="report-soal-wrapper">

    <!-- Action Bar -->
    <div class="action-bar">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-clipboard-question text-primary mr-2"></i>Laporan Evaluasi Per Butir Soal
            </h4>
            <small class="text-muted">Analisis distribusi persepsi mahasiswa dan peringkat performa dosen untuk setiap butir instrumen kuesioner.</small>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                <i class="fa fa-print mr-1"></i> Cetak Laporan Butir Soal
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
                    @if(isset($fakultas))
                        @foreach($fakultas as $f)
                            <option value="{{ $f->kode_fakultas }}">{{ $f->nama_fakultas }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                <label class="font-weight-bold text-muted mb-1" style="font-size: 12px;">Program Studi</label>
                <select id="select-prodi" class="form-control form-control-sm">
                    <option value="">-- Semua Program Studi --</option>
                    @if(isset($prodi))
                        @foreach($prodi as $p)
                            <option value="{{ $p->kode_program_studi }}" data-fakultas="{{ $p->kode_fakultas }}">
                                {{ $p->nama_program_studi }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label class="font-weight-bold text-muted mb-1" style="font-size: 12px;">Tahun Akademik</label>
                <select id="select-mreg" class="form-control form-control-sm">
                    @if(isset($mregList))
                        @foreach($mregList as $m)
                            <option value="{{ $m->id_mreg }}" {{ Session::get('id_mreg') == $m->id_mreg ? 'selected' : '' }}>
                                {{ $m->tahun }} ({{ $m->semester == '1' ? 'Ganjil' : 'Genap' }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 col-sm-6 mt-3 mt-md-4">
                <button type="button" id="btn-apply-filter" class="btn btn-primary btn-sm btn-block">
                    <i class="fa fa-magnifying-glass mr-1"></i> Terapkan
                </button>
            </div>
        </form>
    </div>

    <!-- Master Question Matrix Table -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5>
                <i class="fa fa-table-list text-primary"></i> Rekapitulasi Semua Butir Pertanyaan (<span id="total-soal-count">0</span>)
            </h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filter-komponen" class="form-control form-control-sm" style="width: auto; font-size: 12px;">
                    <option value="">-- Semua Komponen --</option>
                </select>
                <select id="sort-matrix" class="form-control form-control-sm ml-2" style="width: auto; font-size: 12px;">
                    <option value="id_asc">Urutan No Soal</option>
                    <option value="score_desc">Skor Tertinggi</option>
                    <option value="score_asc">Skor Terendah (Prioritas Evaluasi)</option>
                </select>
            </div>
        </div>
        <div class="dashboard-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover custom-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th style="width: 170px;">Komponen</th>
                            <th>Pernyataan Kuesioner</th>
                            <th style="width: 90px;" class="text-center">Responden</th>
                            <th style="width: 160px;">Rata-rata Skor</th>
                            <th style="width: 120px;" class="text-center">Predikat</th>
                            <th style="width: 90px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-matrix-soal">
                        <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Mengambil daftar butir soal...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DETAIL ANALISIS BUTIR SOAL TERPILIH -->
    <div id="section-detail-soal" style="scroll-margin-top: 80px;">

        <!-- Selected Question Banner -->
        <div class="soal-banner">
            <div>
                <div class="mb-2">
                    <span class="meta-chip"><i class="fa fa-hashtag"></i> <span id="banner-soal-id">Soal # -</span></span>
                    <span class="meta-chip"><i class="fa fa-layer-group"></i> <span id="banner-soal-komponen">Komponen</span></span>
                </div>
                <h4 id="banner-soal-pertanyaan" class="mb-1 font-weight-bold" style="line-height: 1.4;">
                    Pilih salah satu butir soal di atas untuk melihat analisis detail.
                </h4>
                <small class="text-white-50">
                    <i class="fa fa-info-circle mr-1"></i> Skala 0 (Tidak Berlaku) dieksklusikan dari perhitungan nilai rata-rata mutu.
                </small>
            </div>
            <div class="score-badge-card">
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85;">Rata-rata Butir Soal</div>
                <div id="banner-soal-score" style="font-size: 26px; font-weight: 800; line-height: 1.1;">-</div>
                <div id="banner-soal-percent" style="font-size: 12px; font-weight: 600; opacity: 0.9;">-</div>
                <span id="banner-soal-predikat" class="badge badge-light mt-1" style="font-size: 11px;">-</span>
            </div>
        </div>

        <!-- 4 KPI Cards for Selected Question -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-info">
                    <h6>Indeks Rata-rata</h6>
                    <div class="kpi-num" id="kpi-soal-avg">-</div>
                    <div class="kpi-sub" id="kpi-soal-percent">-</div>
                </div>
                <div class="kpi-icon" style="background: #e0e7ff; color: #4338ca;">
                    <i class="fa fa-star"></i>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h6>Responden Mahasiswa</h6>
                    <div class="kpi-num" id="kpi-soal-mhs">-</div>
                    <div class="kpi-sub">Mahasiswa Penilai</div>
                </div>
                <div class="kpi-icon" style="background: #ecfdf5; color: #059669;">
                    <i class="fa fa-user-check"></i>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h6>Respon Valid</h6>
                    <div class="kpi-num" id="kpi-soal-valid">-</div>
                    <div class="kpi-sub">Skala 1 s.d. 4</div>
                </div>
                <div class="kpi-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fa fa-circle-check"></i>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h6>Tidak Berlaku (N/A)</h6>
                    <div class="kpi-num" style="color: #64748b;" id="kpi-soal-na">-</div>
                    <div class="kpi-sub">Skala 0 (Eksklusi)</div>
                </div>
                <div class="kpi-icon" style="background: #f1f5f9; color: #64748b;">
                    <i class="fa fa-ban"></i>
                </div>
            </div>
        </div>

        <!-- Donut Chart & Distribution Table -->
        <div class="row">
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="dashboard-card mb-0" style="min-height: 420px;">
                    <div class="dashboard-card-header">
                        <h5>
                            <i class="fa fa-chart-pie text-success mr-1"></i> Proporsi Persepsi Mahasiswa
                        </h5>
                    </div>
                    <div class="dashboard-card-body p-3 d-flex align-items-center justify-content-center">
                        <div id="chart-soal-donut" style="width: 100%; height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12 mb-4">
                <div class="dashboard-card mb-0" style="min-height: 420px;">
                    <div class="dashboard-card-header">
                        <h5>
                            <i class="fa fa-list-check text-info mr-1"></i> Distribusi Frekuensi Skala Jawaban
                        </h5>
                    </div>
                    <div class="dashboard-card-body p-0">
                        <div class="table-responsive">
                            <table class="table custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Skala & Keterangan</th>
                                        <th class="text-right" style="width: 90px;">Jumlah</th>
                                        <th style="width: 170px;">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-soal-dist">
                                    <tr><td colspan="3" class="text-center py-4 text-muted">Pilih soal untuk melihat distribusi.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top & Bottom Dosen on this question -->
        <div class="row mt-4">
            <!-- Top 5 Dosen -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="dashboard-card mb-0">
                    <div class="dashboard-card-header">
                        <h6 class="text-success mb-0">
                            <i class="fa fa-trophy mr-1"></i> 5 Dosen Nilai Tertinggi (Kuorum &ge; 3 Mhs)
                        </h6>
                        <span class="badge badge-success">Top Performers</span>
                    </div>
                    <div class="dashboard-card-body p-0" id="list-top-dosen">
                        <div class="text-center py-4 text-muted">Belum ada data.</div>
                    </div>
                </div>
            </div>

            <!-- Bottom 5 Dosen -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="dashboard-card mb-0">
                    <div class="dashboard-card-header">
                        <h6 class="text-danger mb-0">
                            <i class="fa fa-triangle-exclamation mr-1"></i> 5 Dosen Prioritas Pembinaan (Kuorum &ge; 3 Mhs)
                        </h6>
                        <span class="badge badge-warning">Perlu Evaluasi</span>
                    </div>
                    <div class="dashboard-card-body p-0" id="list-bottom-dosen">
                        <div class="text-center py-4 text-muted">Belum ada data.</div>
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
    var rawSoalList = [];
    var currentActiveSoalId = null;
    var chartDonut = null;

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
            if(!fak || !optFak || optFak == fak){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#select-prodi').val('');
    });

    $('#btn-apply-filter').on('click', function(){
        loadMatrixSoal();
    });

    $('#filter-komponen, #sort-matrix').on('change', function(){
        renderMatrixTable();
    });

    function loadMatrixSoal() {
        var idMreg = $('#select-mreg').val();
        var kodeFakultas = $('#select-fakultas').val();
        var kodeProdi = $('#select-prodi').val();

        $('#tbody-matrix-soal').html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Mengambil daftar butir soal...</td></tr>');

        $.getJSON("{{ url('/report/getsoal') }}", {
            id_mreg: idMreg,
            kode_fakultas: kodeFakultas,
            kode_prodi: kodeProdi,
            with_stats: 1
        }, function(res){
            rawSoalList = res || [];
            $('#total-soal-count').text(rawSoalList.length);

            // Populate filter komponen options
            var kompSet = {};
            $('#filter-komponen').html('<option value="">-- Semua Komponen --</option>');
            rawSoalList.forEach(function(s){
                if(s.nama_komponen && !kompSet[s.nama_komponen]){
                    kompSet[s.nama_komponen] = true;
                    $('#filter-komponen').append('<option value="' + s.nama_komponen + '">' + s.nama_komponen + '</option>');
                }
            });

            renderMatrixTable();

            // Auto-load soal pertama jika ada
            if(rawSoalList.length > 0){
                loadDetailSoal(rawSoalList[0].id_soal);
            }
        }).fail(function(){
            $('#tbody-matrix-soal').html('<tr><td colspan="7" class="text-center py-4 text-danger"><i class="fa fa-exclamation-circle mr-1"></i> Gagal memuat daftar soal.</td></tr>');
        });
    }

    function renderMatrixTable() {
        var list = rawSoalList.slice();
        var komp = $('#filter-komponen').val();
        var sort = $('#sort-matrix').val();

        if(komp){
            list = list.filter(function(s){ return s.nama_komponen === komp; });
        }

        if(sort === 'score_desc'){
            list.sort(function(a,b){ return b.avg_score - a.avg_score; });
        } else if(sort === 'score_asc'){
            list.sort(function(a,b){ return a.avg_score - b.avg_score; });
        } else {
            list.sort(function(a,b){ return a.id_soal - b.id_soal; });
        }

        var tbody = $('#tbody-matrix-soal');
        tbody.empty();

        if(!list.length){
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada butir soal yang sesuai kriteria filter.</td></tr>');
            return;
        }

        list.forEach(function(s, idx){
            var barColor = s.avg_score >= 3.5 ? '#10b981' : (s.avg_score >= 3.0 ? '#3b82f6' : (s.avg_score >= 2.0 ? '#f59e0b' : '#ef4444'));
            var isSelected = s.id_soal == currentActiveSoalId;
            var tr = '<tr class="' + (isSelected ? 'selected-soal-row' : '') + '" id="row-soal-' + s.id_soal + '" style="cursor:pointer;">' +
                '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
                '<td><span class="badge badge-light" style="font-size:11px; padding:4px 8px; border:1px solid #e2e8f0;">' + (s.nama_komponen || 'Umum') + '</span></td>' +
                '<td>' +
                    '<div class="font-weight-600 text-dark">' + s.pertanyaan + '</div>' +
                    '<small class="text-muted">ID Soal: #' + s.id_soal + '</small>' +
                '</td>' +
                '<td class="text-center font-weight-bold">' + s.valid_count + '</td>' +
                '<td>' +
                    '<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">' +
                        '<strong>' + Number(s.avg_score).toFixed(2) + ' <span class="text-muted" style="font-size:10px;">/ 4.00</span></strong>' +
                        '<span class="font-weight-600" style="color:' + barColor + '">' + s.percent + '%</span>' +
                    '</div>' +
                    '<div class="progress progress-score">' +
                        '<div class="progress-bar" style="width:' + s.percent + '%; background-color:' + barColor + ';"></div>' +
                    '</div>' +
                '</td>' +
                '<td class="text-center">' +
                    '<span class="badge badge-' + (s.badge || 'secondary') + '" style="font-size:11px; padding:4px 8px;">' + s.predikat + '</span>' +
                '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-xs btn-' + (isSelected ? 'primary' : 'outline-primary') + ' btn-select-soal" data-id="' + s.id_soal + '" title="Lihat Analisis">' +
                        '<i class="fa fa-chart-pie mr-1"></i> Detail' +
                    '</button>' +
                '</td>' +
            '</tr>';
            tbody.append(tr);
        });

        // Highlight current active row
        if(currentActiveSoalId){
            $('#row-soal-' + currentActiveSoalId).addClass('selected-soal-row');
            $('#row-soal-' + currentActiveSoalId).find('.btn-select-soal').removeClass('btn-outline-primary').addClass('btn-primary');
        }
    }

    // Delegated click handler to select question from matrix table
    $(document).off('click', '#tbody-matrix-soal tr').on('click', '#tbody-matrix-soal tr', function(e){
        var id = $(this).find('.btn-select-soal').data('id');
        if(id){
            loadDetailSoal(id, true);
        }
    });

    function loadDetailSoal(idSoal, shouldScroll) {
        currentActiveSoalId = idSoal;

        // Highlight selected row in table
        $('#tbody-matrix-soal tr').removeClass('selected-soal-row');
        $('#tbody-matrix-soal .btn-select-soal').removeClass('btn-primary').addClass('btn-outline-primary');
        var selRow = $('#row-soal-' + idSoal);
        if(selRow.length){
            selRow.addClass('selected-soal-row');
            selRow.find('.btn-select-soal').removeClass('btn-outline-primary').addClass('btn-primary');
        }

        var idMreg = $('#select-mreg').val();
        var kodeFakultas = $('#select-fakultas').val();
        var kodeProdi = $('#select-prodi').val();

        $.getJSON("{{ url('/report/persoal') }}", {
            id_soal: idSoal,
            id_mreg: idMreg,
            kode_fakultas: kodeFakultas,
            kode_prodi: kodeProdi
        }, function(res){
            var info = res.soal_info || {};
            var summary = res.summary || {};
            var pieData = res.pieData || [];
            var topList = res.topList || [];
            var bottomList = res.bottomList || [];

            // 1. Update Banner
            $('#banner-soal-id').text('Soal #' + (info.id_soal || idSoal));
            $('#banner-soal-komponen').text(info.nama_komponen || 'Umum');
            $('#banner-soal-pertanyaan').text(info.pertanyaan || 'Pertanyaan Kuesioner');
            $('#banner-soal-score').text(Number(summary.avg_score || 0).toFixed(2) + ' / 4.00');
            $('#banner-soal-percent').text('Tingkat Kesesuaian: ' + (summary.percent || 0) + '%');
            $('#banner-soal-predikat').text(summary.predikat || '-').attr('class', 'badge badge-' + (summary.badge || 'secondary') + ' mt-1');

            // 2. Update 4 KPI Cards
            var avgColor = summary.avg_score >= 3.0 ? '#059669' : (summary.avg_score >= 2.0 ? '#d97706' : '#dc2626');
            $('#kpi-soal-avg').html('<span style="color:' + avgColor + '">' + Number(summary.avg_score || 0).toFixed(2) + '</span> <span style="font-size:12px; color:#64748b;">/ 4.00</span>');
            $('#kpi-soal-percent').html('Mutu: <b>' + (summary.percent || 0) + '%</b> (' + (summary.predikat || '-') + ')');
            $('#kpi-soal-mhs').text((summary.total_mhs || 0) + ' Mahasiswa');
            $('#kpi-soal-valid').text((summary.total_valid || 0) + ' Jawaban');
            $('#kpi-soal-na').text((summary.total_na || 0) + ' N/A');

            // 3. Render Donut Chart
            renderSoalDonut(pieData);

            // 4. Render Distribution Table
            renderSoalDistributionTable(pieData, summary);

            // 5. Render Top & Bottom Lecturers
            renderRankLists(topList, bottomList);

            if(shouldScroll){
                var target = $('#section-detail-soal');
                if(target.length){
                    var targetOffset = Math.max(0, target.offset().top - 95);
                    $('html, body').stop().animate({
                        scrollTop: targetOffset
                    }, 400);
                }
            }

            setTimeout(function(){
                if(chartDonut) chartDonut.resize();
            }, 100);
        });
    }

    function renderSoalDonut(pieData) {
        var domChart = document.getElementById('chart-soal-donut');
        if(!domChart) return;

        if(chartDonut) { try { chartDonut.dispose(); } catch(e){} }
        chartDonut = echarts.init(domChart);

        var data = pieData.filter(function(d){ return d.value > 0; }).map(function(d){
            return {
                value: d.value,
                name: d.jawaban + ' - ' + d.name,
                itemStyle: { color: colorMap[d.jawaban] || '#64748b' }
            };
        });

        if(!data.length){
            data = [{ value: 1, name: 'Belum Ada Respon', itemStyle: { color: '#e2e8f0' } }];
        }

        chartDonut.setOption({
            tooltip: { trigger: 'item', formatter: '{b}<br/><b>{c} Respon ({d}%)</b>' },
            series: [{
                type: 'pie',
                radius: ['45%', '72%'],
                avoidLabelOverlap: false,
                itemStyle: { borderRadius: 6, borderColor: '#ffffff', borderWidth: 2 },
                label: { show: false, position: 'center' },
                emphasis: {
                    label: { show: true, fontSize: '13', fontWeight: 'bold', formatter: '{b}\n{d}%' }
                },
                data: data
            }]
        });
    }

    function renderSoalDistributionTable(pieData, summary) {
        var tbody = $('#tbody-soal-dist');
        tbody.empty();

        var sumCount = 0;
        var sumPct = 0;

        pieData.forEach(function(d){
            var clr = colorMap[d.jawaban] || '#64748b';
            sumCount += d.value;
            sumPct += Number(d.percentage);

            var tr = '<tr>' +
                '<td>' +
                    '<span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:' + clr + '; margin-right:6px;"></span>' +
                    '<b>' + d.jawaban + '</b> - ' + d.name +
                '</td>' +
                '<td class="text-right font-weight-bold">' + d.value + '</td>' +
                '<td>' +
                    '<div class="d-flex justify-content-between align-items-center" style="font-size:11px;">' +
                        '<span class="font-weight-600">' + d.percentage + '%</span>' +
                    '</div>' +
                    '<div class="progress" style="height:5px; background:#f1f5f9; border-radius:3px; margin-top:2px;">' +
                        '<div class="progress-bar" style="width:' + d.percentage + '%; background-color:' + clr + ';"></div>' +
                    '</div>' +
                '</td>' +
            '</tr>';
            tbody.append(tr);
        });

        // Baris Total Akumulatif (100%)
        var totalTr = '<tr class="bg-light font-weight-bold" style="border-top: 2px solid #cbd5e1;">' +
            '<td><i class="fa fa-calculator text-primary mr-1"></i> TOTAL AKUMULATIF</td>' +
            '<td class="text-right">' + sumCount + '</td>' +
            '<td><span class="badge badge-primary">100.00%</span></td>' +
        '</tr>';
        tbody.append(totalTr);
    }

    function renderRankLists(topList, bottomList) {
        var topBox = $('#list-top-dosen');
        topBox.empty();

        if(!topList.length){
            topBox.html('<div class="text-center py-4 text-muted">Belum ada dosen yang memenuhi kuorum.</div>');
        } else {
            topList.forEach(function(d, idx){
                var item = '<div class="rank-item">' +
                    '<div class="d-flex align-items-center">' +
                        '<div class="rank-badge bg-success text-white">' + (idx + 1) + '</div>' +
                        '<div>' +
                            '<div class="font-weight-600 text-dark">' + d.nama + '</div>' +
                            '<small class="text-muted">NIP: ' + d.nip + ' | Responden: <b>' + d.responden + '</b></small>' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-right">' +
                        '<div class="font-weight-800 text-success" style="font-size:16px;">' + Number(d.nilai).toFixed(2) + ' ★</div>' +
                        '<small class="text-muted">' + d.percent + '%</small>' +
                    '</div>' +
                '</div>';
                topBox.append(item);
            });
        }

        var bottomBox = $('#list-bottom-dosen');
        bottomBox.empty();

        if(!bottomList.length){
            bottomBox.html('<div class="text-center py-4 text-muted">Belum ada dosen yang memenuhi kuorum.</div>');
        } else {
            bottomList.forEach(function(d, idx){
                var item = '<div class="rank-item">' +
                    '<div class="d-flex align-items-center">' +
                        '<div class="rank-badge bg-danger text-white">' + (idx + 1) + '</div>' +
                        '<div>' +
                            '<div class="font-weight-600 text-dark">' + d.nama + '</div>' +
                            '<small class="text-muted">NIP: ' + d.nip + ' | Responden: <b>' + d.responden + '</b></small>' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-right">' +
                        '<div class="font-weight-800 text-danger" style="font-size:16px;">' + Number(d.nilai).toFixed(2) + '</div>' +
                        '<small class="text-muted">' + d.percent + '%</small>' +
                    '</div>' +
                '</div>';
                bottomBox.append(item);
            });
        }
    }

    // Auto load data pertama kali
    loadMatrixSoal();

    window.addEventListener('resize', function(){
        if(chartDonut) chartDonut.resize();
    });
});
</script>
@endsection