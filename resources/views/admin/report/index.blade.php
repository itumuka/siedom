@extends('layouts.master_sidebar')

@section('title','Dashboard')

@section('css')
<style type="text/css">
    #basic-pie {
        width: 100%;
        height: 100%;
        min-height: 300px;
        max-height: 400px;
    }
    .analytics-info {
        overflow: hidden; /* Menghindari elemen terpotong */
        height: auto;
    }
</style>
@stop

@section('content')
		<!-- Main content -->
		<section class="content">		    
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-body analytics-info">
                            <p class="text-fade">Jumlah Mahasiswa</p>
                            <div id="basic-pie"></div>
                        </div>
                    </div>
                </div>
            </div>            
		</section>
		<!-- /.content -->
@endsection

@section('script-master')
<script src="{{ URL::asset('assets/vendor_components/echarts/dist/echarts-en.min.js') }}"></script>

<script type="text/javascript">
    function fetchChartData() {
        return $.ajax({
            url: "{{ url('/admin/chart/data/jawaban') }}",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error(response.error);
                    return;
                }
                var completed = response.completed_students;
                var notCompleted = response.not_completed_students;

                renderCharts(completed, notCompleted);
            },
            error: function(xhr) {
                console.error('Error fetching data:', xhr);
            }
        });
    }

    function renderCharts(completed, notCompleted) {
    var pieChart = echarts.init(document.getElementById('basic-pie'));

    var pieOption = {
        title: {
            text: 'Questionnaire Completion',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        series: [{
            name: 'Students',
            type: 'pie',
            radius: '55%', // Menghindari terlalu besar
            center: ['50%', '50%'], // Tetap di tengah
            data: [
                { value: completed, name: 'Sudah Mengisi' },
                { value: notCompleted, name: 'Belum Mengisi' }
            ],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },
            label: {
                formatter: '{b}: {c} ({d}%)',
                position: 'outside',
                fontSize: 12
            },
            labelLine: {
                length: 10,
                length2: 15
            }
        }]
    };

    pieChart.setOption(pieOption);

    // Responsiveness
    window.addEventListener('resize', function() {
        pieChart.resize();
    });
}



    $(document).ready(function() {
        fetchChartData();
    });
</script>
@stop
