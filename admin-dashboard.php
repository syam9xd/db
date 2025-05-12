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

// Fetch appointments (initial fetch for page load)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appointmentsQuery = "SELECT * FROM appointments ORDER BY appointment_time DESC";
    $appointmentsResult = $conn->query($appointmentsQuery);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Serenity Spa</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        /**
         * Fetch and update the appointments dynamically.
         */
        function fetchAppointments() {
            fetch('admin-dashboard.php')
                .then(response => response.text())
                .then(html => {
                    document.querySelector('#appointments-list').innerHTML = html;
                })
                .catch(error => console.error('Error fetching appointments:', error));
        }

        /**
         * Handle Appointment Actions
         * Sends an AJAX request to the server to perform the given action (confirm, cancel, pending, delete).
         * @param {number} id - The ID of the appointment.
         * @param {string} action - The action to perform (confirm, cancel, pending, delete).
         */
        function handleAppointmentAction(id, action) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('action', action);

            fetch('admin-dashboard.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    alert(result.message);
                    if (result.success) {
                        fetchAppointments(); // Refresh appointments dynamically
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body>
    <div class="admin-layout">
        <header class="admin-header">
            <h1>Serenity Spa Admin</h1>
            <div class="admin-profile">
                <img src="images/admin.jpg" alt="Admin">
                <span>Admin</span>
            </div>
        </header>

        <main class="admin-main">
            <section class="appointments">
                <div class="section-header">
                    <h2>Appointments</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Staff</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appointments-list">
                            <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && $appointmentsResult->num_rows > 0): ?>
                                <?php while ($row = $appointmentsResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
                                        <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($row['appointment_time']))); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td>
                                            <button class="btn-confirm" onclick="handleAppointmentAction(<?php echo $row['id']; ?>, 'confirm')">Confirm</button>
                                            <button class="btn-cancel" onclick="handleAppointmentAction(<?php echo $row['id']; ?>, 'cancel')">Cancel</button>
                                            <button class="btn-pending" onclick="handleAppointmentAction(<?php echo $row['id']; ?>, 'pending')">Pending</button>
                                            <button class="btn-delete" onclick="handleAppointmentAction(<?php echo $row['id']; ?>, 'delete')">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No appointments found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <?php
    // Handle POST requests for appointment actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        $appointmentId = intval($_POST['id']);

        if ($action === 'confirm') {
            $updateQuery = "UPDATE appointments SET status = 'confirmed' WHERE id = $appointmentId";
            if ($conn->query($updateQuery) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Appointment confirmed successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error confirming appointment: ' . $conn->error]);
            }
        } elseif ($action === 'cancel') {
            $updateQuery = "UPDATE appointments SET status = 'cancelled' WHERE id = $appointmentId";
            if ($conn->query($updateQuery) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Appointment cancelled successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error cancelling appointment: ' . $conn->error]);
            }
        } elseif ($action === 'pending') {
            $updateQuery = "UPDATE appointments SET status = 'pending' WHERE id = $appointmentId";
            if ($conn->query($updateQuery) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Appointment set to pending successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error setting appointment to pending: ' . $conn->error]);
            }
        } elseif ($action === 'delete') {
            $deleteQuery = "DELETE FROM appointments WHERE id = $appointmentId";
            if ($conn->query($deleteQuery) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting appointment: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        }
        exit;
    }

    $conn->close();
    ?>
</body>
</html>