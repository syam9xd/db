<?php
// Start a session to track user login
session_start();

// Check if the form has been submitted
$error_message = ""; // Initialize error message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection details
    $servername = "localhost"; // Replace with your DB server name
    $db_username = "root"; // Replace with your DB username
    $db_password = ""; // Replace with your DB password
    $dbname = "spa_system"; // Replace with your DB name

    // Create a connection to the database
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to check the user credentials
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store user details in the session
            $_SESSION['user_id'] = $user['id'];

            // Redirect based on user type (adjust as needed)
            if ($user['type'] === 'admin') {
                header("Location: admin-dashboard.html");
            } else {
                header("Location: booking.php");
            }
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with the provided email.";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Serenity Spa</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
    <header class="main-header">
    <div class="logo">
        <h1>Serenity Spa</h1>
    </div>
    <nav class="nav-links">
        <a href="index.html">Back to Home</a>
    </nav>
</header>
  <div class="login-container">
    <div class="login-card">
      <h1>Welcome to Serenity Spa</h1>
      <p class="subtitle">Log in to your account</p>

      <!-- Single Login Form -->
      <form id="login-form" class="login-form" method="POST" action="">
        <!-- Error Message Display -->
        <?php if (!empty($error_message)): ?>
            <p class="error-message" style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required />

        <button type="submit" class="login-button">Login</button>
        <p class="register-link">Don't have an account? <a href="register.php">Register</a></p>
      </form>
    </div>
  </div>
</body>
</html>