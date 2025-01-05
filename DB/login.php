<?php
include 'home.php';

session_start();

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
}

$max_attempts = 3; // Number of allowed attempts
$lockout_duration = 300; // Lockout duration in seconds (5 minutes)

$current_time = time();
if ($_SESSION['login_attempts'] >= $max_attempts && $current_time < $_SESSION['lockout_time']) {
    echo "<div class='message'>Too many login attempts. Please try again after " . 
         ceil(($_SESSION['lockout_time'] - $current_time) / 60) . " minutes.</div>";
    exit;
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    //Input sanitization, remove whitespace
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $psswd = trim($_POST['psswd']);

    //Input validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='message'>Invalid email format.</div>";
        exit;
    }

    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($psswd, $user['psswd'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            echo "<div class='message'>Login successful. Redirecting to homepage...</div>";
            header('Refresh: 2; URL=homepage.php');

            // Log successful login for admins
            if ($user['role'] === 'admin') {
                $user_id = $user['user_id'];
                $action_type = "Login";
                $action_details = "Admin logged in successfully.";

                $sql_audit = "INSERT INTO AdminAuditLogs (user_id, action_type, action_details) VALUES (?, ?, ?)";
                $stmt = $connect->prepare($sql_audit);
                $stmt->bind_param("iss", $user_id, $action_type, $action_details);
                $stmt->execute();
            }
        }
        else {
            // Log unsuccessful admin login attempt
            if ($user['role'] === 'admin') {
                $action_type = "Unsuccessful Login";
                $action_details = "Invalid password attempt for admin email: " . $email;
            
                $user_id = $user['user_id'] ?? null;
            
                $sql_audit = "INSERT INTO AdminAuditLogs (user_id, action_type, action_details) VALUES (?, ?, ?)";
                $stmt = $connect->prepare($sql_audit);
                $stmt->bind_param("iss", $user_id, $action_type, $action_details);
                $stmt->execute();
            }

            echo "<div class='message'>Invalid email or password.</div>";
        }
    } 
    else {
        // Log unsuccessful admin login attempt (for emails that might belong to admins)
        $sql_check_admin = "SELECT * FROM Users WHERE email = ? AND role = 'admin'";
        $stmt = $connect->prepare($sql_check_admin);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_check_admin = $stmt->get_result();

        if ($result_check_admin->num_rows > 0) {
            $action_type = "Unsuccessful Login";
            $action_details = "Invalid email or password attempt for admin email: " . $email;

            $sql_audit = "INSERT INTO AdminAuditLogs (user_id, action_type, action_details) VALUES (NULL, ?, ?)";
            $stmt = $connect->prepare($sql_audit);
            $stmt->bind_param("ss", $action_type, $action_details);
            $stmt->execute();
        }

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
