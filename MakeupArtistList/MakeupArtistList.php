 <?php

session_start();

include '../config.php';

 if (!isset($_SESSION['ClientID'])) {
    header("Location: ../Login/Login.php");
    exit();
}

$clientID = $_SESSION['ClientID']; 



$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT ArtistID, Name, Services, Profile, PhoneNumber, InstagramAccount, work, price FROM `makeup atrist`";

if (!empty($search)) {
    $sql .= " WHERE Name LIKE '%$search%' OR Services LIKE '%$search%'";
}

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makeup Artists</title>
    <link rel="stylesheet" href="../General.css">
    <link rel="stylesheet" href="MakeupArtistList.css">
</head>
<body>
  <header>
    <div class="logo">
        <img src="../images/logo2.jpg" alt="رواء Logo">
    </div>
    <nav class="navigation">
        <ul>
            <li><a href="ClientHome/ClientHomePage.php">Home</a></li>
                <li><a href="tips/tips.html">Beauty Tips</a></li>
                <li><a href="CAppointment/CAppointment.php">Reservations</a></li>
                <li><a href="MakeupArtistList.php">Makeup Artists</a></li>
                <li><a href="../logout.php" class="signout">Signout</a></li>
        </ul>
    </nav>
  </header>

  <main>
    <div class="search-filter">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search for an artist..." value="<?php echo $search; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

     
      
    <div class="grid-container" id="artistList">
        <!-- سيتم تحميل الفنانات هنا -->
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($artist = mysqli_fetch_assoc($result)) {
    $workImages = !empty($artist['work']) ? json_decode($artist['work'], true) : [];


    echo "<div class='artist'>";
    echo "<a href='../MakeUpAtrist/MakeUpArtist.php?ArtistID={$artist['ArtistID']}'>";

    
    if (!empty($artist['Profile']) && file_exists("../images/" . $artist['Profile'])) {
        echo "<img src='../images/{$artist['Profile']}' alt='{$artist['Name']}'>";
    } else {
        echo "<div class='no-profile'>No profile picture available</div>";
    }

    echo "</a>";

    echo "<div class='text'>
            <h3>{$artist['Name']}</h3>
            <p><strong>Services:</strong> {$artist['Services']} - {$artist['price']}</p>
            <h4>My Work</h4>
            <div class='work-gallery'>";

    // ✅ صور الأعمال
    if (!empty($workImages)) {
        foreach ($workImages as $image) {
            echo "<img src='../images/$image' alt='Work Image' class='work-image'>";
        }
    } else {
        echo "<p>No work images available.</p>";
    }

    echo "</div>
        <div class='contact'>
            <p> For Contact: </p>
            <p><a href='{$artist['InstagramAccount']}' target='_blank'>• Instagram</a></p>
            <p><a href='{$artist['PhoneNumber']}' target='_blank'>• WhatsApp</a></p>
        </div>
    </div>
    </div>";
}

            
        } else {
            echo "<p style='text-align: center; font-size: 18px; color: red;'>No makeup artists found.</p>";
        }
        ?>
    </div>
</main>

  <footer>
    <p>&copy; 2025 رواء. All Rights Reserved.</p>
  </footer>

</body>
</html>


