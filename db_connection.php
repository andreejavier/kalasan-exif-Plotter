<?php
// db_connection.php

$servername = "localhost";
$username = "root";
$password = ""; // or your database password
$dbname = "landong_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
