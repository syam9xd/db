<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spa_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);
$email = $conn->real_escape_string($data['email']);
$code = $conn->real_escape_string($data['verificationCode']);

// Check verification code
$query = "SELECT * FROM users WHERE email = '$email' AND verification_code = '$code' AND is_verified = 0";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Update is_verified to 1
    $updateQuery = "UPDATE users SET is_verified = 1 WHERE email = '$email'";
    if ($conn->query($updateQuery)) {
        echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update verification status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired verification code']);
}

$conn->close();
?>