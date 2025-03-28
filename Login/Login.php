<?php
$servername = "localhost";
$username = "root"; // Default for MAMP
$password = "root"; // Default for MAMP
$database = "ruwaa";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
