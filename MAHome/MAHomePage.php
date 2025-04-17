<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Makeup Artist Home</title>
    <link rel="stylesheet" href="../General.css">
    <link rel="stylesheet" href="MAHomePage.css">
    <script src="../FilterScript.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../images/logo2.jpg" alt="رواء Logo">
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="MAHomePage.php">Home</a></li>
                <li><a href="../MAppointment/MAppointment.php">Reservations</a></li>
                <li><a href="../MakeUpArtist/modifyArtistProfile.php">Edit Profile</a></li>
                <li><a href="../logout.php" class="signout">Signout</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard">
        <!-- <div class="filters">
            <label for="sortStatus"><strong>Filter by Status:</strong></label>
            <select id="sortStatus" onchange="sortAppointmentsByStatus()">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>-->

        <section class="reservations">
<!--            <h2>Your Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>-->
                <tbody>
                    <?php
                    session_start();
                        $servername = "localhost";
                        $username = "root"; // Default for MAMP
                        $password = "root"; // Default for MAMP
                        $database = "ruwaa";

                        // Create connection
                        $conn = mysqli_connect($servername, $username, $password, $database , 8889);

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                    if (!isset($_SESSION['artist_id'])) {
                        //header("Location: ../Login/Login.php");
                        //exit();
                    }
                    $artistID = $_SESSION['artist_id'];
                    // Get upcoming reservation
$sql = "SELECT c.Name, res.Date, res.Time, res.Status
        FROM reservation res
        JOIN `client` c ON res.ClientID = c.ClientID
        WHERE res.ArtistID = $artistID
            AND res.Date >= CURDATE()
        ORDER BY res.Date ASC, res.Time ASC
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$reservation = mysqli_fetch_assoc($result);
?>

        <h2>Your Upcoming Reservation</h2>
        <div class="reservation">
            <?php if ($reservation): ?>
                <ul>
                    <li><strong>Client:</strong> <?php echo $reservation['Name']; ?></li>
                    <li><strong>Date:</strong> <?php echo $reservation['Date']; ?></li>
                    <li><strong>Time:</strong> <?php echo $reservation['Time']; ?></li>
                    <li><strong>Status:</strong> <span class="confirmedd"><?php echo $reservation['Status']; ?></span></li>
                </ul>
            <?php else: ?>
                <p>No Upcoming Reservations</p>
            <?php endif; ?>
        </div>
    </section>
                </tbody>
            </table>
        </section>
    </main>
<section class="about-platform">
        <h2>About رواء</h2>
        <p>رواء is an innovative platform that connects beauty professionals with customers seeking high-quality makeup and hairstyling services. Our goal is to provide a seamless booking experience, ensuring you can find and book the perfect beauty expert for any occasion.</p>

        <div class="about-images">
            <div class="about-card">
                <img src="../images/About.jpg" alt="Easy Booking">
                <h3>Easy Booking</h3>
                <p>Schedule your appointments effortlessly with our user-friendly platform.</p>
            </div>
            <div class="about-card">
                <img src="../images/About2.jpg" alt="Professional Beauty Services">
                <h3>Professional Services</h3>
                <p>Find top-rated beauty professionals for weddings, parties, and everyday looks.</p>
            </div>
            <div class="about-card">
                <img src="../images/About4.jpg" alt="User-Friendly Experience">
                <h3>Convenient Experience</h3>
                <p>Enjoy a smooth and stress-free beauty service tailored to your needs.</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 رواء. All Rights Reserved.</p>
    </footer>
</body>
</html>
