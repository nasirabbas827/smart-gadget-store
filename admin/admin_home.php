<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch data for dashboard
$totalUsers = 0;
$totalCategories = 0;
$totalOrders = 0;
$totalProviderReviews = 0;
$totalProductReviews = 0;
$totalAppointments = 0;

// Example queries to fetch data
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalCategories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM categories"))['total'];
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];
$totalProviderReviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM provider_reviews"))['total'];
$totalProductReviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM reviews"))['total'];
$totalAppointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments"))['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <div class="row mt-4">
        <!-- Total Users Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Total Categories Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Categories</h5>
                    <p class="card-text"><?php echo $totalCategories; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Total Orders Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text"><?php echo $totalOrders; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Total Provider Reviews Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Provider Reviews</h5>
                    <p class="card-text"><?php echo $totalProviderReviews; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Total Product Reviews Card -->
        <div class="col-md-3 mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Product Reviews</h5>
                    <p class="card-text"><?php echo $totalProductReviews; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Total Appointments Card -->
        <div class="col-md-3 mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Appointments</h5>
                    <p class="card-text"><?php echo $totalAppointments; ?></p>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
