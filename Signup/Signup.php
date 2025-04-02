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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['FullName'] ?? '';
    $email = $_POST['Email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phoneNum'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        echo "All fields are required!";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // توليد ClientID (تقدر تخليه Auto_Increment لو عدلت الجدول مستقبلاً)
    $clientID = rand(1000, 9999);

    $sql = "INSERT INTO client (`ClientID`, `Name`, `Email`, `Password`, `Phone Number`) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $clientID, $name, $email, $hashedPassword, $phone);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../ClientHome/ClientHomePage.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$_SESSION["artist_id"] = $row["ArtistID"]; // بعد التحقق من المستخدم

?>


