<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "car_rental"; 

$connect = new mysqli($servername, $username, $password, $dbname);

if ($connect->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>