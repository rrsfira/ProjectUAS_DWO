<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Site Metas -->
    <link rel="shortcut icon" href="img/db.png" type="">

    <title>Dashboard Project UAS</title>

    <!-- Custom fonts and styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleGraph.css">

    <!-- Highcharts Library -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
</head>

<body id="page-top">
<?php
require('koneksi.php'); // Koneksi ke database

// Query untuk mengambil ActualCost per bulan
$sql_actualcost = "SELECT dt.Bulan AS bulan, 
                          SUM(fp.ActualCost) AS actual_cost
                   FROM factproduct fp
                   JOIN dimtimeall dt ON fp.TimeID = dt.TimeID
                   GROUP BY dt.Bulan
                   ORDER BY dt.Bulan";

$result_actualcost = mysqli_query($mysqli, $sql_actualcost);

$actualcost_data = [];
while ($row = mysqli_fetch_array($result_actualcost)) {
    $actualcost_data[] = [
        "bulan" => $row['bulan'],
        "actual_cost" => $row['actual_cost']
    ];
}
$data_actualcost_json = json_encode($actualcost_data);
?>
<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
        <div id="actualcost-chart" class="grafik"></div>
        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Dashboard WHSakila2021</span>
                </div>
            </div>
        </footer>
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->
<script>
// Ambil data dari PHP
var actualcostData = <?php echo $data_actualcost_json; ?>;

// Ambil bulan dan nilai ActualCost
var bulan = actualcostData.map(item => item.bulan);
var actualCost = actualcostData.map(item => parseFloat(item.actual_cost));

// Konfigurasi Highcharts Area Chart
Highcharts.chart('actualcost-chart', {
    chart: {
        type: 'area',
        backgroundColor: '#ffffff',
        borderRadius: 8,
        style: {
            fontFamily: 'Segoe UI, sans-serif'
        }
    },
    title: {
        text: 'Pendapatan ActualCost per Bulan',
        style: {
            color: '#444',
            fontWeight: 'bold',
            fontSize: '22px'
        }
    },
    subtitle: {
        text: 'Total ActualCost berdasarkan bulan',
        style: { color: '#777', fontSize: '14px' }
    },
    xAxis: {
        categories: bulan,
        title: { text: 'Bulan' },
        labels: {
            style: { color: '#666', fontSize: '12px' }
        },
        gridLineColor: '#eaeaea'
    },
    yAxis: {
        title: {
            text: 'ActualCost (Rupiah)',
            style: { color: '#666' }
        },
        labels: {
            style: { color: '#555' }
        },
        gridLineColor: '#f3f3f3'
    },
    tooltip: {
        backgroundColor: '#fefefe',
        borderColor: '#b3b3b3',
        borderRadius: 8,
        shadow: true,
        useHTML: true,
        style: { color: '#333' },
        formatter: function () {
            return `<b>Bulan: ${this.x}</b><br/>` +
                   `ActualCost: <b>Rp ${Highcharts.numberFormat(this.y, 0, ',', '.')}</b>`;
        }
    },
    plotOptions: {
        area: {
            marker: {
                enabled: true,
                symbol: 'circle',
                radius: 5,
                fillColor: '#fff',
                lineColor: '#22324C',  // Deep teal for the marker's border
                lineWidth: 2
            },
            lineWidth: 2.5,
            fillOpacity: 0.6,
            fillColor: {
                linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                },
                stops: [
                    [0, '#61C3DF'],  // Aqua Blue (start)
                    [0.25, '#20C6EC'],  // Light Blue (early transition)
                    [0.5, '#0090D1'],  // Sky Blue (middle)
                    [0.75, '#0B5693'],  // Strong Blue (late transition)
                    [1, '#22324C']   // Deep Teal (end)
                ]
            }
        }
    },
    series: [{
        name: 'ActualCost',
        data: actualCost,
        lineColor: '#5CC8E2',  // Lighter teal for the line (brighter than the gradient)
        marker: {
            fillColor: '#FFFFFF',
            lineWidth: 2,
            lineColor: '#22324C'  // Deep teal marker border
        }
    }],
    credits: {
        enabled: false
    }
});
</script>


<!-- Core JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/js/sb-admin-2.min.js"></script>

</body>
</html>

