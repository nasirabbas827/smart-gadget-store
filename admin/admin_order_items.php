<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch orders
$order_sql = "SELECT * FROM orders";
$order_result = mysqli_query($conn, $order_sql);

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['order_status'];

    $update_status_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $update_status_stmt = mysqli_prepare($conn, $update_status_sql);
    mysqli_stmt_bind_param($update_status_stmt, "si", $new_status, $order_id);
    mysqli_stmt_execute($update_status_stmt);
    mysqli_stmt_close($update_status_stmt);

    // Redirect to avoid form resubmission
    header("Location: admin_order_items.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders and Order Items</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Orders and Order Items</h2>
    
    <!-- Display Orders -->
    <?php while ($order = mysqli_fetch_assoc($order_result)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Order ID: <?php echo $order['order_id']; ?></h5>
                <p><strong>User ID:</strong> <?php echo $order['user_id']; ?></p>
                <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
                <p><strong>Full Name:</strong> <?php echo $order['full_name']; ?></p>
                <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
                <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
                <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
                <p><strong>Total Price:</strong> $<?php echo $order['total_price']; ?></p>
                
                <!-- Order Status Update Form -->
                <form method="post" action="admin_order_items.php">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <div class="form-group">
                        <label for="order_status">Order Status:</label>
                        <select class="form-control" id="order_status" name="order_status">
                            <option value="pending" <?php if ($order['order_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="completed" <?php if ($order['order_status'] == 'completed') echo 'selected'; ?>>Completed</option>
                            <option value="inprocess" <?php if ($order['order_status'] == 'inprocess') echo 'selected'; ?>>In Process</option>
                            <option value="cancelled" <?php if ($order['order_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </form>
                
                <!-- Display Order Items -->
                <h5 class="mt-4">Order Items</h5>
                <?php
                $order_id = $order['order_id'];
                $items_sql = "SELECT oi.order_item_id, oi.product_id, oi.quantity, oi.price, p.name AS product_name
                              FROM order_items oi
                              JOIN products p ON oi.product_id = p.product_id
                              WHERE oi.order_id = ?";
                $items_stmt = mysqli_prepare($conn, $items_sql);
                mysqli_stmt_bind_param($items_stmt, "i", $order_id);
                mysqli_stmt_execute($items_stmt);
                $items_result = mysqli_stmt_get_result($items_stmt);
                ?>
                <ul class="list-group">
                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <li class="list-group-item">
                            <strong>Product Name:</strong> <?php echo htmlspecialchars($item['product_name']); ?><br>
                            <strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?><br>
                            <strong>Price:</strong> $<?php echo htmlspecialchars($item['price']); ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php mysqli_stmt_close($items_stmt); ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
