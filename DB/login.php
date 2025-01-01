<?php
include 'home.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $psswd = $_POST['psswd'];

    $sql = "SELECT * FROM Users WHERE email = '$email'";
    $result = $connect->query($sql);
    

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($psswd, $user['psswd'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            echo "<div class='message'>Login successful. Redirecting to homepage...</div>";
            header('Refresh: 2; URL=homepage.php'); 
        } else {
            echo "<div class='message'>Invalid email or password.</div>";
        }
    } else {
        echo "<div class='message'>Invalid email or password.</div>";
    }
}

$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
<div class="container">
        <div class="box form-box">
            <header>Login</header>
            <form action="login.php" method="POST">
                <div class="field input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="field input">
                    <label for="password">Password:</label>
                    <input type="password" id="psswd" name="psswd" required>
                </div>
                <button class="btn submit" type="submit" name="login">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
