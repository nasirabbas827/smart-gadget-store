<?php
include('config.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $shipping_address = $_POST["shipping_address"];
    $full_name = $_POST["full_name"];
    $phone = $_POST["phone"];
    $payment_method = $_POST["payment_method"];

    // Calculate total price
    $total_price = 0;
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = intval($quantity);
        
        // Fetch the product price
        $product_sql = "SELECT price FROM products WHERE product_id = ?";
        $product_stmt = mysqli_prepare($conn, $product_sql);
        mysqli_stmt_bind_param($product_stmt, "i", $product_id);
        mysqli_stmt_execute($product_stmt);
        mysqli_stmt_bind_result($product_stmt, $price);
        mysqli_stmt_fetch($product_stmt);
        mysqli_stmt_close($product_stmt);
        
        $total_price += $price * $quantity;
    }

    // Insert order
    $order_sql = "INSERT INTO orders (user_id, shipping_address, full_name, phone, payment_method, order_status, total_price) 
                  VALUES (?, ?, ?, ?, ?, 'pending', ?)";
    $order_stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($order_stmt, "issssi", $user_id, $shipping_address, $full_name, $phone, $payment_method, $total_price);
    mysqli_stmt_execute($order_stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($order_stmt);

    // Insert order items
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = intval($quantity);

        $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                           VALUES (?, ?, ?, (SELECT price FROM products WHERE product_id = ?))";
        $order_item_stmt = mysqli_prepare($conn, $order_item_sql);
        mysqli_stmt_bind_param($order_item_stmt, "iiii", $order_id, $product_id, $quantity, $product_id);
        mysqli_stmt_execute($order_item_stmt);
        mysqli_stmt_close($order_item_stmt);
    }

    // Clear the cart
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
    mysqli_stmt_bind_param($clear_cart_stmt, "i", $user_id);
    mysqli_stmt_execute($clear_cart_stmt);
    mysqli_stmt_close($clear_cart_stmt);

    echo "Order placed successfully! <a href='order_confirmation.php'>View Order Confirmation</a>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Order Confirmation</h2>
    <p>Your order has been placed successfully. Thank you for shopping with us!</p>
    <p><a href="home.php">Back to Home</a></p>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
