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

// Fetch booking details
$sql_rentals = "SELECT 
                         Rental.rent_id, 
                         Rental.date_start, 
                         Rental.date_end, 
                         Rental.amount, 
                         Rental.duration, 
                         Users.name AS customer_name, 
                         Cars.car_name, 
                         Cars.car_model 
                 FROM Rental
                 INNER JOIN Users ON Rental.user_id = Users.user_id
                 INNER JOIN Cars ON Rental.car_id = Cars.car_id
                 WHERE Cars.availability = ?";

$stmt = $connect->prepare($sql_rentals);
$availability = 'booked';
$stmt->bind_param("s", $availability); // Bind parameter
$stmt->execute();
$result_rentals = $stmt->get_result();

if ($result_rentals && $result_rentals->num_rows > 0) {
    $booking_details = $result_rentals->fetch_all(MYSQLI_ASSOC);
} else {
    $booking_details = [];
}


$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>User Details</title>
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

    <div class="profile-container">
        <h2>Admin Profile</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']); ?></p>
    </div>

    <?php if ($role === 'admin' && !empty($booking_details)): ?>
        <div class="booking-details">
            <h3>Customer Booking Details</h3>
            <table>
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Car Name</th>
                        <th>Model</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                        <th>Amount (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($booking_details as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['customer_name']); ?></td>
                            <td><?= htmlspecialchars($booking['car_name']); ?></td>
                            <td><?= htmlspecialchars($booking['car_model']); ?></td>
                            <td><?= htmlspecialchars($booking['date_start']); ?></td>
                            <td><?= htmlspecialchars($booking['date_end']); ?></td>
                            <td><?= htmlspecialchars($booking['duration']); ?> days</td>
                            <td><?= htmlspecialchars($booking['amount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No bookings found</p>
    <?php endif; ?>
</body>
</html>
