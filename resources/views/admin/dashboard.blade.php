@extends('layouts.master_sidebar')

@section('title','Dashboard')

@section('css')
<style>
    .container-full {
        max-width: 1200px;
        margin: 0 auto;
        padding-left: 24px;
        padding-right: 24px;
    }
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .dashboard-filter-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .dashboard-filter-group select {
        min-width: 120px;
    }
    .dashboard-filter-group .btn {
        padding: 6px 12px;
        font-size: 16px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endsection

@section('content')
<div class="container-full">
    <div class="dashboard-header">
        <h2 class="mb-0">Dashboard</h2>
        <div class="dashboard-filter-group">
            <select id="filter-type" class="form-control">
                <option value="universal">Universal (Semua)</option>
                <option value="fakultas">Fakultas</option>
                <option value="prodi">Prodi</option>
            </select>
            <select id="filter-fakultas" class="form-control" style="display:none;">
                <!-- Fakultas options, isi dari JS -->
            </select>
            <select id="filter-prodi" class="form-control" style="display:none;">
                <!-- Prodi options, isi dari JS -->
            </select>
            <button class="btn btn-primary" onclick="fetchDashboardData()" title="Terapkan Filter">
                <i class="fas fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    <!-- Chart General -->
    <div class="box mb-4">
        <div class="box-header"><h4>Tahun Ajaran {{ $tahun_ajaran }}</h4></div>
        <div class="box-body">
            <div id="general-chart" style="height:350px;"></div>
            <div id="pie-explanation" class="mt-3"></div>
        </div>
    </div>

    <!-- Top & Bottom List -->
    <div class="row top-bottom-list">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header"><h5>Top 3 Nilai Tertinggi (Dosen)</h5></div>
                <div class="box-body">
                    <ul id="top-list" class="list-group"></ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header"><h5>Bottom 3 Nilai Terendah (Dosen)</h5></div>
                <div class="box-body">
                    <ul id="bottom-list" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
<script>
function fetchDashboardData() {
    var type = $('#filter-type').val();
    var fakultas = $('#filter-fakultas').val();
    var prodi = $('#filter-prodi').val();

    $.get('/dashboard/general-dashboard', {
        type: type,
        fakultas: fakultas,
        prodi: prodi
    }, function(res) {
        renderGeneralChart(res.pieData);
        renderPieExplanation(res.pieData, res.total);
        renderTopBottomList(res.topList, res.bottomList);
    });
}

function renderGeneralChart(pieData) {
    var chart = echarts.init(document.getElementById('general-chart'));
    chart.setOption({
        title: { text: 'Rekap Semua Jawaban', left: 'center' },
        tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)' },
        series: [{
            type: 'pie',
            radius: '60%',
            data: pieData.map(function(item){
                return {value: item.value, name: item.name};
            })
        }]
    });
}

function renderPieExplanation(pieData, total) {
    // Hitung totalScore dan totalCount untuk persentase
    var totalScore = 0;
    var totalCount = 0;
    pieData.forEach(function(item, idx){
        totalScore += idx * item.value;
        totalCount += item.value;
    });
    var average = totalCount ? (totalScore / totalCount) : 0;
    var totalPercentage = totalCount ? ((average / 4) * 100).toFixed(2) : 0;

    var html = '<table class="table table-bordered"><thead><tr><th>Label</th><th>Jumlah</th><th>Persentase</th></tr></thead><tbody>';
    pieData.forEach(function(item){
        html += `<tr>
            <td>${item.name}</td>
            <td>${item.value}</td>
            <td>${item.percentage}%</td>
        </tr>`;
    });
    // Satu baris untuk total jawaban dan total persentase
    html += `<tr class="table-success font-weight-bold">
        <td>Total</td>
        <td>${total}</td>
        <td>${totalPercentage}%</td>
    </tr>`;
    html += '</tbody></table>';
    $('#pie-explanation').html(html);
}

function renderTopBottomList(topList, bottomList) {
    var top = $('#top-list'), bottom = $('#bottom-list');
    top.empty(); bottom.empty();
    topList.forEach(function(item) {
        var persen = ((item.nilai / 4) * 100).toFixed(2);
        top.append(`<li class="list-group-item">${item.nama} - ${item.nilai} (${persen}%)</li>`);
    });
    bottomList.forEach(function(item) {
        var persen = ((item.nilai / 4) * 100).toFixed(2);
        bottom.append(`<li class="list-group-item">${item.nama} - ${item.nilai} (${persen}%)</li>`);
    });
}

// Dropdown dinamis (isi fakultas/prodi dari endpoint, contoh AJAX)
function loadFakultas() {
    $.get('/api/fakultas', function(res){
        var select = $('#filter-fakultas');
        select.empty();
        res.forEach(function(item){
            select.append(`<option value="${item.kode_fakultas}">${item.nama_fakultas}</option>`);
        });
    });
}
function loadProdi() {
    $.get('/api/prodi', function(res){
        var select = $('#filter-prodi');
        select.empty();
        res.forEach(function(item){
            select.append(`<option value="${item.kode_program_studi}">${item.nama_program_studi}</option>`);
        });
    });
}

$('#filter-type').on('change', function() {
    var val = $(this).val();
    $('#filter-fakultas').toggle(val === 'fakultas');
    $('#filter-prodi').toggle(val === 'prodi');
    if(val === 'fakultas') loadFakultas();
    if(val === 'prodi') loadProdi();
});

// Panggil data awal
$(document).ready(function() {
    fetchDashboardData();
});
</script>
@endsection