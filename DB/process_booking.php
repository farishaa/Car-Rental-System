<?php
session_start();
include 'home.php';

$response = null;
$bookingDetails = null;

function logAudit($user_id, $action, $description) {
    global $connect;
    $sql_audit = "INSERT INTO AuditLogs (user_id, action, description) VALUES (?, ?, ?)";
    $stmt_audit = $connect->prepare($sql_audit);
    $stmt_audit->bind_param("iss", $user_id, $action, $description);
    $stmt_audit->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date_start'], $_POST['date_end'], $_POST['car_id'])) {
    $car_id = $_POST['car_id'];
    $date_start = $_POST['date_start'];
    $date_end = $_POST['date_end'];
    $user_id = $_SESSION['user_id'];

    $date1 = new DateTime($date_start);
    $date2 = new DateTime($date_end);
    $interval = $date1->diff($date2);
    $duration = $interval->days;

    if ($duration <= 0) {
        $response = "Invalid booking duration.";
        logAudit($user_id, "Booking Failed", "User ID: $user_id tried to book with invalid duration (Start: $date_start, End: $date_end).");
    } else {
        // fetch car price
        $sql_car = "SELECT car_name, car_model, rental_price FROM Cars WHERE car_id = ?";
        $stmt = $connect->prepare($sql_car);
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $result_car = $stmt->get_result();

        if ($result_car->num_rows === 0) {
            $response = "Car not found.";
            logAudit($user_id, "Booking Failed", "User ID: $user_id tried to book a non-existent car (Car ID: $car_id).");
        } else {
            $car = $result_car->fetch_assoc();
            $rental_price = $car['rental_price'];
            $amount = $rental_price * $duration;

            // insert data to sql
            $sql_rental = "INSERT INTO Rental (user_id, car_id, date_start, date_end, amount, duration)
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($sql_rental);
            $stmt->bind_param("iissdi", $user_id, $car_id, $date_start, $date_end, $amount, $duration);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $sql_update_car = "UPDATE Cars SET availability = 'booked' WHERE car_id = ?";
                $stmt = $connect->prepare($sql_update_car);
                $stmt->bind_param("i", $car_id);
                $stmt->execute();

                $response = "Booking Confirmed! Total Amount: RM " . number_format($amount, 2);
                $bookingDetails = [
                    'car_name' => $car['car_name'],
                    'model' => $car['car_model'],
                    'rental_price' => $rental_price,
                    'amount' => $amount,
                    'duration' => $duration,
                    'date_start' => $date_start,
                    'date_end' => $date_end
                ];

                // Log the successful booking
                logAudit($user_id, "Booking Created", "User ID: $user_id booked car '" . $car['car_name'] . "' (Model: " . $car['car_model'] . ") from $date_start to $date_end for RM " . number_format($amount, 2));

            } else {
                $response = "Booking failed. Please try again.";
                logAudit($user_id, "Booking Failed", "User ID: $user_id failed to create a booking for car '" . $car['car_name'] . "' (Model: " . $car['car_model'] . ").");
            }
            $stmt->close();
        }
    }
        // Car modifications status
        if ($_SESSION['role'] === 'admin') {
        $user_id = $_SESSION['user_id'];
        $action_type = "Modify Car";
        $action_details = "Updated car availability to 'booked' for car_id: $car_id";
    
        $sql_audit = "INSERT INTO AdminAuditLogs (user_id, action_type, action_details) VALUES (?, ?, ?)";
        $stmt = $connect->prepare($sql_audit);
        $stmt->bind_param("iss", $user_id, $action_type, $action_details);
        $stmt->execute();
    }
        // Booking modification
        if ($_SESSION['role'] === 'admin') {
        $user_id = $_SESSION['user_id'];
        $action_type = "Modify Booking";
        $action_details = "Updated booking for rent_id: $rent_id";
    
        $sql_audit = "INSERT INTO AdminAuditLogs (user_id, action_type, action_details) VALUES (?, ?, ?)";
        $stmt = $connect->prepare($sql_audit);
        $stmt->bind_param("iss", $user_id, $action_type, $action_details);
        $stmt->execute();
    }
    
}

$sql_cars = "SELECT * FROM Cars WHERE availability = 'available'";
$result_cars = $connect->query($sql_cars);
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Book a Car</title>
</head>
    <body>
    <div class="top-nav">
        <form action="homepage.php" method="get">
            <button type="submit" class="top-btn">Dashboard</button>
        </form>
    </div>

        <!-- booking confirmation -->
        <?php if ($response): ?>
            <div class="booking-result">
                <h3><?= htmlspecialchars($response); ?></h3>
                <?php if ($bookingDetails): ?>
                    <p><strong>Car Name:</strong> <?= htmlspecialchars($bookingDetails['car_name']); ?></p>
                    <p><strong>Model:</strong> <?= htmlspecialchars($bookingDetails['model']); ?></p>
                    <p><strong>Rental Price per Day (RM):</strong> <?= number_format($bookingDetails['rental_price'], 2); ?></p>
                    <p><strong>Start Date:</strong> <?= htmlspecialchars($bookingDetails['date_start']); ?></p>
                    <p><strong>End Date:</strong> <?= htmlspecialchars($bookingDetails['date_end']); ?></p>
                    <p><strong>Total Amount (RM):</strong> <?= number_format($bookingDetails['amount'], 2); ?></p>
                    <p><strong>Duration:</strong> <?= htmlspecialchars($bookingDetails['duration']); ?> days</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </body>
</html>
