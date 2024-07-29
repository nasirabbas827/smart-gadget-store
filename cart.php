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

// Update cart quantities
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = intval($quantity);

            // Fetch the product's available stock
            $product_sql = "SELECT stock_quantity FROM products WHERE product_id = ?";
            $product_stmt = mysqli_prepare($conn, $product_sql);
            mysqli_stmt_bind_param($product_stmt, "i", $product_id);
            mysqli_stmt_execute($product_stmt);
            mysqli_stmt_bind_result($product_stmt, $stock_quantity);
            mysqli_stmt_fetch($product_stmt);
            mysqli_stmt_close($product_stmt);

            // Ensure quantity does not exceed stock
            if ($quantity > $stock_quantity) {
                $quantity = $stock_quantity;
            }

            // Update the cart with the new quantity
            $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "iii", $quantity, $user_id, $product_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }
    }
}

// Fetch cart items
$sql = "SELECT c.cart_id, p.product_id, p.name, p.price, c.quantity, p.stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Your Cart</h2>
    
    <form method="post" action="cart.php">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0;
                while ($row = mysqli_fetch_assoc($result)):
                    $product_total = $row['price'] * $row['quantity'];
                    $total_price += $product_total;
                ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                    <td>
                        <input type="number" class="form-control" name="quantity[<?php echo $row['product_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1" max="<?php echo $row['stock_quantity']; ?>">
                    </td>
                    <td>$<?php echo $product_total; ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Price:</strong></td>
                    <td><strong>$<?php echo $total_price; ?></strong></td>
                </tr>
            </tbody>
        </table>
        <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
        <a href="order.php" class="btn btn-success">Proceed to Checkout</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
