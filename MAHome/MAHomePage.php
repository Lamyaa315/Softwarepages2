<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Makeup Artist Home</title>
    <link rel="stylesheet" href="../General.css">
    <link rel="stylesheet" href="MAHomePage.css">
    <script src="../FilterScript.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../images/logo2.jpg" alt="رواء Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="MAHomePage.php">Home</a></li>
                <li><a href="../MAppointment/MAppointment.php">Reservations</a></li>
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
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <section class="appointments container">
            <h2>Your Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    session_start();
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
                    if (!isset($_SESSION['ArtistID'])) {
                        header("Location: ../Login/Login.php");
                        exit();
                    }

                    $artistID = $_SESSION['ArtistID'];
                    $sql = "SELECT reservation.ReservationID, reservation.Date, reservation.Time, reservation.Status, reservation.Service, client.Name AS ClientName
                            FROM reservation
                            JOIN client ON reservation.ClientID = client.ClientID
                            WHERE reservation.ArtistID = $artistID
                            ORDER BY reservation.Date DESC, reservation.Time DESC";

                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status = strtolower($row['Status']);
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['ClientName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Service']) . "</td>";
                            echo "<td class='status $status'>" . htmlspecialchars($row['Status']) . "</td>";

                            if ($status === "pending") {
                                echo "<td><form method='POST' action='../MAppointment/update_status.php'>";
                                echo "<input type='hidden' name='reservation_id' value='" . $row['ReservationID'] . "'>";
                                echo "<button type='submit' name='action' value='confirm' class='confirm-btn'>Confirm</button>";
                                echo "<button type='submit' name='action' value='cancel' class='cancel-btn'>Cancel</button>";
                                echo "</form></td>";
                            } else {
                                echo "<td>-</td>";
                            }

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No appointments found.</td></tr>";
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
