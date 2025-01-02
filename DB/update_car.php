<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'home.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM Users WHERE user_id = '$user_id'";
$result_user = $connect->query($sql_user);

if (!$result_user || $result_user->num_rows === 0) {
    die("Error fetching user data: " . $connect->error);
}

$user = $result_user->fetch_assoc();
$role = $user['role'];

// Redirect if not admin
if ($role !== 'admin') {
    header("Location: homepage.php");
    exit();
}

// Handle form submission to add car
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_name = $connect->real_escape_string($_POST['car_name']);
    $car_model = $connect->real_escape_string($_POST['car_model']);
    $rental_price = $connect->real_escape_string($_POST['rental_price']);
    $availability = 'available';

    $sql_insert = "INSERT INTO Cars (car_name, car_model, rental_price, availability) 
                   VALUES ('$car_name', '$car_model', '$rental_price', '$availability')";

    if ($connect->query($sql_insert) === TRUE) {
        $success_message = "Car successfully added!";
    } else {
        $error_message = "Error adding car: " . $connect->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Add Car</title>
</head>
<body>
    <div class="top-nav">
        <form action="homepage.php" method="get">
            <button type="submit" class="top-btn">Dashboard</button>
        </form>
        <form action="logout.php" method="get">
            <button type="submit" class="top-btn">Logout</button>
        </form>
    </div>

    <div class="update-cars-form">
        <h2>Car Details</h2>
        <?php if (isset($success_message)): ?>
            <p class="success-message"><?= htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <label for="car_name">Car Name:</label>
            <input type="text" id="car_name" name="car_name" required>

            <label for="car_model">Car Model:</label>
            <input type="text" id="car_model" name="car_model" required>

            <label for="rental_price">Price per day (RM):</label>
            <input type="number" id="rental_price" name="rental_price" step="10" required>

            <button type="submit" class="submit-btn">Add Car</button>
        </form>
    </div>
</body>
</html>
