<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle category deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $category_id = $_GET['delete'];

    // Delete category from the database
    $delete_query = "DELETE FROM categories WHERE id = $category_id";

    if ($conn->query($delete_query) === TRUE) {
        $category_deleted = true;
    } else {
        $category_delete_error = "Error deleting category: " . $conn->error;
    }
}

// Fetch all categories from the database
$select_query = "SELECT id, name FROM categories";
$result = $conn->query($select_query);
$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="../css/style.css">
    
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <h3>Manage Categories</h3>
    <?php
    if (isset($category_deleted)) {
        echo "<p class='text-success'>Category deleted successfully!</p>";
    } elseif (isset($category_delete_error)) {
        echo "<p class='text-danger'>$category_delete_error</p>";
    }
    ?>
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category) : ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo $category['name']; ?></td>
                        <td><a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn btn-primary">Edit</a></td>
                        <td>
                            <a href="?delete=<?php echo $category['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
