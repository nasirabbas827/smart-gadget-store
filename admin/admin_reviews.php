<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle review deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_review'])) {
    $review_id = intval($_POST['review_id']);
    
    // Delete review
    $delete_review_sql = "DELETE FROM reviews WHERE review_id = ?";
    $delete_review_stmt = mysqli_prepare($conn, $delete_review_sql);
    mysqli_stmt_bind_param($delete_review_stmt, "i", $review_id);
    mysqli_stmt_execute($delete_review_stmt);
    mysqli_stmt_close($delete_review_stmt);
    
    // Redirect to avoid form resubmission
    header("Location: admin_reviews.php");
    exit;
}

// Fetch all reviews
$reviews_sql = "SELECT r.review_id, r.product_id, r.user_id, r.rating, r.review, r.created_at, p.name AS product_name, u.username AS user_name
                FROM reviews r
                JOIN products p ON r.product_id = p.product_id
                JOIN users u ON r.user_id = u.id";
$reviews_result = mysqli_query($conn, $reviews_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reviews</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Product Reviews</h2>
    
    <!-- Display Reviews -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Review ID</th>
                <th>Product Name</th>
                <th>User Name</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['review_id']); ?></td>
                    <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($review['rating']); ?></td>
                    <td><?php echo htmlspecialchars($review['review']); ?></td>
                    <td><?php echo htmlspecialchars($review['created_at']); ?></td>
                    <td>
                        <!-- Delete Review Form -->
                        <form method="post" action="admin_reviews.php" class="d-inline">
                            <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['review_id']); ?>">
                            <button type="submit" name="delete_review" class="btn btn-danger btn-sm">Delete</button>
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
