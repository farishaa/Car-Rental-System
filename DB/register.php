<?php
include 'home.php';


if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    //input sanitization, remove whitespace
    $name = htmlspecialchars(trim($_POST['name'])); 
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL); 
    $psswd = trim($_POST['psswd']); 
    $role = htmlspecialchars(trim($_POST['role']));
    
    // Input validation
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        echo("<div class='message'>Invalid name format!</div>");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo("<div class='message'>Invalid email format!</div>");
        exit;
    }

    //email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='message'>Invalid email format.</div>";
        exit;
    }

    //strong password
    $password_error = "";
    if (strlen($psswd) < 8) {
        $password_error .= "Password must be at least 8 characters long, ";
    }
    if (!preg_match("/[A-Z]/", $psswd)) {
        $password_error .= "Password must include at least one uppercase letter, ";
    }
    if (!preg_match("/[a-z]/", $psswd)) {
        $password_error .= "one lowercase letter, ";
    }
    if (!preg_match("/[0-9]/", $psswd)) {
        $password_error .= "one number, ";
    }
    if (!preg_match("/[#.*.^]/", $psswd)) {
        $password_error .= "and one special character.";
    }

    if ($password_error) {
        echo "<div class='message' style='color: red; text-align: center;'>$password_error</div>";
        exit;
    }

    $psswd = password_hash($psswd, PASSWORD_BCRYPT);

    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) { 
        $sqlinsert = "INSERT INTO Users (name, email, psswd, role) VALUES (?, ?, ?, ?)";
        $stmtinsert = $connect->prepare($sqlinsert);
        $stmtinsert->bind_param("ssss", $name, $email, $psswd, $role);

        if ($stmtinsert->execute()) {
            echo "<div class='message'>Registration successful. Redirecting to login...</div>";
            header('Refresh: 2; URL=login.php'); 
        } else {
            echo "<div class='message' style='color: red; text-align: center;'>Error during registration.</div>";
        }
    } else {
        echo "<div class='message' style='color: red; text-align: center;'>Email already exists.</div>";
    }
}

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
                        <option value="dba">Database Admin</option> <!--DBA-->
                    </select>
                </div>
                <button class="btn submit" type="submit" name="register">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
