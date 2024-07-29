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

// Get the product ID from the query string
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Check if the product is already in the cart
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Update the quantity if the product is already in the cart
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
    } else {
        // Add the product to the cart
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Product added to cart successfully!";
        header("Location: home.php");
        exit;
    } else {
        echo "Error adding product to cart: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "No product ID provided.";
}
?>
