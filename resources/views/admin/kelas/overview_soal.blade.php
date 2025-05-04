@extends('layouts.master_sidebar')

@section('title', 'Overview Semua Soal')

@section('content')
<section class="content">
  <div class="container">
    <h3>Detail Jawaban</h3>
    <div id="matakuliah-info" class="mb-4">
      <h4 id="dosen-nama">Dosen: …</h4>
      <h4 id="matakuliah-nama">Mata Kuliah: …</h4>
      <h5 id="matakuliah-kode">Kode: …</h5>
    </div>
    <div id="soal-container">
      <p class="text-center">Memuat soal...</p>
    </div>
  </div>
</section>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>
<script>
$(function(){
  $.getJSON("{{ url('/admin/kelas/'.$id_kelas.'/soal/data') }}", function(res){
    // 1) Tampilkan info matakuliah & dosen
    $('#dosen-nama').text('Dosen: ' + res.nama_dosen);
    $('#matakuliah-nama').text('Mata Kuliah: ' + res.nama_matakuliah);
    $('#matakuliah-kode').text('Kode: ' + res.kode_matakuliah);

    var raw = res.data;
    if(!raw.length){
      return $('#soal-container').html('<p class="text-center">Belum ada jawaban.</p>');
    }

    // 2) Group by soal
    var grouped = {};
    raw.forEach(r => {
      if(!grouped[r.id_soal]){
        grouped[r.id_soal] = { pertanyaan: r.pertanyaan, dist: [] };
      }
      grouped[r.id_soal].dist.push({ jawaban: r.jawaban, count: r.count });
    });

    // 3) Render blok per soal
    var labels = {
      0:'Tidak Berlaku',1:'Sangat Tidak Sesuai',2:'Tidak Sesuai',
      3:'Sesuai',4:'Sangat Sesuai'
    };
    var html = '';
    Object.keys(grouped).forEach(id=>{
      html += `
      <div class="box mb-4 p-3 border">
        <h5>Soal #${id}: ${grouped[id].pertanyaan}</h5>
        <div class="row">
          <div class="col-md-6">
            <div id="chart-${id}" style="width:100%;height:300px;"></div>
          </div>
          <div class="col-md-6">
            <table class="table table-sm">
              <thead><tr>
                <th>Jawaban (Kode & Label)</th>
                <th>Jumlah</th><th>%</th>
              </tr></thead>
              <tbody id="table-${id}"></tbody>
            </table>
          </div>
        </div>
      </div>`;
    });
    $('#soal-container').html(html);

    // 4) Render chart + table tiap soal
    Object.keys(grouped).forEach(id=>{
      var arr = grouped[id].dist;
      var total = arr.reduce((s,o)=>s+o.count,0);

      // chart
      var chart = echarts.init(document.getElementById('chart-'+id));
      chart.setOption({
        tooltip:{trigger:'item',formatter:'{b}: {c} ({d}%)'},
        series:[{
          type:'pie',
          radius:'60%',
          data: arr.map(o=>({
            value: o.count,
            name: `${o.jawaban} (${labels[o.jawaban]})`
          })),
          label:{formatter:'{b}: {c} ({d}%)'}
        }]
      });
      window.addEventListener('resize', ()=>chart.resize());

      // table
      var tb = $('#table-'+id);
      arr.forEach(o=>{
        var pct = total ? ((o.count/total*100).toFixed(1)+'%') : '0%';
        tb.append(`
          <tr>
            <td>${o.jawaban} (${labels[o.jawaban]})</td>
            <td>${o.count}</td>
            <td>${pct}</td>
          </tr>`);
      });
    });
  });
});
</script>
@endsection
