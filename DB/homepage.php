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


    // fetch data
    if ($role == 'admin') {
        $sql_cars = "SELECT * FROM Cars";
    } else {
        $sql_cars = "SELECT * FROM Cars WHERE availability = 'available'";
    }
    $result_cars = $connect->query($sql_cars);

    $connect->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <link rel="stylesheet" href="style.css">
        <title>Homepage</title>
    </head>
    <body>
        <div class="top-nav">

            <?php if ($role == 'admin'): ?>
                <form action="update_car.php" method="get">
                    <button type="submit" class="top-btn">Update Car</button>
                </form>
                <form action="user_account.php" method="get">
                    <button type="submit" class="top-btn">Profile Account</button>
                </form>
            <?php endif; ?>

            <?php if ($role == 'dba'): ?>
                <!-- DBA Backup and Restore buttons -->
                <form action="backup.php" method="get">
                    <button type="submit" class="top-btn">Backup Data</button>
                </form>
                <form action="restore.php" method="get">
                    <button type="submit" class="top-btn">Restore Data</button>
                </form>
                <?php endif; ?>

            <form action="logout.php" method="get">
                <button type="submit" class="top-btn">Logout</button>
            </form>
        </div>

        <?php if ($role == 'dba'): ?>
            <!-- DBA page content -->
            <div class="dba-welcome">
                <h2>Welcome to DBA Page!</h2>
            </div>
        <?php else: ?>  

    
            <div class="cars-list">
                <h2>Car lists</h2>

                <?php if ($role == 'admin'): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Car Name</th>
                                <th>Model</th>
                                <th>Price per day (RM)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($car = $result_cars->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($car['car_name']); ?></td>
                                    <td><?php echo htmlspecialchars($car['car_model']); ?></td>
                                    <td><?php echo htmlspecialchars($car['rental_price']); ?></td>
                                    <td><?php echo htmlspecialchars($car['availability']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Car Name</th>
                                <th>Model</th>
                                <th>Price per day (RM)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($car = $result_cars->fetch_assoc()): ?>
                                <?php if ($car['availability'] == 'available'): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($car['car_name']); ?></td>
                                        <td><?php echo htmlspecialchars($car['car_model']); ?></td>
                                        <td><?php echo htmlspecialchars($car['rental_price']); ?></td>
                                        <td>
                                            <form action="book_car.php" method="post">
                                                <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['car_id']); ?>">
                                                <button type="submit" class="book-btn">Book</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

    </body>
    
</html>
