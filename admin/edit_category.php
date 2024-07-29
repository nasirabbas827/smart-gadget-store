<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    $category_id = $_POST['category_id'];
    $new_category_name = $_POST['category_name'];

    // Update category in the database
    $update_query = "UPDATE categories SET name = '$new_category_name' WHERE id = $category_id";

    if ($conn->query($update_query) === TRUE) {
        $category_updated = true;
    } else {
        $category_update_error = "Error updating category: " . $conn->error;
    }
}

// Fetch category details based on ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $category_id = $_GET['id'];
    
    $select_query = "SELECT id, name FROM categories WHERE id = $category_id";
    $result = $conn->query($select_query);

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        $category_not_found = true;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <h3>Edit Category</h3>
    <?php
    if (isset($category_updated)) {
        echo "<p class='text-success'>Category updated successfully!</p>";
    } elseif (isset($category_update_error)) {
        echo "<p class='text-danger'>$category_update_error</p>";
    } elseif (isset($category_not_found)) {
        echo "<p class='text-danger'>Category not found.</p>";
    }
    ?>

    <?php if (isset($category)) : ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo $category['name']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mr-3">Update Category</button>
    <a href="view_categories.php" class="btn btn-success">Back to Manage Categories</a><br>

        </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
