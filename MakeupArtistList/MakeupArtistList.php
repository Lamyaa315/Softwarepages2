<?php

session_start();

include 'config.php';

if (!isset($_SESSION['ClientID'])) {
    header("Location: Login/Login.php");
    exit();
}

$clientID = $_SESSION['ClientID'];
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makeup Artists</title>
    <link rel="stylesheet" href="MakeupArtistList.css">
</head>
<body>
  <header>
    <div class="logo">
        <img src="logo2.jpg" alt="رواء Logo">
    </div>
    <nav class="navigation">
        <ul>
            <li><a href="ClientHome/ClientHomePage.php">Home</a></li>
                <li><a href="tips/tips.php">Beauty Tips</a></li>
                <li><a href="CAppointment/CAppointment.php">Reservations</a></li>
                <li><a href="MakeupArtistList/MakeupArtistList.php">Makeup Artists</a></li>
                <li><a href="logout.php" class="signout">Signout</a></li>
        </ul>
    </nav>
  </header>

  <main>
    <div class="search-filter">
        <input type="text" id="search" placeholder="Search for an artist..." onkeyup="filterArtists()">
    </div>

      <?php 
      
      $sql = "SELECT ArtistID, Name, Services, Profile, InstagramAccount, work FROM 'makeup atrist' ";
      
      $result = mysqli_query($conn , $sql);
     
      ?>
      
      
    <div class="grid-container" id="artistList">
        <!-- سيتم تحميل الفنانات هنا -->
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($artist = mysqli_fetch_assoc($result)) {
                $workImages = json_decode($artist['work'], true);
                echo "
                <div class ='artist'>
                    <a href='MakeUpArtist.php?ArtistID={$artist['ArtistID']}'>
                        <img src='{$artist['Profile']}' alt='{$artist['Name']}'>
                    </a>
                            
                    <div class='text'>
                        <h3>{$artist['Name']}</h3>
                        <p><strong>Services:</strong> {$artist['Services']}</p>
                        <p><a href='{$artist['InstagramAccount']}' target='_blank'>Instagram</a></p>
                        
                    <h4>My Work</h4>
                        <div class='work-gallery'>";
                
                // عرض كل الصور من JSON
                if (!empty($workImages)) {
                    foreach ($workImages as $image) {
                        echo "<img src='$image' alt='Work Image' class='work-image'>";
                    }
                } else {
                    echo "<p>No work images available.</p>";
                }

                echo "</div>
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


