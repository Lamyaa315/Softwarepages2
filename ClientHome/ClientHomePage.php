<?php
session_start();
include '../config.php';

if (!isset($_SESSION['ClientID'])) {
    header("Location: ../Login/Login.php"); 
    exit();
}

$clientID = $_SESSION['ClientID'];

// Get upcoming reservation
$sql = "SELECT art.Name, res.Date, res.Time, res.Status
        FROM reservation res
        JOIN `makeup atrist` art ON res.ArtistID = art.ArtistID
        WHERE res.ClientID = $clientID
            AND res.Date >= CURDATE()
        ORDER BY res.Date ASC, res.Time ASC
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$reservation = mysqli_fetch_assoc($result);
?>
