<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all orders
$orders_sql = "SELECT o.order_id, o.user_id, o.order_date, o.order_status, u.username
               FROM orders o
               JOIN users u ON o.user_id = u.id
               ORDER BY o.order_date DESC";
$orders_result = mysqli_query($conn, $orders_sql);

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = $_POST['order_status'];
    
    $update_status_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $update_status_stmt = mysqli_prepare($conn, $update_status_sql);
    mysqli_stmt_bind_param($update_status_stmt, "si", $order_status, $order_id);
    mysqli_stmt_execute($update_status_stmt);
    mysqli_stmt_close($update_status_stmt);
    
    header("Location: admin_orders.php"); // Refresh page to reflect changes
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Manage Orders</h2>
    
    <!-- Orders Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Order Date</th>
                <th>Order Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                    <td>
                        <!-- Button to View Order Items -->
                        <a href="admin_order_items.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm m-2">View Items</a>
                        
                        <!-- Status Update Form -->
                        <form method="post" action="admin_orders.php" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="order_status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="pending" <?php if ($order['order_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="completed" <?php if ($order['order_status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                <option value="inprocess" <?php if ($order['order_status'] == 'inprocess') echo 'selected'; ?>>In Process</option>
                                <option value="cancelled" <?php if ($order['order_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </form>
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
