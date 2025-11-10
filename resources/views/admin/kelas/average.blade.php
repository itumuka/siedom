@extends('layouts.master_sidebar')

@section('title', 'Average Scores per Subject')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-body">
                    <h4>Average Scores per Subject</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Subject Name</th>
                                <th>Average Score</th>
                            </tr>
                        </thead>
                        <tbody id="average-scores">
                            <!-- Scores will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script-master')
<script type="text/javascript">
    // Function to fetch the average scores from the server
    function fetchAverageScores() {
        $.ajax({
            url: "{{ url('/admin/report/average-scores/' . $id_kelas) }}",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.data) {
                    var tableBody = '';
                    response.data.forEach(function(item) {
                        tableBody += '<tr>' +
                            '<td>' + item.nama_matakuliah + '</td>' +
                            '<td>' + parseFloat(item.avg_score).toFixed(2) + '</td>' +
                            '</tr>';
                    });
                    $('#average-scores').html(tableBody);
                } else {
                    $('#average-scores').html('<tr><td colspan="2">No data available</td></tr>');
                }
            },
            error: function(xhr) {
                console.error('Error fetching data:', xhr);
            }
        });
    }

    // Run the fetch function when the page is ready
    $(document).ready(function() {
        fetchAverageScores();
    });
</script>
@endsection
