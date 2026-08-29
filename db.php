<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "ecommerce_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>