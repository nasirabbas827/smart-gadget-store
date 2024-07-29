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

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch products
$sql = "SELECT p.product_id, p.name, c.name as category, p.description, p.price, p.stock_quantity, p.image_url
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.name LIKE ? OR c.name LIKE ?";
$stmt = mysqli_prepare($conn, $sql);
$search_param = "%" . $search_query . "%";
mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch service providers
$providers_sql = "SELECT id, full_name, age, certifications, experience, expertise, phone, email
                  FROM users
                  WHERE usertype = 'service_provider'";
$providers_result = mysqli_query($conn, $providers_sql);

// Handle review posting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review_text = $_POST['review'];

    if ($rating >= 1 && $rating <= 5) {
        $review_sql = "INSERT INTO reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)";
        $review_stmt = mysqli_prepare($conn, $review_sql);
        mysqli_stmt_bind_param($review_stmt, "iiis", $user_id, $product_id, $rating, $review_text);
        mysqli_stmt_execute($review_stmt);
        mysqli_stmt_close($review_stmt);
    }
}

// Fetch reviews for a specific product
$reviews = [];
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $reviews_sql = "SELECT r.rating, r.review, r.created_at, u.username
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.product_id = ?";
    $reviews_stmt = mysqli_prepare($conn, $reviews_sql);
    mysqli_stmt_bind_param($reviews_stmt, "i", $product_id);
    mysqli_stmt_execute($reviews_stmt);
    $reviews_result = mysqli_stmt_get_result($reviews_stmt);
    while ($review = mysqli_fetch_assoc($reviews_result)) {
        $reviews[] = $review;
    }
    mysqli_stmt_close($reviews_stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Products</h2>
    
    <!-- Search Bar -->
    <form method="get" action="home.php" class="form-inline my-4">
        <input class="form-control mr-sm-2" type="search" placeholder="Search by name or category" aria-label="Search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    
    <!-- Display Products -->
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="admin/<?php echo $row['image_url']; ?>" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                        <p class="card-text"><strong>Category:</strong> <?php echo $row['category']; ?></p>
                        <p class="card-text"><strong>Price:</strong> $<?php echo $row['price']; ?></p>
                        <a href="add_to_cart.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-primary">Add to Cart</a>
                        <a href="product_reviews.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-secondary mt-2">Reviews</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Display Service Providers -->
    <h2 class="text-center mt-5">Service Providers</h2>
    <div class="row">
        <?php while ($provider = mysqli_fetch_assoc($providers_result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($provider['full_name']); ?></h5>
                        <p class="card-text"><strong>Age:</strong> <?php echo htmlspecialchars($provider['age']); ?></p>
                        <p class="card-text"><strong>Certifications:</strong> <?php echo htmlspecialchars($provider['certifications']); ?></p>
                        <p class="card-text"><strong>Experience:</strong> <?php echo htmlspecialchars($provider['experience']); ?></p>
                        <p class="card-text"><strong>Expertise:</strong> <?php echo htmlspecialchars($provider['expertise']); ?></p>
                        <p class="card-text"><strong>Phone:</strong> <?php echo htmlspecialchars($provider['phone']); ?></p>
                        <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($provider['email']); ?></p>
                        <a href="make_appointment.php?provider_id=<?php echo $provider['id']; ?>" class="btn btn-primary">Make Appointment</a>
                        <a href="provider_reviews.php?provider_id=<?php echo $provider['id']; ?>" class="btn btn-secondary mt-2">Reviews</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
