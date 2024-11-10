<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $created_at = $_POST['created_at'];
   
    // Validate that passwords match
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM `users` WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already taken.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare to insert the new user record
            $stmt = $conn->prepare("INSERT INTO `users` (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $created_at);

            if ($stmt->execute()) {
                $success = "Account created successfully. <a href='./index.php'>Login here</a>";
            } else {
                $error = "Error creating account: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Register</title>
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
    </style>
</head>

<body>
    <div class="content">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Register</h4>
                <form class="login-form" action="" method="POST">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>

                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>

                    <input type="hidden" name="created_at" value="<?php echo date('Y-m-d H:i:s'); ?>">

                    <?php if (!empty($error)): ?> 
                        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
                    <?php elseif (!empty($success)): ?> 
                        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                    <a href="./index.php" class="btn btn-link">Log In</a>
                </form>
            </div>
        </div>
    </div>

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
