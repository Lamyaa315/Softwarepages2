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
$existingWorkImages = !empty($artist["work"]) ? json_decode($artist["work"], true) : [];
$existingWorkImages = array_filter($existingWorkImages, fn($val) => trim($val) !== "");

// حذف صورة من الأعمال باستخدام GET
if (isset($_GET['delete'])) {
    $deleteImage = $_GET['delete'];

    if (file_exists("../$deleteImage")) {
        unlink("../$deleteImage");
    }

    $existingWorkImages = array_filter($existingWorkImages, function($img) use ($deleteImage) {
        return $img !== $deleteImage;
    });

    $encodedWorkImages = json_encode(array_values($existingWorkImages));

    $stmt = $conn->prepare("UPDATE `makeup atrist` SET work = ? WHERE ArtistID = ?");
    $stmt->bind_param("si", $encodedWorkImages, $artist_id);
    $stmt->execute();

    header("Location: modifyArtistProfile.php");
    exit();
}

// حذف صورة البروفايل باستخدام GET
if (isset($_GET['delete_profile']) && $_GET['delete_profile'] == '1') {
    $profilePath = $artist['Profile'] ?? '';
    if ($profilePath && file_exists("../$profilePath")) {
        unlink("../$profilePath");
    }

    $stmt = $conn->prepare("UPDATE `makeup atrist` SET Profile = NULL WHERE ArtistID = ?");
    $stmt->bind_param("i", $artist_id);
    $stmt->execute();

    header("Location: modifyArtistProfile.php");
    exit();
}

// إذا المستخدم ضغط حفظ
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name        = $_POST["Name"] ?? "";
    $description = $_POST["Description"] ?? "";
    $whatsapp    = $_POST["PhoneNumber"] ?? "";
    $instagram   = $_POST["InstagramAccount"] ?? "";
    $price       = $_POST["price"] ?? null;
    $service     = $_POST["Services"] ?? null;

    $uploadedWorkImages = [];
    if (!empty($_FILES['workImages']['name'][0])) {
        foreach ($_FILES['workImages']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['workImages']['error'][$key] === UPLOAD_ERR_OK) {
                $fileTmp = $tmp_name;

                $originalName = basename($_FILES['workImages']['name'][$key]);
                $fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $originalName);

                $destination = '../images/' . $fileName;

                if (move_uploaded_file($fileTmp, $destination)) {
                    $uploadedWorkImages[] = 'images/' . $fileName;
                }
            }
        }
    }

    $allWorkImages = array_merge($existingWorkImages, $uploadedWorkImages);
    $encodedWorkImages = json_encode($allWorkImages);

    $profileImagePath = $artist['Profile'] ?? null;
    if (isset($_FILES['Profile']) && $_FILES['Profile']['error'] === UPLOAD_ERR_OK) {
        $profileTmp = $_FILES['Profile']['tmp_name'];
        $originalProfileName = basename($_FILES['Profile']['name']);
        $cleanProfileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $originalProfileName);
        $uploadPath = '../images/' . $cleanProfileName;

        if (move_uploaded_file($profileTmp, $uploadPath)) {
            $profileImagePath = 'images/' . $cleanProfileName;
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
    <link rel="stylesheet" href="../General.css">
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
        input[type="text"], input[type="number"], textarea {
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
            background-color: #4CAF50;
            color: white;
            padding: 20px 35px;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0 8px 16px rgba(0,0,0,0.25);
            opacity: 0;
            animation: fadeInOut 3s ease forwards;
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-10px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-10px); }
        }
        .img-box {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        .img-box img {
            width: 120px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .img-box a {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            padding: 2px 7px;
            border-radius: 50%;
            text-decoration: none;
            font-weight: bold;
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

            <label>Service:</label><br>
            <label><input type="radio" name="Services" value="Bridal Makeup" <?= ($selectedService === "Bridal Makeup") ? "checked" : "" ?>> Bridal Makeup</label>
            <label><input type="radio" name="Services" value="Evening Makeup" <?= ($selectedService === "Evening Makeup") ? "checked" : "" ?>> Evening Makeup</label><br><br>

            <label>Upload New Work Images:</label>
            <input type="file" name="workImages[]" multiple><br><br>

            <?php if (!empty($existingWorkImages)): ?>
                <div class="preview-imgs">
                    <?php foreach ($existingWorkImages as $img): ?>
                        <div class="img-box">
                            <img src="../<?= htmlspecialchars($img) ?>" alt="Work Image">
                            <a href="?delete=<?= urlencode($img) ?>" onclick="return confirm('Are you sure you want to delete this image?')">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div><br>
            <?php endif; ?>

            <label>Profile Picture:</label>
            <input type="file" name="Profile"><br><br>

            <?php if (!empty($artist['Profile'])): ?>
                <div class="img-box">
                    <img src="../<?= htmlspecialchars($artist['Profile']) ?>" alt="Profile Image">
                    <a href="?delete_profile=1" onclick="return confirm('Are you sure you want to delete the profile picture?')">&times;</a>
                </div><br><br>
            <?php endif; ?>

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
