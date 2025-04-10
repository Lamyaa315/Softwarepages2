<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "root";
$database = "ruwaa";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION['ArtistID'])) {
    header("Location: ../Login/Login.php");
    exit();
}

$artistID = $_SESSION['ArtistID'];

$sql = "SELECT client.Name AS ClientName, reservation.Date, reservation.Time, reservation.Status
        FROM reservation
        JOIN client ON reservation.ClientID = client.ClientID
        WHERE reservation.ArtistID = ?
        AND reservation.Date >= CURDATE()
        ORDER BY reservation.Date ASC, reservation.Time ASC
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $artistID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$reservation = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MA Home</title>
    <link rel="stylesheet" href="../General.css">
    <link rel="stylesheet" href="MAHomePage.css">
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

    <section class="reservations">
        <h2>Your Upcoming Reservations</h2>
        <div class="reservation">
            <?php if ($reservation): ?>
                <ul>
                    <li><strong>Client:</strong> <?= htmlspecialchars($reservation['ClientName']) ?></li>
                    <li><strong>Date:</strong> <?= htmlspecialchars($reservation['Date']) ?></li>
                    <li><strong>Time:</strong> <?= htmlspecialchars($reservation['Time']) ?></li>
                    <li><strong>Status:</strong> <span class="confirmed"><?= htmlspecialchars($reservation['Status']) ?></span></li>
                </ul>
            <?php else: ?>
                <p>No upcoming reservations found.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="about-platform">
        <h2>About رواء</h2>
        <p>رواء is an innovative platform that connects beauty professionals with customers seeking high-quality makeup and hairstyling services. Our goal is to provide a seamless booking experience, ensuring you can find and book the perfect beauty expert for any occasion.</p>

        <div class="about-images">
            <div class="about-card">
                <img src="../images/About.jpg" alt="Easy Booking">
                <h3>Easy Booking</h3>
                <p>Schedule your appointments effortlessly with our user-friendly platform.</p>
            </div>
            <div class="about-card">
                <img src="../images/About2.jpg" alt="Professional Beauty Services">
                <h3>Professional Services</h3>
                <p>Find top-rated beauty professionals for weddings, parties, and everyday looks.</p>
            </div>
            <div class="about-card">
                <img src="../images/About4.jpg" alt="User-Friendly Experience">
                <h3>Convenient Experience</h3>
                <p>Enjoy a smooth and stress-free beauty service tailored to your needs.</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 رواء. All Rights Reserved.</p>
    </footer>
</body>
</html>
