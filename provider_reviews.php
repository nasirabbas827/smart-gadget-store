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

// Handle review posting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['provider_id'])) {
    $provider_id = intval($_POST['provider_id']);
    $rating = intval($_POST['rating']);
    $review_text = $_POST['review'];

    if ($rating >= 1 && $rating <= 5) {
        $review_sql = "INSERT INTO provider_reviews (user_id, provider_id, rating, review) VALUES (?, ?, ?, ?)";
        $review_stmt = mysqli_prepare($conn, $review_sql);
        mysqli_stmt_bind_param($review_stmt, "iiis", $user_id, $provider_id, $rating, $review_text);
        mysqli_stmt_execute($review_stmt);
        mysqli_stmt_close($review_stmt);
        header("Location: home.php");
exit;
    }
}

// Fetch reviews for a specific provider
$reviews = [];
if (isset($_GET['provider_id'])) {
    $provider_id = intval($_GET['provider_id']);
    $reviews_sql = "SELECT r.rating, r.review, r.created_at, u.username
                    FROM provider_reviews r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.provider_id = ?";
    $reviews_stmt = mysqli_prepare($conn, $reviews_sql);
    mysqli_stmt_bind_param($reviews_stmt, "i", $provider_id);
    mysqli_stmt_execute($reviews_stmt);
    $reviews_result = mysqli_stmt_get_result($reviews_stmt);
    while ($review = mysqli_fetch_assoc($reviews_result)) {
        $reviews[] = $review;
    }
    mysqli_stmt_close($reviews_stmt);
}

// Fetch provider details
$provider = [];
if (isset($_GET['provider_id'])) {
    $provider_id = intval($_GET['provider_id']);
    $provider_sql = "SELECT full_name FROM users WHERE id = ? AND usertype = 'service_provider'";
    $provider_stmt = mysqli_prepare($conn, $provider_sql);
    mysqli_stmt_bind_param($provider_stmt, "i", $provider_id);
    mysqli_stmt_execute($provider_stmt);
    $provider_result = mysqli_stmt_get_result($provider_stmt);
    $provider = mysqli_fetch_assoc($provider_result);
    mysqli_stmt_close($provider_stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Provider Reviews</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Reviews for <?php echo htmlspecialchars($provider['full_name']); ?></h2>

    <!-- Review Posting Form -->
    <form method="post" action="provider_reviews.php" class="mb-4">
        <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($_GET['provider_id']); ?>">
        <div class="form-group">
            <label for="rating">Rating (1 to 5):</label>
            <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
        </div>
        <div class="form-group">
            <label for="review">Review:</label>
            <textarea class="form-control" id="review" name="review" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>

    <!-- Display Reviews -->
    <h3>Customer Reviews</h3>
    <?php if (empty($reviews)): ?>
        <p>No reviews yet.</p>
    <?php else: ?>
        <div class="list-group mb-4">
        <?php foreach ($reviews as $review): ?>
            <div class="list-group-item m-2">
                <h5><?php echo htmlspecialchars($review['username']); ?></h5>
                <p>Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                <p><?php echo htmlspecialchars($review['review']); ?></p>
                <p><small>Reviewed on: <?php echo htmlspecialchars($review['created_at']); ?></small></p>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
