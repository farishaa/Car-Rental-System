<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'home.php';

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM Users WHERE user_id = '$user_id'";
$result_user = $connect->query($sql_user);

if (!$result_user || $result_user->num_rows === 0) {
    die("Error fetching user data: " . $connect->error);
}

$user = $result_user->fetch_assoc();
$role = $user['role'];

$sql_cars = "SELECT * FROM Cars";
$result_cars = $connect->query($sql_cars);

if ($role !== 'admin') {
    header("Location: homepage.php");
    exit();
}

//form submission to add car
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_name = isset($_POST['car_name']) ? $connect->real_escape_string($_POST['car_name']) : '';
    $car_model = isset($_POST['car_model']) ? $connect->real_escape_string($_POST['car_model']) : '';
    $rental_price = isset($_POST['rental_price']) ? $connect->real_escape_string($_POST['rental_price']) : '';
    if (!empty($car_name) && !empty($car_model) && !empty($rental_price)) {
        $availability = 'available';

        $sql_insert = "INSERT INTO Cars (car_name, car_model, rental_price, availability) 
                       VALUES ('$car_name', '$car_model', '$rental_price', '$availability')";

        if ($connect->query($sql_insert) === TRUE) {
            $success_message = "Car successfully added!";
        } else {
            $error_message = "Error adding car: " . $connect->error;
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_car_id'])) {
    $delete_car_id = (int) $_POST['delete_car_id'];

    $sql_fetch_car = "SELECT * FROM Cars WHERE car_id = ?";
    $stmt = $connect->prepare($sql_fetch_car);
    $stmt->bind_param("i", $delete_car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    if ($car) {
        // delete the car
        $sql_delete_car = "DELETE FROM Cars WHERE car_id = ?";
        $stmt = $connect->prepare($sql_delete_car);
        $stmt->bind_param("i", $delete_car_id);

        if ($stmt->execute()) {
            $success_message = "Car successfully deleted.";

            // Log admin action
            $user_id = $_SESSION['user_id'];
            $action_type = "Delete Car";
            $action_details = "Deleted car: " . $car['car_name'] . " (ID: " . $car['car_id'] . ")";
            $sql_audit = "INSERT INTO AdminAuditLogs (user_id, action_type, action_details) VALUES (?, ?, ?)";
            $stmt = $connect->prepare($sql_audit);
            $stmt->bind_param("iss", $user_id, $action_type, $action_details);
            $stmt->execute();
        } else {
            $error_message = "Error deleting car: " . $connect->error;
        }
    } else {
        $error_message = "Car not found.";
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
        <form action="update_car.php" method="get">
            <button type="submit" class="top-btn">Update Car</button>
        </form>
        <form action="user_account.php" method="get">
            <button type="submit" class="top-btn">Profile Account</button>
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
    <div class="car-list">
    <h2>Available Cars</h2>
    <table>
        <thead>
            <tr>
                
                <th>Car Name</th>
                <th>Car Model</th>
                <th>Rental Price (RM)</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($car = $result_cars->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($car['car_name']); ?></td>
                    <td><?= htmlspecialchars($car['car_model']); ?></td>
                    <td><?= number_format($car['rental_price'], 2); ?></td>
                    <td><?= htmlspecialchars($car['availability']); ?></td>
                    <td>
                    <form action="" method="post" style="display: inline;">
                        <input type="hidden" name="delete_car_id" value="<?= $car['car_id']; ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this car?');">Delete</button>
                    </form>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
