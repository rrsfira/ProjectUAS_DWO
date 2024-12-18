<?php
$dbHost = "localhost";
$dbDatabase = "projectuas_";
$dbUser = "root";
$dbPassword = "";

$mysqli = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbDatabase);
// mengecek koneksi
if (!$mysqli) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
//echo "Koneksi berhasil";

//mysqli_close($conn);

?>