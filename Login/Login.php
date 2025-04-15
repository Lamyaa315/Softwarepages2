<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$database = "ruwaa";

$conn = mysqli_connect($servername, $username, $password, $database , 8889);
$loginError = "";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($role === "Client") {
        $stmt = mysqli_prepare($conn, "SELECT * FROM client WHERE Email = ? LIMIT 1");
    } elseif ($role === "MakeupArtist") {
        $stmt = mysqli_prepare($conn, "SELECT * FROM `makeup atrist` WHERE Email = ? LIMIT 1");
    } else {
        $loginError = "Invalid role selected!";
        $stmt = null;
    }

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($passwordInput, $user['Password'])) {
                $_SESSION['user'] = $user;

                if ($role === "Client") {
                    $_SESSION['ClientID'] = $user["ClientID"];
                    header("Location: http://localhost/Softwarepages2/ClientHome/ClientHomePage.html");
                } else {
                    $_SESSION['artist_id'] = $user["ArtistID"] ?? null;
                    header("Location: http://localhost/Softwarepages2/MAHome/MAHomePage.php");
                }
                exit;
            } else {
                $loginError = "Incorrect password!";
            }
        } else {
            $loginError = "Email not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <link rel="stylesheet" href="Login.css">
</head>
<body>
    <img src="../images/logo2-removebg-preview.png" alt="Logo" class="logo">

    <div class="tabs">
        <a href="#" class="active">Login</a>
        <a href="../Signup/Signup.php" class="inactive-tab">Sign Up</a>
    </div>

    <form method="POST" action="Login.php" id="LoginForm">
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <div class="rolesholder">
            <label class="roles">Select Role:</label>
            <label class="roles"><input type="radio" name="role" value="Client" checked> Client</label>
            <label class="roles"><input type="radio" name="role" value="MakeupArtist"> Makeup Artist</label>
        </div>

        <button type="submit" class="login-btn">Log In</button>

        <?php if (!empty($loginError)): ?>
            <p style="color: red; text-align: center; margin-top: 10px;"><?php echo htmlspecialchars($loginError); ?></p>
        <?php endif; ?>
    </form>

    <script>
        document.getElementById("LoginForm").addEventListener("submit", function(e) {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email || !password) {
                alert("Please fill in all fields.");
                e.preventDefault();
                return;
            }

            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
