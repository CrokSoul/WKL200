<?php
$host = "localhost";
$username = "WKL200";
$password = "8uekc55p2vlvPN";
$database = "WKL200";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>