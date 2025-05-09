<?php
session_start();
include("../config.php"); 

if (!isset($_SESSION['artist_id'])) {
  header("Location: Login/Login.php");
  exit();
}

$artistID = $_SESSION['artist_id'];
//$artistID = 3;

// Get all reservations for this makeup artist
$sql = "SELECT reservation.ReservationID, reservation.Date, reservation.Time, reservation.Status, reservation.Service, client.Name AS ClientName
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
    <link rel="stylesheet" href="../General.css">
    <link rel="stylesheet" href="artist_styles.css">
    <script src="../FilterScript.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../images/logo2.jpg" alt="رواء Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="../MAHome/MAHomePage.php">Home</a></li>
                <li><a href="MAppointment.php">Reservations</a></li>
                <li><a href="../MakeUpArtist/modifyArtistProfile.php">Edit Profile</a></li>
                <li><a href="../logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard">
        <div class="filters">
            <label for="sortStatus"><strong>Filter by Status:</strong></label>
            <select id="sortStatus" onchange="sortAppointmentsByStatus()">
                <option value="all">All Statuses</option>
                <option value="confirmed">Confirmed</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

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
        $status = strtolower($row['Status']); 
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ClientName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Service']) . "</td>";
        echo "<td class='status $status'>";

        if ($status === "pending") {
            echo "<form method='POST' action='update_status.php' style='display:flex; gap:5px;'>";
            echo "<input type='hidden' name='reservation_id' value='" . $row['ReservationID'] . "'>";
            echo "<button type='submit' name='action' value='confirm' class='confirm-btn'>Confirm</button>";
            echo "<button type='submit' name='action' value='cancel' class='cancel-btn'>Cancel</button>";
            echo "</form>";
        } else {
            echo htmlspecialchars($row['Status']);
        }

        echo "</td>";
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
