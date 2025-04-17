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
    <style>
        .search-filter {
    display: flex;
    justify-content: center;
    margin: 30px 0 20px;
}

.search-container {
    display: flex;
    width: 100%;
    max-width: 400px;
    border: 2px solid #bfa380;
    border-radius: 10px;
    overflow: hidden;
    background: white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.search-container input[type="text"] {
    flex: 1;
    padding: 12px 15px;
    font-size: 16px;
    border: none;
    outline: none;
}

.search-container button {
    background-color: #bfa380;
    color: white;
    padding: 0 20px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-container button:hover {
    background-color: #a98a6a;
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
            <li><a href="../ClientHome/ClientHomePage.php">Home</a></li>
                <li><a href="../tips/tips.html">Beauty Tips</a></li>
                <li><a href="../CAppointment/CAppointment.php">Reservations</a></li>
                <li><a href="../MakeupArtistList.php">Makeup Artists</a></li>
                <li><a href="../logout.php" class="signout">Signout</a></li>
        </ul>
    </nav>
  </header>

  <main>
<div class="search-filter">
    <form method="GET" action="">
        <div class="search-container">
            <input type="text" name="search" placeholder="Search for an artist..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">🔍</button>
        </div>
    </form>
</div>

     
      
    <div class="grid-container" id="artistList">
        <!-- سيتم تحميل الفنانات هنا -->
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($artist = mysqli_fetch_assoc($result)) {
    $workImages = !empty($artist['work']) ? json_decode($artist['work'], true) : [];


    echo "<div class='artist'>";
    echo "<a href='../MakeUpArtist/MakeUpArtist.php?ArtistID={$artist['ArtistID']}'>";

    
    if (!empty($artist['Profile']) && file_exists("../" . $artist['Profile'])) {
        echo "<img src='../{$artist['Profile']}' alt='{$artist['Name']}'>";
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
            echo "<img src='../$image' alt='Work Image' class='work-image'>";
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


