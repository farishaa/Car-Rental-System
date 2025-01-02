<?php
    session_start();
    include 'home.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if (!isset($_POST['car_id'])) {
        die("Car ID not provided.");
    }

    $car_id = $_POST['car_id'];
    $sql_car = "SELECT * FROM Cars WHERE car_id = '$car_id'";
    $result_car = $connect->query($sql_car);

    if ($result_car->num_rows === 0) {
        die("Car not found.");
    }

    $car = $result_car->fetch_assoc();
    $connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Car</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="booking-container">
        <h2>Booking Details</h2>
        <p><strong>Car Name:</strong> <?= htmlspecialchars($car['car_name']); ?></p>
        <p><strong>Model:</strong> <?= htmlspecialchars($car['car_model']); ?></p>
        <p><strong>Price per Day (RM):</strong> <?= htmlspecialchars($car['rental_price']); ?></p>

        <form action="process_booking.php" method="post">
            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['car_id']); ?>">
            <input type="hidden" name="price_per_day" value="<?= htmlspecialchars($car['rental_price']); ?>">
            <label for="date_start">Start Date:</label>
            <input type="date" id="date_start" name="date_start" required>
            <label for="date_end">End Date:</label>
            <input type="date" id="date_end" name="date_end" required>
            <button type="submit">Confirm Booking</button>
        </form>
    </div>
</body>
</html>
