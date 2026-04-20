<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "project";

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
