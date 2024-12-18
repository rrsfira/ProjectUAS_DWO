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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.3/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleGraph.css">

    <!-- Highcharts Scripts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
</head>

<body id="page-top">

    <?php
    // Database queries
    require('koneksi.php');

    // Get total sales amount
    $sql = "SELECT SUM(SalesAmount) as tot FROM factsales";
    $tot = mysqli_query($mysqli, $sql);
    $tot_amount = mysqli_fetch_assoc($tot)['tot'];

    // Get sales data by ProductCategory
    $sql = "SELECT 
                dp.ProductCategory AS name, 
                SUM(fs.SalesAmount) * 100 / $tot_amount AS y, 
                dp.ProductCategory AS drilldown
            FROM dimproduct dp
            JOIN factsales fs ON dp.ProductID = fs.ProductID
            GROUP BY dp.ProductCategory
            ORDER BY y DESC";
    $all_kat = mysqli_query($mysqli, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($all_kat)) {
        $data[] = [
            'name' => $row['name'],
            'y' => (float) $row['y'],
            'drilldown' => $row['drilldown']
        ];
    }
    $json_all_kat = json_encode($data);

    // Get drilldown data for subcategories
    $sql = "SELECT 
                dp.ProductCategory AS category, 
                dp.ProductSubCategory AS subcategory, 
                SUM(fs.SalesAmount) AS sales_amount
            FROM dimproduct dp
            JOIN factsales fs ON dp.ProductID = fs.ProductID
            GROUP BY dp.ProductCategory, dp.ProductSubCategory";
    $det_kat = mysqli_query($mysqli, $sql);
    $drilldown_data = [];
    $categories = [];
    while ($row = mysqli_fetch_assoc($det_kat)) {
        $categories[$row['category']][] = [
            $row['subcategory'],
            (float) $row['sales_amount']
        ];
    }

    foreach ($categories as $category => $data_points) {
        $drilldown_data[] = [
            'name' => $category,
            'id' => $category,
            'data' => $data_points
        ];
    }
    $string_data = json_encode($drilldown_data);

    // Get dashboard box data
    $sql2 = "SELECT count(distinct `ProductCategory`) as jml_cust from dimproduct";
    $jml_c = mysqli_query($mysqli, $sql2);
    $jml_cust = mysqli_fetch_assoc($jml_c);

    $sql3 = "SELECT sum(SalesAmount) as tot2 from factsales";
    $tot2 = mysqli_query($mysqli, $sql3);
    $tot_penj = mysqli_fetch_assoc($tot2);

    $sql4 = "SELECT count(CustomerName) as tot_jud_film from dimcustomer";
    $tot3 = mysqli_query($mysqli, $sql4);
    $tot_jud_film = mysqli_fetch_assoc($tot3);
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

                <div class="row justify-content-center mt-5 mb-2">
                    <!-- Small Box 1: Total Product Category -->
<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
    <div class="small-box" style="background-color: #17a2b8; color: white; border-radius: 10px; margin: 10px; padding: 15px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
        <div class="inner p-4">
            <h3 style="margin: 0; font-size: 1.8rem; font-weight: bold;"><?php echo $jml_cust['jml_cust']; ?></h3>
            <p style="margin: 0; font-size: 1rem; font-weight: 300;">Product Category</p>
        </div>
        <div class="icon" style="position: absolute; top: 10px; right: 10px; opacity: 0.2; font-size: 4rem;">
            <i class="fas fa-user-plus"></i>
        </div>
    </div>
</div>

<!-- Small Box 2: Total Sales -->
<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
    <div class="small-box" style="background-color: #28a745; color: white; border-radius: 10px; margin: 10px; padding: 15px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
        <div class="inner p-4">
            <h3 style="margin: 0; font-size: 1.8rem; font-weight: bold;"><?php echo number_format($tot_penj['tot2'], 2, ',', '.'); ?></h3>
            <p style="margin: 0; font-size: 1rem; font-weight: 300;">Sales Amount</p>
        </div>
        <div class="icon" style="position: absolute; top: 10px; right: 10px; opacity: 0.2; font-size: 4rem;">
            <i class="fas fa-chart-bar"></i>
        </div>
    </div>
</div>

<!-- Small Box 3: Total Customer Names -->
<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
    <div class="small-box" style="background-color: #ffc107; color: white; border-radius: 10px; margin: 10px; padding: 15px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
        <div class="inner p-4">
            <h3 style="margin: 0; font-size: 1.8rem; font-weight: bold;"><?php echo $tot_jud_film['tot_jud_film']; ?></h3>
            <p style="margin: 0; font-size: 1rem; font-weight: 300;">Customer Name</p>
        </div>
        <div class="icon" style="position: absolute; top: 10px; right: 10px; opacity: 0.2; font-size: 4rem;">
            <i class="fas fa-shopping-bag"></i>
        </div>
    </div>
</div>

                </div>

                <!-- Chart Container -->
                <div id="container" class="grafik"></div>

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

    <script type="text/javascript">
        Highcharts.chart('container', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Persentase Penjualan Berdasarkan Kategori Produk'
            },
            subtitle: {
                text: 'Klik untuk melihat detail penjualan subkategori'
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
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
            },
            series: [{
                name: "Penjualan Kategori",
                colorByPoint: true,
                data: <?php echo $json_all_kat; ?>
            }],
            drilldown: {
                series: <?php echo $string_data; ?>
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
