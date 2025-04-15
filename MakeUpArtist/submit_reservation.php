<?php
session_start();

header('Content-Type: application/json');

// تأكد من أن المستخدم مسجل دخول كـ Client
if (!isset($_SESSION['ClientID'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// تأكد أن الطلب من نوع POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// استلام البيانات من الـ fetch
$date      = $_POST['date'] ?? '';
$time      = $_POST['time'] ?? '';
$service   = $_POST['service'] ?? '';
$artist_id = $_POST['artist_id'] ?? '';
$client_id = $_SESSION['ClientID'];
$status    = "Pending"; 

// التحقق من وجود جميع الحقول المطلوبة
if (empty($date) || empty($time) || empty($service) || empty($artist_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// الاتصال بقاعدة البيانات
$servername = "localhost";
$username   = "root";
$password   = "root";
$database   = "ruwaa";

$conn = new mysqli($servername, $username, $password, $database, 8889);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO reservation (Date, Time, Status, Service, ClientID, ArtistID) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssii", $date, $time, $status, $service, $client_id, $artist_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to insert reservation']);
}

$stmt->close();
$conn->close();
?>
