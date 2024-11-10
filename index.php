<?php
session_start();

// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">

    <!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
    <link href="assets/demo/demo.css" rel="stylesheet" />

    <style>
        body {
            background-image: url('assets/img/reforestation-planting-trees-forest-man-child-plant-bare-tree-fir-silhouette-vector-illustration-216838316.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            min-height: 50px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            text-align: center;
        }

        .footer-nav ul {
            list-style: none;
            padding-left: 0;
        }

        .footer-nav ul li {
            display: inline;
            margin: 0 10px;
        }

        .footer-nav ul li a {
            color: #555;
            text-decoration: none;
        }

        .login-form label {
            font-weight: bold;
            margin-top: 10px;
        }

        .btn-block {
            margin-top: 20px;
        }

        .forgot {
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="content">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Log In</h4>
                <form class="login-form" action="" method="POST">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>

                    <?php if (!empty($error)): ?> 
                        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
                    <?php elseif (!empty($success)): ?> 
                        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-danger btn-block">Log In</button>
                </form>
                <div class="forgot text-center mt-3">
                    <a href="./register.php" class="btn btn-link">Register</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <nav class="footer-nav">
            <ul>
                <li><a href="#">Kalasan Team</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Licenses</a></li>
            </ul>
        </nav>
        <div class="credits ml-auto">
            <span class="copyright">
                © <script>document.write(new Date().getFullYear())</script>, Northern Bukidnon State College
            </span>
        </div>
    </footer>

    <!-- Core JS Files -->
    <script src="assets/js/core/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
</body>

</html>
