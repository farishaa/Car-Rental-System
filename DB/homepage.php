<?php
include 'home.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: register.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM Users WHERE user_id = '$user_id'";
$result = $connect->query($sql);
$user = $result->fetch_assoc();

$role = $user['role'];

$sql = "SELECT * FROM Cars WHERE availability = 'available'";
$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>
    <h2>Welcome, <?php echo $user['name']; ?></h2>
    <h3>Available Cars:</h3>
    <table>
        <tr>
            <th>Car Name</th>
            <th>Model</th>
            <th>Rental Price</th>
            <th>Availability</th>
        </tr>
        <?php while($car = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $car['car_name']; ?></td>
                <td><?php echo $car['car_model']; ?></td>
                <td><?php echo $car['rental_price']; ?></td>
                <td><?php echo $car['availability']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <?php if ($role == 'admin') { ?>
        <h3>Admin: Update Car Availability</h3>
        <form action="update_car.php" method="POST">
            <label for="car_id">Car ID:</label><br>
            <input type="number" name="car_id" required><br><br>
            <label for="availability">Availability:</label><br>
            <select name="availability" required>
                <option value="available">Available</option>
                <option value="booked">Booked</option>
            </select><br><br>
            <button type="submit">Update Availability</button>
        </form>
    <?php } ?>
</body>
</html>
