<?php 
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "root", "root", "ruwaa", 8889);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION["artist_id"])) {
    die("Unauthorized access");
}

$artist_id = $_SESSION["artist_id"];
$message = "";

// جلب بيانات الفنانة
$stmt = $conn->prepare("SELECT * FROM `makeup atrist` WHERE ArtistID = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
$artist = $result->fetch_assoc();

if (!$artist) {
    die("Artist not found.");
}

$selectedService = $artist["Services"] ?? null;

// ✅ فلترة الصور قبل العرض
$workImages = !empty($artist["work"]) ? json_decode($artist["work"], true) : [];
$workImages = array_filter($workImages, fn($val) => trim($val) !== "");

// إذا المستخدم ضغط حفظ
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name        = $_POST["Name"] ?? "";
    $description = $_POST["Description"] ?? "";
    $whatsapp    = $_POST["PhoneNumber"] ?? "";
    $instagram   = $_POST["InstagramAccount"] ?? "";
    $price       = $_POST["price"] ?? null;
    $service     = $_POST["Services"] ?? null;

    $workInput = $_POST["work"] ?? [];
    $workInput = array_filter($workInput, fn($val) => trim($val) !== "");
    $workImages = array_values($workInput);
    $encodedWorkImages = json_encode($workImages);

    $profileImagePath = $artist['Profile'] ?? null;
    if (isset($_FILES['Profile']) && $_FILES['Profile']['error'] === UPLOAD_ERR_OK) {
        $profileTmp = $_FILES['Profile']['tmp_name'];
        $profileName = basename($_FILES['Profile']['name']);
        $uploadPath = 'images/' . $profileName;

        if (move_uploaded_file($profileTmp, $uploadPath)) {
            $profileImagePath = $uploadPath;
        }
    }

    $sql = "UPDATE `makeup atrist` 
            SET Name = ?, Description = ?, PhoneNumber = ?, InstagramAccount = ?, Services = ?, work = ?, Profile = ?, price = ?
            WHERE ArtistID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $name, $description, $whatsapp, $instagram, $service, $encodedWorkImages, $profileImagePath, $price, $artist_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully";
    } else {
        $message = "Database error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
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
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            background-color: #bfa380;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background-color: #a98a6a;
            transform: scale(1.05);
        }

        #popup-message {
            position: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 20px 35px;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0 8px 16px rgba(0,0,0,0.25);
            opacity: 0;
            animation: fadeInOut 3s ease forwards;
            z-index: 9999;
            text-align: center;
            font-size: 18px;
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-10px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-10px); }
        }
    </style>
    <link rel="stylesheet" href="../General.css">
</head>
<body>
     <header>
        <div class="logo">
            <img src="../images/logo2.jpg" alt="رواء Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="../MAHome/MAHomePage.php">Home</a></li>
                <li><a href="../MAppointment/MAppointment.php">Reservations</a></li>
                <li><a href="../MakeUpArtist/modifyArtistProfile.php">Edit Profile</a></li>
                <li><a href="../logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>
<div class="container">
    <h2>Edit Your Profile</h2>

    <?php if ($message): ?>
        <div id="popup-message"><?= htmlspecialchars($message) ?></div>
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

        <label>Service:</label>
        <label><input type="radio" name="Services" value="Bridal Makeup" <?= ($selectedService === "Bridal Makeup") ? "checked" : "" ?>> Bridal Makeup</label>
        <label><input type="radio" name="Services" value="Evening Makeup" <?= ($selectedService === "Evening Makeup") ? "checked" : "" ?>> Evening Makeup</label><br><br>

        <label>Work Images (URLs):</label>
        <div id="work-images">
            <?php foreach ($workImages as $img): ?>
                <input type="text" name="work[]" value="<?= htmlspecialchars($img ?? '') ?>">
            <?php endforeach; ?>
        </div>
        <button type="button" onclick="addImageInput()" style="margin-top: 10px; background-color: #eee; color: #333; border: 1px solid #ccc;">+ Add another image</button><br><br>

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

<script>
    function addImageInput() {
        const container = document.getElementById("work-images");
        const input = document.createElement("input");
        input.type = "text";
        input.name = "work[]";
        input.placeholder = "Image URL";
        input.style.marginTop = "10px";
        input.style.display = "block";
        input.style.width = "100%";
        input.style.padding = "8px";
        input.style.borderRadius = "6px";
        input.style.border = "1px solid #ccc";
        container.appendChild(input);
    }
</script>
</body>
</html>
