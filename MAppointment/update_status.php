<?php
session_start();
include("../config.php"); 

if (isset($_POST['reservation_id']) && isset($_POST['action'])) {
    $id = intval($_POST['reservation_id']);
    $action = $_POST['action'];

    if ($action === 'active') {
        $newStatus = 'Active';
    } elseif ($action === 'cancel') {
        $newStatus = 'Canceled';
    } else {
        exit("Invalid action.");
    }

    $update = "UPDATE reservation SET Status = '$newStatus' WHERE ReservationID = $id";
    mysqli_query($conn, $update);

    header("Location: MAppointment.php");
    exit();
} else {
    echo "Missing data.";
}
?>
