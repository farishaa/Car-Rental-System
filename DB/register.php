<?php
include 'home.php';

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "car_rental"; 

$connect = new mysqli($servername, $username, $password, $dbname);

if ($connect->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $psswd = password_hash($_POST['psswd'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $sql = "SELECT * FROM Users WHERE email = '$email'";

    $sendsql = mysqli_query($connect, $sql);
	$result = mysqli_fetch_assoc ($sendsql);
    
    if(!$result)
		{
			$sqlinsert = "INSERT INTO Users (name, psswd, email, role) VALUES ('$name', '$psswd', '$email', '$role')";	
			$sendsql = mysqli_query($connect, $sqlinsert);
			echo "<div class='message'>Registration successful. Redirecting to login...</div>";
            header('Refresh: 2; URL=login.php'); 
		}
		else
		{
			echo "<div class='message'>Invalid email or password.</div>";
		}
}

$connect->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>
<div class="container">
        <div class="box form-box">
            <header>Register</header>
            <form action="register.php" method="POST">
                <div class="field input">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="field input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="field input">
                    <label for="password">Password:</label>
                    <input type="password" id="psswd" name="psswd" required>
                </div>
                <div class="field input">
                    <label for="role">Role:</label>
                    <select name="role" id="role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button class="btn submit" type="submit" name="register">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
