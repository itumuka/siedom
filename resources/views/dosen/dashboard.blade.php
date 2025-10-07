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
    .tahun-akademik-info {
        font-size: 1.1em;
        color: #555;
        margin-bottom: 12px;
        font-weight: 500;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endsection

@section('content')
<div class="container-full">
    <div class="dashboard-header">
        <div>
            <h2 class="mb-0">Dashboard</h2>
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
    var totalScore = 0;
    var totalCount = 0;
    pieData.forEach(function(item, idx){
        // Jawaban 0-4, skor = idx * jumlah
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
    html += `<tr class="table-success font-weight-bold">
        <td colspan="1">Total Jawaban</td>
        <td>${total}</td>
        <td>${totalPercentage}%</td>
    </tr>`;
    html += '</tbody></table>';
    $('#pie-explanation').html(html);
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