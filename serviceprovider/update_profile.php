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

// Fetch user details from the database
$sql = "SELECT id, username, email, age, full_name, certifications, experience, usertype, expertise, previous_work FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fetched_id, $username, $email, $age, $full_name, $certifications, $experience, $usertype, $expertise, $previous_work);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Update user profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST["username"];
    $newEmail = $_POST["email"];
    $newAge = $_POST["age"];
    $newFullName = $_POST["full_name"];
    $newCertifications = $_POST["certifications"];
    $newExperience = $_POST["experience"];
    $newExpertise = $_POST["expertise"];
    $newPreviousWork = $_POST["previous_work"];

    // Update user details in the database
    $update_query = "UPDATE users 
                     SET username = ?, email = ?, age = ?, full_name = ?, certifications = ?, experience = ?, expertise = ?, previous_work = ? 
                     WHERE id = ?";
    
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssisssssi", $newUsername, $newEmail, $newAge, $newFullName, $newCertifications, $newExperience, $newExpertise, $newPreviousWork, $user_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo "Profile updated successfully!";
        // Update session data if needed
        $_SESSION["username"] = $newUsername;
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }

    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Update Profile</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" required>
        </div>
        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" class="form-control" name="age" value="<?php echo $age; ?>" required>
        </div>

        <?php if ($usertype == 'service_provider'): ?>
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" class="form-control" name="full_name" value="<?php echo $full_name; ?>" required>
            </div>
            <div class="form-group">
                <label for="certifications">Certifications:</label>
                <textarea class="form-control" name="certifications" required><?php echo $certifications; ?></textarea>
            </div>
            <div class="form-group">
                <label for="experience">Experience:</label>
                <textarea class="form-control" name="experience" required><?php echo $experience; ?></textarea>
            </div>
            <div class="form-group">
                <label for="expertise">Expertise:</label>
                <textarea class="form-control" name="expertise" required><?php echo $expertise; ?></textarea>
            </div>
            <div class="form-group">
                <label for="previous_work">Previous Work:</label>
                <textarea class="form-control" name="previous_work" required><?php echo $previous_work; ?></textarea>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
