<?php
include('config.php');

session_start();

// Check if user is logged in and is a service provider, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"]) || $_SESSION["usertype"] != 'service_provider') {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$provider_id = $_SESSION["id"];

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $status = $_POST['status'];

    // Validate status
    $valid_statuses = ['pending', 'completed', 'inprocess', 'cancelled'];
    if (in_array($status, $valid_statuses)) {
        $update_sql = "UPDATE appointments SET status = ? WHERE id = ? AND provider_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sii", $status, $appointment_id, $provider_id);
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
    }
}

// Fetch provider's appointments
$sql = "SELECT a.id, a.appointment_datetime, a.address, a.status, u.username AS user_name, u.phone AS user_phone, u.email AS user_email
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.provider_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $provider_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">My Appointments</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Appointment Date & Time</th>
                    <th>User Name</th>
                    <th>User Phone</th>
                    <th>User Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_datetime']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                        <td>
                            <form method="post" action="service_provider_dashboard.php">
                                <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <select name="status" class="form-control" required>
                                    <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                    <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                    <option value="inprocess" <?php if ($row['status'] == 'inprocess') echo 'selected'; ?>>In Process</option>
                                    <option value="cancelled" <?php if ($row['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">You have no appointments.</div>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
