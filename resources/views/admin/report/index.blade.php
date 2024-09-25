@extends('layouts.master_sidebar')

@section('title','Dashboard')

@section('content')
		<!-- Main content -->
		<section class="content">		    
			<div class="row">
				<div class="col-xl-6 col-12">
					<div class="">
						<div class="box-body analytics-info">
							<div id="basic-pie" style="height:400px; width:800px"></div>
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
                color: ['#4CAF50', '#FF9800'],
                text: 'Questionnaire Completion',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'  // Display percentage in tooltip
            },
            series: [{
                name: 'Students',
                type: 'pie',
                radius: '70%',
                center: ['50%', '57.5%'],
                data: [
                    {value: completed, name: 'Sudah Mengisi'},
                    {value: notCompleted, name: 'Belum Mengisi'}
                ],
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },
                label: {
                    formatter: '{b}: {c} ({d}%)'  // Show percentages next to labels
                }
            }]
        };

        pieChart.setOption(pieOption);

        var doughnutChart = echarts.init(document.getElementById('basic-doughnut'));

        var doughnutOption = {
            title: {
                text: 'Questionnaire Completion (Doughnut)',
                left: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'  // Display percentage in tooltip
            },
            series: [{
                name: 'Students',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['50%', '57.5%'],
                data: [
                    {value: completed, name: 'Sudah Mengisi'},
                    {value: notCompleted, name: 'Belum Mengisi'}
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

        doughnutChart.setOption(doughnutOption);
    }


    $(document).ready(function() {
        fetchChartData();
    });
</script>
@stop
