<?php
// Database connection details
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "spa_system"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $user_id = intval($_POST['user_id']);
    $service_id = intval($_POST['service_id']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $status = "pending"; // Default status
    $created_at = date('Y-m-d H:i:s'); // Current timestamp

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("INSERT INTO bookings (booking_id, user_id, service_id, appointment_date, appointment_time, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $user_id, $service_id, $appointment_date, $appointment_time, $status, $created_at);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Booking successfully saved to the database."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);
}

$conn->close();
?>