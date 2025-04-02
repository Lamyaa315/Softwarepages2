<?php

session_start();

session_destroy();

header("Location: Welcome/WelcomePage.php" );

exit();

?>