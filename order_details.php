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

// Fetch order ID from URL
if (!isset($_GET['orderID']) || empty($_GET['orderID'])) {
    header("location: order_summary.php");
    exit;
}

$orderID = intval($_GET['orderID']);

// Fetch order details
$sql = "SELECT o.*, oi.product_id, p.name, oi.quantity, oi.price
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.order_id = ? AND o.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $orderID, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

// If no order found or does not belong to the user, redirect
if (!$order) {
    header("location: order_summary.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Order Details - <?php echo htmlspecialchars($order['order_id']); ?></h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_amount = 0;
            do {
                $total = $order['quantity'] * $order['price'];
                $total_amount += $total;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($order['name']); ?></td>
                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                <td>$<?php echo htmlspecialchars($order['price']); ?></td>
                <td>$<?php echo $total; ?></td>
            </tr>
            <?php } while ($order = mysqli_fetch_assoc($result)); ?>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                <td><strong>$<?php echo $total_amount; ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
