<?php
include('config.php');
require_once('stripe-php-master/init.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch user orders
$sql = "SELECT o.order_id, o.order_date, o.total_price, o.payment_method, o.order_status
        FROM orders o
        WHERE o.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Your Orders</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                <td>$<?php echo htmlspecialchars($order['total_price']); ?></td>
                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                <td>
                    <?php if ($order['payment_method'] === 'unpaid'): ?>
                        <a href="pay_now.php?orderID=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-primary">Pay Now</a>
                    <?php endif; ?>
                    <a href="order_details.php?orderID=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-info">View Details</a>
                </td>
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
