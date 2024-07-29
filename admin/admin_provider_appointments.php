<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get provider ID from the URL
$provider_id = isset($_GET['provider_id']) ? intval($_GET['provider_id']) : 0;

// Fetch provider appointments
$appointments_sql = "SELECT a.id, appointment_datetime, a.address, a.status, u.username AS user_name
                      FROM appointments a
                      JOIN users u ON a.user_id = u.id
                      WHERE a.provider_id = ?";
$appointments_stmt = mysqli_prepare($conn, $appointments_sql);
mysqli_stmt_bind_param($appointments_stmt, "i", $provider_id);
mysqli_stmt_execute($appointments_stmt);
$appointments_result = mysqli_stmt_get_result($appointments_stmt);
mysqli_stmt_close($appointments_stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Provider Appointments</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Appointments for Provider ID <?php echo htmlspecialchars($provider_id); ?></h2>
    
    <!-- Display Appointments -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Datetime</th>
                <th>Address</th>
                <th>Status</th>
                <th>User Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['appointment_datetime']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['address']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['user_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
