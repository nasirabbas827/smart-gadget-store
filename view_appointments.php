<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch user's appointments
$sql = "SELECT a.id, a.appointment_datetime, a.address, a.status, p.full_name AS provider_name, p.phone AS provider_phone, p.email AS provider_email
        FROM appointments a
        JOIN users p ON a.provider_id = p.id
        WHERE a.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
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
                    <th>Provider Name</th>
                    <th>Provider Phone</th>
                    <th>Provider Email</th>
                    <th>Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_datetime']); ?></td>
                        <td><?php echo htmlspecialchars($row['provider_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['provider_phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['provider_email']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">You have no upcoming appointments.</div>
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
