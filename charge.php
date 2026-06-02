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

// Get POST data
$orderID = intval($_POST['orderID']);
$amount = intval($_POST['amount']);

// Stripe API keys
$stripe_secret_key = "YOUR_OWN_API_KEY";
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Create a new charge
try {
    $charge = \Stripe\Charge::create([
        'amount' => $amount,
        'currency' => 'usd',
        'source' => $_POST['stripeToken'],
        'description' => 'Payment for Order ID: ' . $orderID
    ]);

    // Update order status to paid
    $sql = "UPDATE orders SET payment_method = 'paid' WHERE order_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $orderID, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect to confirmation page
    header("location: order_confirmation.php?orderID=" . $orderID);
    exit;

} catch (\Stripe\Exception\CardException $e) {
    // Handle card errors
    echo 'Error: ' . $e->getMessage();
}

$conn->close();
?>
