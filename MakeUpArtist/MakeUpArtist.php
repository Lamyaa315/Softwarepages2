<?php
// عرض الأخطاء للتصحيح

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// الاتصال بقاعدة البيانات
$artist_id = isset($_GET["ArtistID"]) ? intval($_GET["ArtistID"]) : 0;
$servername = "localhost";
$username = "root";
$password = "root";
$database = "ruwaa";

$conn = new mysqli($servername, $username, $password, $database,8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//مدري اذا هذا$artist_id = $_SESSION["artist_id"];

// استعلام لجلب بيانات الفنانة
$stmt = $conn->prepare("SELECT * FROM `makeup artist profile` WHERE id = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
$artist = $result->fetch_assoc();

if (!$artist) {
    die("Artist not found.");
}

$services = json_decode($artist["services"]);
$workImages = json_decode($artist["work_images"]);
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
                <li><a href="ClientHomePage.html">Home</a></li>
                <li><a href="tips.html">Beauty Tips</a></li>
                <li><a href="CAppointment.html">Reservations</a></li>
                <li><a href="MakeupArtistList.html">Makeup Artists</a></li>
                <li><a href="logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container" id="profileContainer">
        <h1><?= htmlspecialchars($artist["name"]) ?></h1>
        <p><?= htmlspecialchars($artist["description"]) ?></p>

        <h2>My Work</h2>
        <div class="gallery">
            <?php foreach ($workImages as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="Work Image">
            <?php endforeach; ?>
        </div>

        <h2>Services</h2>
        <ul>
            <?php foreach ($services as $service): ?>
                <li><?= htmlspecialchars($service) ?></li>
            <?php endforeach; ?>
        </ul>

        <button id="bookingButton" class="booking-button">Book Now</button>

        <h2>Contact Me</h2>
        <div class="contact">
            <a href="<?= htmlspecialchars($artist["whatsapp"]) ?>">WhatsApp</a>
            <a href="<?= htmlspecialchars($artist["instagram"]) ?>">Instagram</a>
        </div>
    </div>

    <div class="container booking-form" id="bookingForm">
        <h2>Book an Appointment</h2>
        <label for="bookingDate">Select Date:</label>
        <input type="date" id="bookingDate"><br>

        <label for="bookingTime">Select Time:</label>
        <input type="time" id="bookingTime"><br>

        <label class="services">Choose Service: </label><br>
        <label class="services">
            <input type="radio" name="service" value="Evening"> Evening Makeup
        </label><br>
        <label class="services">
            <input type="radio" name="service" value="Bridal"> Bridal Makeup 
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
