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

// Fetch cart items
$sql = "SELECT c.cart_id, p.product_id, p.name, p.price, c.quantity, p.stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Calculate total price and quantity
$total_amount = 0;
$cart_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $total_amount += $row['price'] * $row['quantity'];
    $cart_items[] = $row;
}

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Insert order into orders table
    $order_sql = "INSERT INTO orders (user_id, total_price, payment_method, order_status, order_date) VALUES (?, ?, 'unpaid', 'pending', NOW())";
    $order_stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($order_stmt, "id", $user_id, $total_amount);
    mysqli_stmt_execute($order_stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($order_stmt);

    // Insert order items
    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $price = $item['price'];
        $quantity = $item['quantity'];

        $order_item_sql = "INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
        $order_item_stmt = mysqli_prepare($conn, $order_item_sql);
        mysqli_stmt_bind_param($order_item_stmt, "iiii", $order_id, $product_id, $price, $quantity);
        mysqli_stmt_execute($order_item_stmt);
        mysqli_stmt_close($order_item_stmt);

        // Update product stock
        $update_stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
        $update_stock_stmt = mysqli_prepare($conn, $update_stock_sql);
        mysqli_stmt_bind_param($update_stock_stmt, "ii", $quantity, $product_id);
        mysqli_stmt_execute($update_stock_stmt);
        mysqli_stmt_close($update_stock_stmt);
    }

    // Clear the cart
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
    mysqli_stmt_bind_param($clear_cart_stmt, "i", $user_id);
    mysqli_stmt_execute($clear_cart_stmt);
    mysqli_stmt_close($clear_cart_stmt);

    // Redirect to a confirmation page
    header("Location: order_confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Checkout</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Checkout</h2>

    <form method="post" action="order.php">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="total_amount">Total Amount: $<?php echo number_format($total_amount, 2); ?></label>
        </div>
        <button type="submit" name="checkout" class="btn btn-success">Place Order</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
