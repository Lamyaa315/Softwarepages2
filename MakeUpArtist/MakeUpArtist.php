<?php
// عرض الأخطاء للتصحيح
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "root";
$database = "ruwaa";

$conn = new mysqli($servername, $username, $password, $database,8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_GET['ArtistID'])) {
    die("No artist selected.");
}
$artist_id = $_GET['ArtistID'];


// استعلام لجلب بيانات الفنانة
$stmt = $conn->prepare("SELECT * FROM `makeup atrist` WHERE ArtistID = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
$artist = $result->fetch_assoc();

if (!$artist) {
    die("Artist not found.");
}

$services_raw = $artist["Services"] ?? '';
$services = [];

if ($services_raw) {
    if ($services_raw[0] === '[') {
        // JSON array
        $services = json_decode($services_raw, true);
    } else {
        // نص عادي مفصول بفواصل
        $services = explode(',', $services_raw);
    }
}
$workImages = json_decode($artist["work"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makeup Artist</title>
    <link rel="stylesheet" href="General.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f8fa;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }
        .gallery img {
            width: 150px;
            height: 150px;
            margin: 5px;
            border-radius: 10px;
            border: 2px solid #BFA380;
        }
        .booking-button {
            background-color: #BFA380;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }
        .booking-form {
            display: none;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo2.jpg" alt="Roa Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="ClientHome/ClientHomePage.html">Home</a></li>
                <li><a href="tips/tips.html">Beauty Tips</a></li>
                <li><a href="CAppointment/CAppointment.php">Reservations</a></li>
                <li><a href="MakeupArtistList/MakeupArtistList.php">Makeup Artists</a></li>
                <li><a href="../logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container" id="profileContainer">
        <h1><?= htmlspecialchars($artist["Name"]) ?></h1>
        <p><?= htmlspecialchars($artist["Description"]) ?></p>

        <h2>My Work</h2>
        <div class="gallery">
            <?php foreach ($workImages as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="Work Image">
            <?php endforeach; ?>
        </div>

        <h2>Services</h2>
        <ul>
            <?php if (!empty($services)): ?>
    <?php foreach ($services as $service): ?>
        <li><?= htmlspecialchars($service) ?></li>
    <?php endforeach; ?>
<?php else: ?>
    <li>No services listed.</li>
<?php endif; ?>

        </ul>

        <button id="bookingButton" class="booking-button">Book Now</button>

        <h2>Contact Me</h2>
        <div class="contact">
            <a href="<?= htmlspecialchars($artist["PhoneNumber"]) ?>">WhatsApp</a>
            <a href="<?= htmlspecialchars($artist["InstagramAccount"]) ?>">Instagram</a>
        </div>
    </div>

    <div class="container booking-form" id="bookingForm">
        <h2>Book an Appointment</h2>
        <label for="bookingDate">Select Date:</label>
        <input type="date" id="bookingDate"><br>

        <label for="bookingTime">Select Time:</label>
        <input type="time" id="bookingTime"><br>

        <label class="Services">Choose Service: </label><br>
        <label class="Services">
            <input type="radio" name="Service" value="Evening"> Evening Makeup
        </label><br>
        <label class="Services">
            <input type="radio" name="Service" value="Bridal"> Bridal Makeup 
        </label><br>

        <button id="confirmBooking" class="booking-button">Confirm Booking</button>
    </div>

    <footer>
        <p>&copy; 2025 Roa. All Rights Reserved.</p>
    </footer>

    <script>
        document.getElementById("bookingButton").addEventListener("click", function() {
            document.getElementById("bookingForm").style.display = "block";
        });

        document.getElementById("confirmBooking").addEventListener("click", function() {
            const date = document.getElementById("bookingDate").value;
            const time = document.getElementById("bookingTime").value;
            const service = document.querySelector('input[name="service"]:checked');

            if (!date || !time || !service) {
                alert("Please fill in all fields and select a service.");
                return;
            }

            alert(`Your booking is confirmed for ${date} at ${time} for ${service.value} makeup!`);
            document.getElementById("bookingForm").style.display = "none";
        });
    </script>
</body>
</html>
