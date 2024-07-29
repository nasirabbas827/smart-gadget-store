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

// Fetch the product ID from the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    header("location: home.php");
    exit;
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$product_sql = "SELECT name FROM products WHERE product_id = ?";
$product_stmt = mysqli_prepare($conn, $product_sql);
mysqli_stmt_bind_param($product_stmt, "i", $product_id);
mysqli_stmt_execute($product_stmt);
$product_result = mysqli_stmt_get_result($product_stmt);
$product = mysqli_fetch_assoc($product_result);
mysqli_stmt_close($product_stmt);

if (!$product) {
    header("location: home.php");
    exit;
}

// Handle review posting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rating'])) {
    $rating = intval($_POST['rating']);
    $review_text = $_POST['review'];

    if ($rating >= 1 && $rating <= 5) {
        $review_sql = "INSERT INTO reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)";
        $review_stmt = mysqli_prepare($conn, $review_sql);
        mysqli_stmt_bind_param($review_stmt, "iiis", $user_id, $product_id, $rating, $review_text);
        mysqli_stmt_execute($review_stmt);
        mysqli_stmt_close($review_stmt);
        header("Location: product_reviews.php?product_id=" . $product_id);
        exit;
    }
}

// Fetch reviews for the product
$reviews_sql = "SELECT r.rating, r.review, r.created_at, u.username
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.product_id = ?";
$reviews_stmt = mysqli_prepare($conn, $reviews_sql);
mysqli_stmt_bind_param($reviews_stmt, "i", $product_id);
mysqli_stmt_execute($reviews_stmt);
$reviews_result = mysqli_stmt_get_result($reviews_stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Reviews</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center"><?php echo htmlspecialchars($product['name']); ?> Reviews</h2>
    
    <!-- Display Reviews -->
    <div class="list-group mb-4">
        <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
            <div class="list-group-item">
                <h5 class="mb-1"><?php echo htmlspecialchars($review['username']); ?></h5>
                <p class="mb-1"><strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                <p><?php echo htmlspecialchars($review['review']); ?></p>
                <small class="text-muted">Posted on <?php echo htmlspecialchars($review['created_at']); ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Post a Review -->
    <h4 class="text-center">Post a Review</h4>
    <form method="post" action="product_reviews.php?product_id=<?php echo $product_id; ?>">
        <div class="form-group">
            <label for="rating">Rating (1 to 5):</label>
            <input type="number" id="rating" name="rating" class="form-control" min="1" max="5" required>
        </div>
        <div class="form-group">
            <label for="review">Review:</label>
            <textarea id="review" name="review" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
