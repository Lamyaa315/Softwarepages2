<?php


session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost","root" , "root", "ruwaa" , "8889");

// لتظهر الأخطاء في الصفحة


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// تحقق من وجود artist_id في الجلسة
if (!isset($_SESSION["artist_id"])) {
    http_response_code(403);
    echo "Unauthorized access";
    exit();
}

$artist_id = $_SESSION["artist_id"];

// استلام البيانات من الطلب
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo "Invalid input";
    exit();
}

// تجميع البيانات
$name = $data["name"];
$description = $data["description"];
$whatsapp = $data["whatsapp"];
$instagram = $data["instagram"];
$services = json_encode($data["services"]);
$workImages = json_encode($data["workImages"]);

// تحديث البيانات في جدول البروفايل
$sql = "UPDATE `makeup artist profile` 
        SET name = ?, description = ?, whatsapp = ?, instagram = ?, services = ?, work_images = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $name, $description, $whatsapp, $instagram, $services, $workImages, $artist_id);

if ($stmt->execute()) {
    echo "Profile updated successfully";
} else {
    http_response_code(500);
    echo "Database error: " . $stmt->error;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Makeup Artist</title>
    <link rel="stylesheet" href="General.css">
    <script defer src="artists.js"></script>
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
            position: relative;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
            flex-grow: 1;
        }
        .gallery img {
            max-width: 100px;
            margin: 5px;
            border-radius: 5px;
        }
        .checkbox-group {
            text-align: center;
            margin: 10px auto;
        }
    </style>
</head>
<body>
    <!-- Header -->
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

    <!-- Main Content -->
    <div class="container" id="profileContainer">
        <h1>Edit Your Profile</h1>

        <label for="name">Name:</label>
        <input type="text" id="name">

        <label for="description">Description:</label>
        <textarea id="description"></textarea>

        <h2>My Work</h2>
        <div class="gallery" id="galleryContainer"></div>
        <input type="text" id="newWorkImage" placeholder="Add image URL">
        <button onclick="addWorkImage()">Add Image</button>

        <h2>Services</h2>
        <div class="checkbox-group">
           <label><input type="checkbox" id="eveningMakeup" value="Evening Makeup"> Evening Makeup</label>
            <label><input type="checkbox" id="bridalMakeup" value="Bridal Makeup">  Bridal Makeup</label>
        </div>

        <h2>Contact</h2>
        <label for="whatsapp">WhatsApp:</label>
        <input type="text" id="whatsapp">

        <label for="instagram">Instagram:</label>
        <input type="text" id="instagram"><br>

        <button class="save-button" onclick="saveProfile()">Save Changes</button>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 رواء. All Rights Reserved.</p>
    </footer>


</body>
</html>
