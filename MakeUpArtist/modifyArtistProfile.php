<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost","root" , "root", "ruwaa" , 8889);

// لتظهر الأخطاء في الصفحة


$conn = mysqli_connect("localhost", "root", "root", "ruwaa", 8889);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION["artist_id"])) {
    die("Unauthorized access");
}

$artist_id = $_SESSION["artist_id"];
$message = "";

// إذا المستخدم ضغط حفظ
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["Name"] ?? "";
    $description = $_POST["Description"] ?? "";
    $whatsapp = $_POST["PhoneNumber"] ?? "";
    $instagram = $_POST["InstagramAccount"] ?? "";
    $price = $_POST["price"] ?? null;
    $services = isset($_POST["Services"]) ? json_encode($_POST["Services"]) : json_encode([]);
    $workImages = isset($_POST["work"]) ? json_encode($_POST["work"]) : json_encode([]);

    // معالجة صورة البروفايل
    $profileImagePath = $artist['Profile'] ?? null; // احتفظ بالصورة القديمة إذا ما تم الرفع
    if (isset($_FILES['Profile']) && $_FILES['Profile']['error'] === UPLOAD_ERR_OK) {
        $profileTmp = $_FILES['Profile']['tmp_name'];
        $profileName = basename($_FILES['Profile']['name']);
        $uploadPath = 'images/' . $profileName;

        // نقل الصورة إلى مجلد images
        if (move_uploaded_file($profileTmp, $uploadPath)) {
            $profileImagePath = $uploadPath;
        }
    }

    $sql = "UPDATE `makeup atrist` 
            SET Name = ?, Description = ?, PhoneNumber = ?, InstagramAccount = ?, Services = ?, work = ?, Profile = ?, price = ?
            WHERE ArtistID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $name, $description, $whatsapp, $instagram, $services, $workImages, $profileImagePath, $price, $artist_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully";
    } else {
        $message = "Database error: " . $stmt->error;
    }
}

// جلب البيانات من قاعدة البيانات
$stmt = $conn->prepare("SELECT * FROM `makeup atrist` WHERE ArtistID = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
$artist = $result->fetch_assoc();

if (!$artist) {
    die("Artist not found.");
}

$services = !empty($artist["Services"]) ? json_decode($artist["Services"], true) : [];
$workImages = !empty($artist["work"]) ? json_decode($artist["work"], true) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { background: white; max-width: 700px; margin: auto; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        input[type="file"] { margin-top: 5px; }
        .checkbox { margin-right: 10px; }
        button { margin-top: 20px; padding: 10px 20px; background: #bfa380; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .success { color: green; }
    </style>
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
                <li><a href="MAHome/MAHomePage.php">Home</a></li>
                <li><a href="../MAppointment/MAppointment.php">Reservations</a></li>
                <li><a href="../MakeUpArtist/modifyArtistProfile.php">Edit Profile</a></li>
                <li><a href="../logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>
<div class="container">
    <h2>Edit Your Profile</h2>

    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="Name" value="<?= htmlspecialchars($artist['Name'] ?? '') ?>">

        <label>Description:</label>
        <textarea name="Description"><?= htmlspecialchars($artist['Description'] ?? '') ?></textarea>

        <label>WhatsApp:</label>
        <input type="text" name="PhoneNumber" value="<?= htmlspecialchars($artist['PhoneNumber'] ?? '') ?>">

        <label>Instagram:</label>
        <input type="text" name="InstagramAccount" value="<?= htmlspecialchars($artist['InstagramAccount'] ?? '') ?>">

        <label>Services:</label>
        <label><input type="checkbox" class="checkbox" name="Services[]" value="Bridal Makeup" <?= in_array("Bridal Makeup", $services) ? "checked" : "" ?>> Bridal Makeup</label>
        <label><input type="checkbox" class="checkbox" name="Services[]" value="Evening Makeup" <?= in_array("Evening Makeup", $services) ? "checked" : "" ?>> Evening Makeup</label>

        <label>Work Images (URLs):</label>
        <?php foreach ($workImages as $img): ?>
            <input type="text" name="work[]" value="<?= htmlspecialchars($img ?? '') ?>"><br>
        <?php endforeach; ?>
        <input type="text" name="work[]" placeholder="Add new image"><br>

        <label>Profile Picture:</label>
        <input type="file" name="Profile"><br>

        <label>Price:</label>
        <input type="number" name="price" value="<?= htmlspecialchars($artist['price'] ?? '') ?>"><br>

        <button type="submit">Save</button>
    </form>
</div>
    <footer>
        <p>&copy; 2025 رواء. All Rights Reserved.</p>
    </footer>
</body>
</html>
