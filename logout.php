<?php

session_start();

session_destroy();

header("Location:WelcomePage.php" );

exit();

?>