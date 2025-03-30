<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$database = "ruwaa";

$conn = mysqli_connect($servername, $username, $password, $database);
$loginError = "";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $sql = "SELECT * FROM client WHERE Email = '$email' AND role = '$role' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($passwordInput, $user['Password'])) {
            $_SESSION['user'] = $user;

            if ($role === "MakeupArtist") {
                header("Location: ../MAHome/MAHomePage.html");
            } else {
                header("Location: ../ClientHome/ClientHomePage.php");
            }
            exit();
        } else {
            $loginError = "!كلمة المرور غير صحيحة";
        }
    } else {
        $loginError = "!البريد الإلكتروني غير صحيح";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Login.css">
    <title>Login Page</title>
</head>
<body>
    <img src="logo2-removebg-preview.png" alt="Logo" class="logo">

    <div class="tabs">
        <a href="#" class="active">Login</a>
        <a href="../signup.html" class="inactive-tab">Sign up</a>
    </div>

    <form method="POST" action="login.php" id="LoginForm">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <div class="rolesholder">
            <label class="roles">Your Role: </label>
            <label class="roles">
                <input type="radio" name="role" value="Client" checked> Client
            </label>
            <label class="roles">
                <input type="radio" name="role" value="MakeupArtist"> Makeup Artist
            </label>
        </div>

        <button type="submit" class="login-btn">Log In</button>

        <?php if (!empty($loginError)): ?>
            <p style="color: red; text-align: center; margin-top: 10px;"> <?php echo $loginError; ?> </p>
        <?php endif; ?>
    </form>
</body>
</html>
