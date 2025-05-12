<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Leave empty if root has no password
$dbname = "spa_system";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error and success messages
$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $conn->real_escape_string($_POST['fullName']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $sex = $conn->real_escape_string($_POST['sex']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate passwords
    if ($password !== $confirmPassword) {
        $error_message = "Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if the email already exists
        $emailCheckQuery = "SELECT * FROM users WHERE email = '$email'";
        $emailCheckResult = $conn->query($emailCheckQuery);

        if ($emailCheckResult->num_rows > 0) {
            $error_message = "Email is already registered.";
        } else {
            // Insert user data into the database
            $stmt = $conn->prepare("INSERT INTO users (full_name, birthday, sex, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullName, $birthday, $sex, $email, $phone, $hashedPassword);

            if ($stmt->execute()) {
                // Redirect to the next HTML page
                header("Location: verify.html");
                exit();
            } else {
                $error_message = "Error inserting data: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Serenity Spa</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="register-container">
        <header class="register-header">
            <h1>Create Your Account</h1>
            <p>Join Serenity Spa to book your wellness journey</p>
        </header>

        <form id="registerForm" class="register-form" method="POST" action="">
    <div class="form-group">
        <label for="fullName">Full Name</label>
        <input type="text" id="fullName" name="fullName" required>
    </div>
    <div class="flex-row">
        <div class="half-width">
            <label for="birthday">Birthday</label>
            <input type="date" id="birthday" name="birthday" required />
        </div>
        <div class="half-width">
            <label for="sex">Sex</label>
            <select id="sex" name="sex" required>
                <option value="" disabled selected>Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>
        <small class="email-hint">We'll send a verification code to this email</small>
    </div>

    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required 
               pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
               title="Password must be at least 8 characters long and include both letters and numbers">
        <small class="password-hint">Password must be at least 8 characters with letters and numbers</small>
    </div>

    <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
    </div>


            <button type="submit" class="register-submit-btn">Create Account</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.html">Login here</a></p>
        </div>
    </div>

    <script>
        function generateVerificationCode() {
            return Math.floor(100000 + Math.random() * 900000).toString();
        }

        function handleRegistration(event) {
            event.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const email = document.getElementById('email').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }

            const verificationCode = generateVerificationCode();
            
            const userData = {
                fullName: document.getElementById('fullName').value,
                email: email,
                phone: document.getElementById('phone').value,
                verificationCode: verificationCode,
                isVerified: false
            };
            
            localStorage.setItem('userData', JSON.stringify(userData));
            
            alert(`Your verification code is: ${verificationCode}\n\nPlease use this code to verify your email.`);
            
            window.location.href = 'verify.html';
            return false;
        }
    </script>
</body>
</html> 