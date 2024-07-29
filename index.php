<?php
include('config.php');


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


?>
<!DOCTYPE html>
<html>
<head>
    <title>Online Smart Gadget Store</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="jumbotron text-center">
    <h1>Welcome to Online Smart Gadget Store</h1>
    <p>Discover the Latest Gadgets and Technology</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Explore</a>
</div>

<div class="container mt-5">
    <h2 class="text-center">Order Tracking</h2>
    <form method="get" action="" class="form-inline justify-content-center">
        <div class="form-group mx-sm-3 mb-2">
            <label for="order_id" class="sr-only">Order ID</label>
            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Enter your Order ID" required>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Track Order</button>
    </form>

    <?php
    // Include config file
    include('config.php');

    // Fetch order details if order_id is provided
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $order = null;

    if ($order_id > 0) {
        $sqlorder = "SELECT order_id, order_date, full_name, phone, address, payment_method, order_status, total_price 
                FROM orders 
                WHERE order_id = ?";
        $stmt = mysqli_prepare($conn, $sqlorder);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $resultorder = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($resultorder);
        mysqli_stmt_close($stmt);
    }
    ?>

    <?php if ($order): ?>
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h5>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
                <p><strong>Total Price:</strong> $<?php echo htmlspecialchars($order['total_price']); ?></p>
            </div>
        </div>
    <?php elseif ($order_id > 0): ?>
        <p class="text-center mt-4">No order found with the provided ID.</p>
    <?php endif; ?>
</div>
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
<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 Online Smart Gadget Store. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
