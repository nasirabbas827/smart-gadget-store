<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch product details
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST["product_id"];
    $name = $_POST["name"];
    $category_id = $_POST["category_id"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $stock_quantity = $_POST["stock_quantity"];
    $image_url = $_POST["existing_image_url"];

    // Handle file upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is a valid image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false && in_array($imageFileType, ["jpg", "jpeg", "png"])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image or invalid format.";
        }
    }

    // Update product details in the database
    $sql = "UPDATE products SET name = ?, category_id = ?, description = ?, price = ?, stock_quantity = ?, image_url = ? WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sisdssi", $name, $category_id, $description, $price, $stock_quantity, $image_url, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Product updated successfully!";
        header("Location: view_products.php");
        exit;
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Fetch categories for the dropdown
$categories_result = mysqli_query($conn, "SELECT id, name FROM categories");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2 class="text-center">Edit Product</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
        <div class="form-group">
            <label for="name">Product Name:</label>
            <input type="text" class="form-control" name="name" value="<?php echo $product['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category:</label>
            <select class="form-control" name="category_id" required>
                <?php while ($row = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $product['category_id']) echo 'selected'; ?>><?php echo $row['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" name="description" required><?php echo $product['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $product['price']; ?>" required>
        </div>
        <div class="form-group">
            <label for="stock_quantity">Stock Quantity:</label>
            <input type="number" class="form-control" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required>
        </div>
        <div class="form-group">
            <label for="existing_image_url">Existing Image:</label>
            <img src="<?php echo $product['image_url']; ?>" alt="Product Image" width="100">
            <input type="hidden" name="existing_image_url" value="<?php echo $product['image_url']; ?>">
        </div>
        <div class="form-group">
            <label for="image">New Image (optional):</label>
            <input type="file" class="form-control-file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
