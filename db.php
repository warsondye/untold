<?php
// db.php - simple mysqli connection
// Edit credentials as needed (or use .env extensions if you prefer)
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'church_dashboard';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status'=>'error','message'=>'DB connection failed: '.$conn->connect_error]);
    exit;
}
$conn->set_charset('utf8mb4');
?>
