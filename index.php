<?php
// index.php

include 'db_connection.php';

session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="img/tree-logo.png" alt="Tree Logo">
        </div>
        <form method="POST" action="" class="login-form">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Admin" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePassword()">&#128065;</span> <!-- Eye icon -->
                </div>
            </div>
            <button type="submit" class="login-button">Log In</button>
            <div class="help-links">
                <a href="#">Forgot password?</a>
            </div>
            <div class="help-links">
              <a href="register.php">Create an account</a>
            </div>
            <a href="#" class="get-help">Get Help</a>
        </form>

        <?php
        if ($error) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>
    </div>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = '&#128065;'; // Eye icon for hidden password
            }
        }
    </script>
</body>
</html>
