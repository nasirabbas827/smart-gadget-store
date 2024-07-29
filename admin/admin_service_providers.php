<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all service providers
$providers_sql = "SELECT id, full_name, phone, email FROM users WHERE usertype = 'service_provider'";
$providers_result = mysqli_query($conn, $providers_sql);

// Handle deleting review
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_review'])) {
    $review_id = intval($_POST['review_id']);
    
    // Delete review
    $delete_review_sql = "DELETE FROM reviews WHERE review_id = ?";
    $delete_review_stmt = mysqli_prepare($conn, $delete_review_sql);
    mysqli_stmt_bind_param($delete_review_stmt, "i", $review_id);
    mysqli_stmt_execute($delete_review_stmt);
    mysqli_stmt_close($delete_review_stmt);
    
    // Redirect to avoid form resubmission
    header("Location: admin_service_providers.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Service Providers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Service Providers Overview</h2>
    
    <!-- Display Service Providers -->
    <h3 class="mt-4">Service Providers</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Provider ID</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($provider = mysqli_fetch_assoc($providers_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($provider['id']); ?></td>
                    <td><?php echo htmlspecialchars($provider['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($provider['phone']); ?></td>
                    <td><?php echo htmlspecialchars($provider['email']); ?></td>
                    <td>
                        <!-- Links to view appointments and reviews -->
                        <a href="admin_provider_appointments.php?provider_id=<?php echo $provider['id']; ?>" class="btn btn-info btn-sm">View Appointments</a>
                        <a href="admin_provider_reviews.php?provider_id=<?php echo $provider['id']; ?>" class="btn btn-secondary btn-sm">View Reviews</a>
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
