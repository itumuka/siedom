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
                                        <div id="basic-pie" style="height:400px; width:800px;"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-xl-6 col-12">
                                <div class="box">
                                    <div class="box-body analytics-info">
                                        <div id="basic-doughnut" style="height:400px;"></div>
                                    </div>
                                </div>
                            </div> --}}
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

                    response.data.forEach(function(item) {
                        answers[item.jawaban] = item.count;
                    });

                    document.getElementById('matakuliah-nama').textContent = 'Matakuliah: ' + matakuliahNama;
                    document.getElementById('matakuliah-kode').textContent = 'Kode: ' + matakuliahKode;

                    // Check if all answer counts are zero (i.e., no responses)
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
