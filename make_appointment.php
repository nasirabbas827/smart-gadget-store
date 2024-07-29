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

// Handle appointment booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $provider_id = intval($_POST['provider_id']);
    $appointment_datetime = $_POST['appointment_datetime'];
    $address = $_POST['address'];

    // Validate datetime to ensure it is not in the past
    $current_datetime = date("Y-m-d\TH:i");
    if ($appointment_datetime > $current_datetime) {
        $appointment_sql = "INSERT INTO appointments (user_id, provider_id, appointment_datetime, address, status) VALUES (?, ?, ?, ?, 'pending')";
        $appointment_stmt = mysqli_prepare($conn, $appointment_sql);
        mysqli_stmt_bind_param($appointment_stmt, "iiss", $user_id, $provider_id, $appointment_datetime, $address);
        mysqli_stmt_execute($appointment_stmt);
        mysqli_stmt_close($appointment_stmt);
        $message = "Appointment successfully booked!";
    } else {
        $message = "Please select a future date and time.";
    }
} else {
    $provider_id = intval($_GET['provider_id']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Make Appointment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Make Appointment</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="make_appointment.php">
        <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($provider_id); ?>">
        
        <div class="form-group">
            <label for="appointment_datetime">Appointment Date and Time:</label>
            <input type="datetime-local" class="form-control" min="<?= date('Y-m-d\TH:i'); ?>" id="appointment_datetime" name="appointment_datetime" required>
        </div>
        
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Book Appointment</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
