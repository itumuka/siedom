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
<div class="container-full">
    <!-- Main content -->
    <section class="content">
        <div class="row align-items-end">
            <div class="col-xl-9 col-12">
                <div class="box bg-primary-light pull-up">
                    <div class="box-body p-xl-0">							
                        <div class="row align-items-center">
                            <div class="col-12 col-lg-3"><img src="../images/svg-icon/color-svg/custom-14.svg" alt=""></div>
                            <div class="col-12 col-lg-9">
                                <h2>Selamat Datang, Admin!</h2>
                                <p class="text-dark mb-0 fs-16">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-12">
                <div class="box bg-transparent no-shadow">
                    <div class="box-body p-xl-0 text-center">
                        <h3 class="px-30 mb-20">Lihat Report Kelas</h3>
                        <a href="{{ route('kelas.index') }}" class="waves-effect waves-light w-p100 btn btn-primary">
                            <i class="fa fa-file me-15"></i> Lihat Report
                        </a>
                    </div>
                </div>
            </div>            
        </div>
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
  </div>
    {{-- <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">{{ $title }}</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item" aria-current="page">{{ $parent_breadcrumb }}</li>
                                <li class="breadcrumb-item active" aria-current="page"></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Selamat Datang</h3>
                </div>
                <!-- /.box-header -->
                <!-- /.box-body -->
            </div>
        </section>
        <!-- /.content -->
    </div> --}}
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
            radius: '60%',
            center: ['50%', '50%'],
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

    // Pastikan chart responsif
    $(window).on('resize', function() {
        pieChart.resize();
    });
}



    $(document).ready(function() {
        fetchChartData();
    });
</script>
@stop
