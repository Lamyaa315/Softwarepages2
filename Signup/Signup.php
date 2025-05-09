<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$database = "ruwaa";

$conn = mysqli_connect($servername, $username, $password, $database , 8889);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$signupError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['FullName'] ?? '';
    $email    = $_POST['Email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone    = $_POST['phoneNum'] ?? '';
    $role     = $_POST['role'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($role)) {
        $signupError = "All fields are required!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($role === "Client") {
            $clientID = rand(1000, 9999);  
            $sql = "INSERT INTO client (`ClientID`, `Name`, `Email`, `Password`, `Phone Number`) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issss", $clientID, $name, $email, $hashedPassword, $phone);
        } elseif ($role === "MakeupArtist") {
            $artistID = rand(1000, 9999);  
            $sql = "INSERT INTO `makeup atrist` (`ArtistID`, `Name`, `Email`, `Password`, `PhoneNumber`) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issss", $artistID, $name, $email, $hashedPassword, $phone);
        } else {
            $signupError = "Invalid role selected.";
            $stmt = null;
        }

        if ($stmt) {
            if (mysqli_stmt_execute($stmt)) {
                // ✅ SET session variables for login persistence
                if ($role === "MakeupArtist") {
                    $_SESSION['ArtistID'] = $artistID;
                    header("Location: ../MAHome/MAHomePage.php");
                } else {
                    $_SESSION['ClientID'] = $clientID;
                    header("Location: ../ClientHome/ClientHomePage.php");
                }
                exit;
            } else {
                $signupError = "Error during registration. Email may already be used.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="Signup.css">
</head>
<body>

    <!-- Logo at the top -->
    <div class="logo-container">
        <img src="../images/logo2-removebg-preview.png" alt="Logo">
    </div>

    <!-- Form Container -->
    <form id="UserForm" action="Signup.php" method="POST">
        <div class="form-group">
            <label for="FullName">Name:</label>
            <input type="text" id="FullName" name="FullName" placeholder="Your Name.." required>
        </div>
        <div class="form-group">
            <label for="Email">Email:</label>
            <input type="email" id="Email" name="Email" placeholder="Your Email.." required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Your password.." required>
        </div>
        <div class="form-group">
            <label for="phoneNum">Phone Number:</label>
            <input type="tel" id="phoneNum" name="phoneNum" placeholder="Your Phone Number.." required>
        </div>

        <div class="rolesholder">
            <label class="roles">Your role:</label>
            <label class="roles"><input type="radio" name="role" value="Client" checked> Client</label>
            <label class="roles"><input type="radio" name="role" value="MakeupArtist"> Makeup Artist</label>
        </div>

        <button type="submit" class="signup">Sign-up</button>

        <?php if (!empty($signupError)): ?>
            <p style="color: red; text-align: center; margin-top: 10px;"><?php echo htmlspecialchars($signupError); ?></p>
        <?php endif; ?>
    </form>

    <script>
        document.getElementById("UserForm").addEventListener("submit", function(e) {
            const name = document.getElementById("FullName").value.trim();
            const email = document.getElementById("Email").value.trim();
            const password = document.getElementById("password").value.trim();
            const phone = document.getElementById("phoneNum").value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!name || !email || !password || !phone) {
                alert("All fields are required!");
                e.preventDefault();
                return;
            }

            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address!");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
