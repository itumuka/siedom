@extends('layouts.master_sidebar')

@section('title', 'Detail Kelas')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="">
                    <div class="">
                        <div id="matakuliah-info" class="mb-4">
                            <h4 id="matakuliah-nama"></h4>
                            <h5 id="matakuliah-kode"></h5>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="">
                                    <div class="box-body analytics-info" id="no-answer">
                                        <div id="basic-pie" style="height:400px; width:100%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="">
                                    <div class="box-body analytics-info">
                                        <div style="height:400px;"><h5 id="total-mhs"></h5><h5 id="total-jwb"></h5><h5 id="rata-jwb"></h5></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>

<script type="text/javascript">
function calculateAverage(data) {
    var totalScore = 0;
    var totalCount = 0;

    data.forEach(function(item) {
        totalScore += item.jawaban * item.count;
        totalCount += item.count;
    });

    return totalCount === 0 ? 0 : totalScore / totalCount;
}

function fetchChartData() {
    return $.ajax({
        url: "{{ url('/admin/chart/data/jawaban-kelas/' . $id_kelas) }}",
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var answers = [0, 0, 0, 0, 0];
                var matakuliahNama = response.data[0].nama_matakuliah;
                var matakuliahKode = response.data[0].kode_matakuliah;
                var totalStudents = response.total_students;
                var totalResponses = response.total_responses;
                var averageScore = calculateAverage(response.data);  // Calculate average score

                response.data.forEach(function(item) {
                    answers[item.jawaban] = item.count;
                });

                // Display Matakuliah name, total students, total responses, and average score
                document.getElementById('matakuliah-nama').textContent = 'Matakuliah: ' + matakuliahNama;
                document.getElementById('matakuliah-kode').textContent = 'Kode: ' + matakuliahKode;
                document.getElementById('total-mhs').textContent = 'Total Mahasiswa: ' + totalStudents;
                document.getElementById('total-jwb').textContent = 'Total Jawaban: ' + totalResponses;
                document.getElementById('rata-jwb').textContent = 'Rata-rata Jawaban:' + averageScore.toFixed(2);

                // Check if all answer counts are zero
                if (answers.every(function(count) { return count === 0; })) {
                    showNoDataMessage();
                } else {
                    renderCharts(answers);
                }
            } else {
                showNoDataMessage();
            }
        },
        error: function(xhr) {
            console.error('Error fetching data:', xhr);
            showNoDataMessage();
        }
    });
}


function renderCharts(answers) {
    var pieChart = echarts.init(document.getElementById('basic-pie'));

    var pieOption = {
        title: {
            text: 'Jawaban Kelas',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        series: [{
            name: 'Jawaban',
            type: 'pie',
            radius: '70%',
            center: ['50%', '57.5%'],
            data: [
                {value: answers[1], name: 'Sangat Tidak Sesuai'},
                {value: answers[2], name: 'Tidak Sesuai'},
                {value: answers[3], name: 'Sesuai'},
                {value: answers[4], name: 'Sangat Sesuai'},
                {value: answers[0], name: 'Tidak Berlaku'}
            ],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },
            label: {
                formatter: '{b}: {c} ({d}%)'
            }
        }]
    };

    pieChart.setOption(pieOption);
}

function showNoDataMessage() {
    document.getElementById('no-answer').innerHTML = '<h4 class="text-center">Matakuliah ini belum diisi jawaban.</h4>';
}

$(document).ready(function() {
    fetchChartData();
});

</script>

@stop
