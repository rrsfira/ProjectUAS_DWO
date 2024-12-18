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
require('koneksi.php'); // Menghubungkan file koneksi database

// Query untuk mengambil data kategori, bulan, dan jumlah produk terjual
$sql = "SELECT dp.ProductCategory AS kategori, 
               dt.Bulan AS bulan,
               COUNT(DISTINCT fs.ProductID) AS jumlah_produk_terjual
        FROM factsales fs
        JOIN dimproduct dp ON fs.ProductID = dp.ProductID
        JOIN dimtimeall dt ON fs.TimeID = dt.TimeID
        GROUP BY dp.ProductCategory, dt.Bulan
        ORDER BY dp.ProductCategory, dt.Bulan";

$result = mysqli_query($mysqli, $sql);

if (!$result) {
    die("Error dalam query: " . mysqli_error($mysqli));
}

$hasil = array();

while ($row = mysqli_fetch_array($result)) {
    array_push($hasil, array(
        "kategori" => $row['kategori'],
        "bulan" => $row['bulan'],
        "jumlah_produk_terjual" => $row['jumlah_produk_terjual']
    ));
}

$data_produk = json_encode($hasil);
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
            <!-- Begin Page Content -->
            <div id="barchart2" class="grafik"></div>
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

<!-- Highcharts JavaScript -->
<script type="text/javascript">
    // Ambil data JSON dari PHP
    var data_produk = <?php echo $data_produk; ?>;

    // Variabel untuk kategori dan data series
    var categories = [];
    var seriesData = {};

    // Proses data JSON menjadi kategori dan jumlah produk
    data_produk.forEach(function (item) {
        // Tambahkan bulan ke kategori jika belum ada
        if (!categories.includes(item.bulan)) {
            categories.push(item.bulan);
        }

        // Siapkan array untuk kategori produk
        if (!seriesData[item.kategori]) {
            seriesData[item.kategori] = Array(categories.length).fill(0);
        }

        // Tambahkan data jumlah produk terjual ke index bulan
        var index = categories.indexOf(item.bulan);
        if (index !== -1) {
            seriesData[item.kategori][index] = parseInt(item.jumlah_produk_terjual);
        }
    });

    // Konversi seriesData menjadi array yang bisa digunakan Highcharts
    var series = [];
    for (var kategori in seriesData) {
        series.push({
            name: kategori,
            data: seriesData[kategori]
        });
    }

    // Konfigurasi Highcharts
    Highcharts.chart('barchart2', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Data Produk Terlaris per Kategori dan Bulan'
        },
        subtitle: {
            text: 'Source: Database projectuas_'
        },
        xAxis: {
            categories: categories,
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah Produk Terjual'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: 'gray'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y} produk</b></td></tr>' +
                '<tr><td colspan="2" style="color:gray;padding:0"><i>{point.percentage:.1f}% dari total bulan</i></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '10px'
                    }
                }
            }
        },
        colors: ['#D3DAA1', '#9FBFB1', '#D0A9A2', '#A3B8B9'], // Warna kategori baru
        series: series
    });
</script>


<!-- Core JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/js/sb-admin-2.min.js"></script>

</body>
</html>
