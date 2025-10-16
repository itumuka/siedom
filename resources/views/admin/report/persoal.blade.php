@extends('layouts.master_sidebar')

@section('title','Report Soal')

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
        min-width: 220px;
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
        <h2 class="mb-0">Report Soal</h2>
        <div class="dashboard-filter-group">
            <select id="soal-select" class="form-control">
                <option value="">Pilih Soal</option>
            </select>
            <button class="btn btn-primary" onclick="fetchPerSoalData()" title="Tampilkan">
                <i class="fas fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    <!-- Chart General -->
    <div class="box mb-4">
        <div class="box-header"><h4 id="soal-title">Pilih soal untuk melihat rekap</h4></div>
        <div class="box-body">
            <div id="persoal-chart" style="height:350px;"></div>
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
function loadSoalOptions() {
    $.get('/report/getsoal', function(res){
        var select = $('#soal-select');
        select.empty();
        select.append('<option value="">Pilih Soal</option>');
        res.forEach(function(item){
            select.append(`<option value="${item.id_soal}">${item.pertanyaan}</option>`);
        });
    });
}

function fetchPerSoalData() {
    var id_soal = $('#soal-select').val();
    var soalText = $('#soal-select option:selected').text();
    if (!id_soal) {
        $('#soal-title').text('Pilih soal untuk melihat rekap');
        $('#persoal-chart').empty();
        $('#pie-explanation').empty();
        $('#top-list').empty();
        $('#bottom-list').empty();
        return;
    }
    $('#soal-title').text(soalText);

    $.get('/report/persoal', { id_soal: id_soal }, function(res) {
        renderPersoalChart(res.pieData);
        renderPieExplanation(res.pieData, res.total);
        renderTopBottomList(res.topList, res.bottomList);
    });
}

function renderPersoalChart(pieData) {
    var chart = echarts.init(document.getElementById('persoal-chart'));
    chart.setOption({
        title: { text: 'Rekap Jawaban', left: 'center' },
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
        top.append(`<li class="list-group-item">${item.nama} (${item.nip}) - ${item.nilai} (${persen}%)</li>`);
    });
    bottomList.forEach(function(item) {
        var persen = ((item.nilai / 4) * 100).toFixed(2);
        bottom.append(`<li class="list-group-item">${item.nama} (${item.nip}) - ${item.nilai} (${persen}%)</li>`);
    });
}

$(document).ready(function() {
    loadSoalOptions();
    $('#soal-select').on('change', fetchPerSoalData);
});
</script>
@endsection