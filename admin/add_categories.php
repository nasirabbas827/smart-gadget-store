<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission to add a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {

    $category_name = $_POST['category_name'];

    // Insert category into database
    $insert_query = "INSERT INTO categories (name) VALUES ('$category_name')";
    
    if ($conn->query($insert_query) === TRUE) {
        $category_added = true;
    } else {
        $category_error = "Error adding category: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
  </head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <h3>Add Category</h3>
    <?php
    if (isset($category_added)) {
        echo "<p class='text-success'>Category added successfully!</p>";
    } elseif (isset($category_error)) {
        echo "<p class='text-danger'>$category_error</p>";
    }
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" class="form-control" id="category_name" name="category_name" required>
        </div>
        <button type="submit" class="btn btn-primary mr-4 ">Add Category</button>
    <a href="view_categories.php" class="btn btn-success">View Categories</a>

    </form>
  
   
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
