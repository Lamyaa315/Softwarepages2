<?php
session_start();
$host = "localhost";
$user = "root";
$password = "root";
$database = "ruwaa";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['ArtistID'])) {
    header("Location: login/login.php");
    exit();
}

$artistID = $_SESSION['ArtistID'];

// Get all reservations for this makeup artist
$sql = "SELECT reservation.Date, reservation.Time, reservation.Status, reservation.Service, client.Name AS ClientName
        FROM reservation
        INNER JOIN client ON reservation.ClientID = client.ClientID
        WHERE reservation.ArtistID = $artistID
        ORDER BY reservation.Date DESC, reservation.Time DESC";

$result = mysqli_query($conn, $sql);
?>
﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="General.css">
    <link rel="stylesheet" href="artist_styles.css">
    <script src="FilterScript.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo2.jpg" alt="رواء Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="MAHomePage.html">Home</a></li>
                <li><a href="MAppointment.html">Reservations</a></li>
                <li><a href="modifyArtistProfile.html">Edit Profile</a></li>
                <li><a href="logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard">
        <div class="filters">
            <label for="sortStatus"><strong>Filter by Status:</strong></label>
            <select id="sortStatus" onchange="sortAppointmentsByStatus()">
                <option value="all">All Statuses</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="canceled">Canceled</option>
            </select>
        </div>




        <!---<select id="sortStatus" onchange="sortAppointmentsByStatus()">
            <option value="status">Sort by Status</option>
            <option value="all">All Statuses</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="canceled">Canceled</option>
        </select>-->
        <section class="appointments container">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                 <?php
                 if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['ClientName']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['Time']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['Service']) . "</td>";
                  echo "<td class='status " . strtolower($row['Status']) . "'>" . htmlspecialchars($row['Status']) . "</td>";
                  echo "</tr>";
                  }
                  } else {
                   echo "<tr><td colspan='5'>No appointments found.</td></tr>";
                   }
                   ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 رواء. All Rights Reserved.</p>
    </footer>
</body>
</html>
