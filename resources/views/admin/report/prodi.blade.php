@extends('layouts.master_sidebar')

@section('title','Report Prodi')

@section('css')
<style>
    .container-full { max-width:1200px; margin:0 auto; padding:0 24px; }
    .dashboard-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
    .dashboard-filter-group { display:flex; gap:8px; align-items:center; }
    #dosen-table .list-group-item { cursor:pointer; }
    #prodi-chart { height:360px; }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endsection

@section('content')
<div class="container-full">
    <div class="dashboard-header">
        <h2 class="mb-0">Report Program Studi</h2>
        <div class="dashboard-filter-group">
            <select id="mode" class="form-control">
                <option value="universal">Universal (Prodi)</option>
                <option value="dosen">Per-Dosen</option>
                <option value="kelas">Per-Kelas (drilldown)</option>
            </select>
            <select id="dosen-select" class="form-control" style="display:none; min-width:260px;">
                <option value="">Pilih Dosen</option>
            </select>
            <button id="btn-load" class="btn btn-primary"><i class="fas fa-magnifying-glass"></i></button>
        </div>
    </div>

    <div class="box mb-4">
        <div class="box-header"><h4 id="report-title">Report Prodi</h4></div>
        <div class="box-body">
            <div id="prodi-chart"></div>
            <div id="report-content" class="mt-3"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header"><h5>Daftar Dosen / Kelas</h5></div>
                <div class="box-body">
                    <div id="dosen-table"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header"><h5>Detail Drilldown</h5></div>
                <div class="box-body" id="drilldown-area">
                    <p class="text-muted">Pilih mode dan muat data untuk melihat detail.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
<script>
function loadDosenList() {
    $.get('/report/prodi/dosen', function(res){
        var select = $('#dosen-select');
        select.empty();
        select.append('<option value="">Pilih Dosen</option>');
        $('#dosen-table').empty();

        if (!res || !Array.isArray(res.list) || res.list.length === 0) {
            select.append('<option value="">-- Tidak ada dosen --</option>');
            $('#dosen-table').html('<div class="text-muted">Tidak ada data dosen untuk prodi / tahun akademik aktif.</div>');
            return;
        }

        res.list.forEach(function(d){
            select.append(`<option value="${d.id_pegawai}">${d.nama} (${d.nip})</option>`);
        });

        // render daftar clickable
        renderDosenTable(res.list);
    }).fail(function(xhr){
        console.error('/report/prodi/dosen error', xhr);
        $('#dosen-table').html('<div class="text-danger">Gagal memuat daftar dosen. Cek console/network.</div>');
    });
}

function loadUniversalChart() {
    $.get('/report/prodi/universal')
    .done(function(res){
        console.log('universal', res);
        if (!res || !Array.isArray(res.pieData) || res.total === 0) {
            // tampilkan pesan dan kosongkan chart area
            $('#prodi-chart').html('<div class="text-muted p-3">Tidak ada data rekap untuk prodi / tahun akademik aktif.</div>');
            return;
        }
        renderUniversalChart(res.pieData);
    })
    .fail(function(xhr){
        console.error('/report/prodi/universal error', xhr);
        $('#prodi-chart').html('<div class="text-danger p-3">Gagal memuat chart. Cek console/network.</div>');
    });
}

function renderUniversalChart(pieData) {
    var chartEl = document.getElementById('prodi-chart');
    chartEl.innerHTML = ''; // reset
    var chart = echarts.init(chartEl);
    chart.setOption({
        title:{ text:'Rekap Semua Jawaban Prodi', left:'center' },
        tooltip:{ trigger:'item', formatter:'{b}: {c} ({d}%)' },
        series:[{ type:'pie', radius:'60%', data: pieData.map(i=>({name:i.name,value:i.value})) }]
    });
    window.addEventListener('resize', function(){ chart.resize(); });
}

function renderDosenTable(list) {
    var html = '<div class="list-group">';
    list.forEach(function(d){
        html += `<a href="#" class="list-group-item list-group-item-action" data-id="${d.id_pegawai}">${d.nama} (${d.nip}) - ${d.avg} (${d.percent}%) <span class="float-end text-muted">${d.responses} jwb</span></a>`;
    });
    html += '</div>';
    $('#dosen-table').html(html);
    // click drilldown
    $('#dosen-table a').on('click', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        // highlight active
        $('#dosen-table a').removeClass('active');
        $(this).addClass('active');
        loadPerKelas(id);
    });
}

function loadPerKelas(id_dosen) {
    $('#report-title').text('Per-Kelas: memuat...');
    $('#drilldown-area').html('<p class="text-muted">Memuat...</p>');
    $.get('/report/prodi/kelas', { id_dosen: id_dosen })
    .done(function(res){
        console.log('/report/prodi/kelas', res);
        if (!res || !Array.isArray(res.kelas) || res.kelas.length === 0) {
            $('#drilldown-area').html('<div class="text-muted">Tidak ada data kelas untuk dosen ini.</div>');
            $('#report-title').text('Per-Kelas (tidak ada data)');
            return;
        }
        var rows = res.kelas;
        var html = '<table class="table table-sm"><thead><tr><th>Kelas</th><th>Matakuliah</th><th>Rata-rata</th><th>Persen</th><th>Respon</th></tr></thead><tbody>';
        rows.forEach(function(r){
            html += `<tr><td>${r.nama_kelas}</td><td>${r.nama_matakuliah}</td><td>${r.avg}</td><td>${r.percent}%</td><td>${r.responses}</td></tr>`;
        });
        html += '</tbody></table>';
        $('#drilldown-area').html(html);
        $('#report-title').text('Per-Kelas (Drilldown)');
        // render mini bar chart of kelas avg
        var chart = echarts.init(document.getElementById('prodi-chart'));
        chart.setOption({
            title:{ text:'Rata-rata per Kelas', left:'center' },
            tooltip:{},
            xAxis:{ type:'category', data: rows.map(r=>r.nama_kelas) },
            yAxis:{ type:'value', max:4 },
            series:[{ type:'bar', data: rows.map(r=>r.avg) }]
        });
    })
    .fail(function(xhr){
        console.error('/report/prodi/kelas error', xhr);
        $('#drilldown-area').html('<div class="text-danger">Gagal memuat data kelas. Cek console/network.</div>');
    });
}

$(document).ready(function(){
    // initial loads
    loadDosenList();
    loadUniversalChart(); // tampilkan chart otomatis

    var modeEl = $('#mode'), dosenSelect = $('#dosen-select');
    modeEl.on('change', function(){
        var v = $(this).val();
        dosenSelect.toggle(v === 'kelas');
    });

    $('#btn-load').on('click', function(){
        var mode = $('#mode').val();
        $('#drilldown-area').html('<p class="text-muted">Memuat...</p>');
        if (mode === 'universal') {
            $('#report-title').text('Universal - Rekap Prodi');
            loadUniversalChart();
        } else if (mode === 'dosen') {
            $('#report-title').text('Per-Dosen - Ringkasan Prodi');
            $.get('/report/prodi/dosen', function(res){
                renderDosenTable(res.list || []);
                $('#prodi-chart').html('');
                $('#drilldown-area').html('<p class="text-muted">Klik nama dosen untuk drilldown per-kelas.</p>');
            });
        } else if (mode === 'kelas') {
            var id = $('#dosen-select').val();
            if (!id) { alert('Pilih dosen'); return; }
            loadPerKelas(id);
        }
    });
});
</script>
@endsection