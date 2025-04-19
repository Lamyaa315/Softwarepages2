<?php
// عرض الأخطاء للتصحيح
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "root";
$database = "ruwaa";

$conn = new mysqli($servername, $username, $password, $database, 8889);
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
        $services = json_decode($services_raw, true);
    } else {
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
    <style>
      
         .container {
            background: white;
            width: 100%;
            max-width: 900px;
            margin: 40px auto;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
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
        body{
    height:100%;
    margin:0;
    padding:0;
    display:flex;
    flex-direction:column;
}
main{
    flex:1;
}
header, footer {
    background-color: white;
    width: 100%;
    padding: 10px 20px;
}

header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    border-bottom-right-radius: 15px;
    border-bottom-left-radius: 15px;
}

.logo img {
    width: 350px;
}

.navigation {
    background-color: #94A6A2;
    border-top-left-radius: 30px;
    padding: 50px;
}

nav ul {
    display: flex;
    list-style: none;
}

    nav ul li {
        margin: 0 10px;
    }

        nav ul li a {
            text-decoration: none;
            color: #95775e;
            font-size: 22px;
            padding: 10px 15px;
            background-color: #94A6A2;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

            nav ul li a:hover {
                background-color: #95775e;
                color: white;
            }

.signout {
    background-color: #95775e;
    color: white;
    padding: 10px 20px;
    border-radius: 20px;
    font-size: 16px;
}


footer {
    text-align: center;
    padding: 20px;
    bottom: 0;
    left:0;
    padding:10px 0;
    width: 100%;
   
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}

    footer p {
        background-color: #94A6A2;
        border-radius: 15px;
        padding: 10px;
        color: white;
    }


    </style>
</head>
<body>
     <header>
        <div class="logo">
            <img src="../images/logo2.jpg" alt="رواء Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="../ClientHome/ClientHomePage.html">Home</a></li>
                <li><a href="../tips/tips.html">Beauty Tips</a></li>
                <li><a href="../CAppointment/CAppointment.php">Reservations</a></li>
                <li><a href="../MakeupArtistList/MakeupArtistList.php">Makeup Artists</a></li>
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
                <img src="<?='../'.htmlspecialchars($img) ?>" alt="Work Image">
            <?php endforeach; ?>
        </div>

        <h2>Services</h2>
        
            <p><?= htmlspecialchars($artist["Services"] ?? 'No services listed.') ?></p>
        
        <h2>Price: <?= htmlspecialchars($artist["price"]) ?> SR</h2>

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

        <input type="hidden" id="service" value="<?= htmlspecialchars($artist['Services']) ?>">

        <button id="confirmBooking" class="booking-button">Confirm Booking</button>
    </div>

    <footer>
        <p>&copy; 2025 رواء. All Rights Reserved.</p>
    </footer>

    <script>
        document.getElementById("bookingButton").addEventListener("click", function() {
            document.getElementById("bookingForm").style.display = "block";
        });

        document.getElementById("confirmBooking").addEventListener("click", function () {
            const date = document.getElementById("bookingDate").value;
            const time = document.getElementById("bookingTime").value;
            const service = document.getElementById("service").value;
            const artistId = <?= json_encode($artist_id) ?>;

console.log({ date, time, service, artistId });
            if (!date || !time) {
                alert("Please fill in all fields.");
                return;
            }

            fetch("submit_reservation.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    date: date,
                    time: time,
                    service: service,
                    artist_id: artistId
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Reservation confirmed!");
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            });
        });
    </script>
</body>
</html>
