<?php

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "ecommerce_db";

//اتصال mysqli
$conn = mysqli_connect($servername, $username, $password, $dbname);

//فحص الاتصال نجح
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>