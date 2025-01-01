<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'car_rental'); // Make sure the DB name is correct

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all users who have passwords hashed with SHA2()
$sql = "SELECT user_id, psswd FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through all users and rehash the password
    while ($user = $result->fetch_assoc()) {
        // Rehash the existing SHA2 password using password_hash()
        $hashed_password = password_hash($user['psswd'], PASSWORD_DEFAULT);

        // Update the password in the database
        $update_sql = "UPDATE users SET psswd = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('si', $hashed_password, $user['user_id']);
        $stmt->execute();
    }

    echo "Passwords have been successfully rehashed!";
} else {
    echo "No users found in the database.";
}

$conn->close();
?>
