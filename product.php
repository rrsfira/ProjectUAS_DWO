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

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"
        type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/css/sb-admin-2.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="css/styleGraph.css">

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

</head>

<body id="page-top">

    <?php
    require('koneksi.php');
    $sql = "SELECT SUM(ActualCost) as total_cost FROM factproduct";
    $tot = mysqli_query($mysqli, $sql);
    $tot_cost = mysqli_fetch_row($tot);

    // Query untuk mengambil data scrapreason
    $sql = "SELECT 
                    s.Name as name, 
                    SUM(fp.ActualCost) * 100 / " . $tot_cost[0] . " as y, 
                    s.Name as drilldown
                FROM scrapreason s
                JOIN factproduct fp ON s.ScrapReasonID = fp.ScrapReasonID
                GROUP BY s.Name
                ORDER BY y DESC";
    $all_scrapreason = mysqli_query($mysqli, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($all_scrapreason)) {
        $data[] = [
            "name" => $row['name'],
            "y" => (float)$row['y'],
            "drilldown" => $row['drilldown']
        ];
    }

    $json_all_scrapreason = json_encode($data);

    // CHART KEDUA (DRILL DOWN)

    // Query untuk data drill-down per scrapreason dan bulan
    $sql = "SELECT 
                    s.Name as scrapreason_name, 
                    d.Bulan as bulan, 
                    SUM(fp.ActualCost) as cost_per_month
                FROM scrapreason s
                JOIN factproduct fp ON s.ScrapReasonID = fp.ScrapReasonID
                JOIN dimtimeall d ON d.TimeID = fp.TimeID
                GROUP BY s.Name, d.Bulan";
    $detail_scrapreason = mysqli_query($mysqli, $sql);

    $data_detail = [];
    while ($row = mysqli_fetch_assoc($detail_scrapreason)) {
        $data_detail[$row['scrapreason_name']][] = [
            $row['bulan'],
            (float)$row['cost_per_month']
        ];
    }

    $drilldown_data = [];
    foreach ($data_detail as $scrapreason => $details) {
        $drilldown_data[] = [
            "name" => $scrapreason,
            "id" => $scrapreason,
            "data" => $details
        ];
    }

    $json_drilldown_data = json_encode($drilldown_data);

    //PERSIAPAN DASHBOARD ATAS (KOTAK)
    //1. Total Customer
    $sql2 = "SELECT count(distinct `Name`) as jml_cust from scrapreason";
    $jml_c = mysqli_query($mysqli, $sql2);
    $jml_cust = mysqli_fetch_assoc($jml_c);

    //2. Total Sales
    $sql3 = "SELECT sum(ActualCost) as tot2 from factproduct";
    $tot2 = mysqli_query($mysqli, $sql3);
    $tot_penj = mysqli_fetch_assoc($tot2);

    //3. Total Judul Film
    $sql4 = "SELECT count(ProductName) as tot_jud_film from dimproduct";
    $tot3 = mysqli_query($mysqli, $sql4);
    $tot_jud_film = mysqli_fetch_assoc($tot3)

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

                <div class="row justify-content-center mt-5 mb-2"> <!-- Tambahkan mt-5 dan mb-2 -->
                    <!-- Small Box 1: Total Scrap Reason -->
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="small-box" style="background-color: #17a2b8; color: white; border-radius: 10px; position: relative; overflow: hidden;">
                            <div class="inner p-4">
                                <h3 style="margin: 0; font-size: 2rem; font-weight: bold;"><?php echo $jml_cust['jml_cust']; ?></h3>
                                <p style="margin: 0; font-size: 1.1rem; font-weight: 500;">Total Scrap Reason</p>
                            </div>
                            <div class="icon" style="position: absolute; top: 15px; right: 15px; opacity: 0.2;">
                                <i class="fas fa-user-plus fa-5x"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Small Box 2: Total Actual Cost -->
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="small-box" style="background-color: #28a745; color: white; border-radius: 10px; position: relative; overflow: hidden;">
                            <div class="inner p-4">
                                <h3 style="margin: 0; font-size: 2rem; font-weight: bold;"><?php echo number_format($tot_penj['tot2'], 2, ',', '.'); ?></h3>
                                <p style="margin: 0; font-size: 1.1rem; font-weight: 500;">Total Actual Cost</p>
                            </div>
                            <div class="icon" style="position: absolute; top: 15px; right: 15px; opacity: 0.2;">
                                <i class="fas fa-chart-bar fa-5x"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Small Box 3: Total Product -->
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="small-box" style="background-color: #ffc107; color: white; border-radius: 10px; position: relative; overflow: hidden;">
                            <div class="inner p-4">
                                <h3 style="margin: 0; font-size: 2rem; font-weight: bold;"><?php echo $tot_jud_film['tot_jud_film']; ?></h3>
                                <p style="margin: 0; font-size: 1.1rem; font-weight: 500;">Total Product</p>
                            </div>
                            <div class="icon" style="position: absolute; top: 15px; right: 15px; opacity: 0.2;">
                                <i class="fas fa-shopping-bag fa-5x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content bawah, kurangi jarak atasnya -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div id="container" class="grafik"></div>
                    </div>
                </div>

                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Dashboard ProjectUAS_DWO</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script type=" text/javascript">
        // Create the pie chart
        Highcharts.chart('container', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Persentase Biaya ScrapReason'
            },
            subtitle: {
                text: 'Klik untuk melihat rincian biaya per bulan'
            },

            accessibility: {
                announceNewData: {
                    enabled: true
                },
                point: {
                    valueSuffix: '%'
                }
            },

            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y:.1f}%'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> dari total<br />'
            },

            series: [{
                name: "Biaya By ScrapReason",
                colorByPoint: true,
                data: <?php echo $json_all_scrapreason; ?>
            }],
            drilldown: {
                series: <?php echo $json_drilldown_data; ?>
            }
        });
    </script>
    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/js/sb-admin-2.min.js"></script>


</body>

</html>