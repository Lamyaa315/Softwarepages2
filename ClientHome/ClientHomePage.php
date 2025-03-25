<?php
session_start();
include 'config.php';

if (!isset($_SESSION['ClientID'])) {
    header("Location: Login/Login.php");
    exit();
}

$clientID = $_SESSION['ClientID']
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="General.css">
    <link rel="stylesheet" href="ClientHomePage.css">
</head>

<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="logo2.jpg" alt="رواء Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="ClientHomePage.php">Home</a></li>
                <li><a href="tips.html">Beauty Tips</a></li>
                <li><a href="CAppointment.php">Reservations</a></li>
                <li><a href="MakeupArtistList.php">Makeup Artists</a></li>
                <li><a href="logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>

    
    <?php 
    
    $sql = "SELECT art.Name, res.Date, res.Time, res.Status
        FROM reservation res
        JOIN makeup atrist art ON res.ArtistID = art.ArtistID
        WHERE res.ClientID = $clientID
            AND res.Date >= CURDATE()
            ORDER BY res.Date ASC, res.Time ASC
            LIMIT 1";
    
    $result = mysqli_query($conn , $sql);
    
    $reservation = mysqli_fetch_assoc($result);
    
    ?>
    
    <!-- Upcoming Reservations Section -->
    <section class="reservations">

        <h2>Your Upcoming Reservations</h2>
        <div class="reservation">
            <?php if($reservation){ ?>
            <ul>
                <li><strong>Artist:</strong> <?php echo $reservation['Name']; ?></li>
                <li><strong>Date:</strong> <?php echo $reservation['Date']; ?></li>
                <li><strong>Time:</strong> <?php echo $reservation['Time']; ?></li>
                <li><strong>Status:</strong> <span class="confirmed"><?php echo $reservation['Status']; ?></span></li>
            </ul>
            <?php } 
             else{
                 echo "No Upcoming Reservations"; 
             }?>
        </div>
    </section>

    <section class="about-platform">
        <h2>About رواء</h2>
        <p>رواء is an innovative platform that connects beauty professionals with customers seeking high-quality makeup and hairstyling services. Our goal is to provide a seamless booking experience, ensuring you can find and book the perfect beauty expert for any occasion.</p>

        <div class="about-images">
            <div class="about-card">
                <img src="images\About.jpg" alt="Easy Booking">
                <h3>Easy Booking</h3>
                <p>Schedule your appointments effortlessly with our user-friendly platform.</p>
            </div>
            <div class="about-card">
                <img src="images\About2.jpg" alt="Professional Beauty Services">
                <h3>Professional Services</h3>
                <p>Find top-rated beauty professionals for weddings, parties, and everyday looks.</p>
            </div>
            <div class="about-card">
                <img src="images\About4.jpg" alt="User-Friendly Experience">
                <h3>Convenient Experience</h3>
                <p>Enjoy a smooth and stress-free beauty service tailored to your needs.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 رواء. All Rights Reserved.</p>
    </footer>
</body>
</html>































