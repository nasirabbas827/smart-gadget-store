<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle delete product
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Product deleted successfully!";
    } else {
        echo "Error deleting product: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Fetch all products
$sql = "SELECT p.product_id, p.name, c.name as category, p.description, p.price, p.stock_quantity, p.image_url
        FROM products p
        JOIN categories c ON p.category_id = c.id";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin View Products</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">View and Manage Products</h2>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock Quantity</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                    <td><img src="<?php echo $row['image_url']; ?>" alt="Product Image" width="50"></td>
                    <td>
                        <a href="edit_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="view_products.php?delete_id=<?php echo $row['product_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
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
