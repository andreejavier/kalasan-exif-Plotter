<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db_connection.php';

// Get the logged-in user's username or ID
$username = $_SESSION['username'];

// Fetch user ID based on username
$user_query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($user_query);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Handle tree deletion request
if (isset($_POST['delete_tree_id'])) {
    $tree_id = $_POST['delete_tree_id'];

    // Delete tree record for the current user
    $delete_query = "DELETE FROM tree_planted WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('ii', $tree_id, $user_id);

    if ($stmt->execute()) {
        $message = "Tree record deleted successfully.";
    } else {
        $message = "Error deleting tree record.";
    }

    $stmt->close();
}

// Handle additional image uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['additional_images'])) {
    $tree_id = $_POST['tree_id'];
    $uploads_dir = 'uploads/trees';

    // Ensure upload directory exists
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    foreach ($_FILES['additional_images']['tmp_name'] as $index => $tmp_name) {
        $file_type = mime_content_type($tmp_name);

        // Only allow image uploads (JPEG, PNG)
        if (in_array($file_type, ['image/jpeg', 'image/png'])) {
            $image_name = time() . "_" . basename($_FILES['additional_images']['name'][$index]);
            $target_file = "$uploads_dir/$image_name";

            if (move_uploaded_file($tmp_name, $target_file)) {
                $stmt = $conn->prepare("INSERT INTO tree_images (tree_planted_id, image_path) VALUES (?, ?)");
                $stmt->bind_param('is', $tree_id, $target_file);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    $message = "Additional images uploaded successfully.";
}

// Query to fetch tree records with additional images
$query = "SELECT tp.*, ti.image_path AS additional_image
          FROM tree_planted tp
          LEFT JOIN tree_images ti ON tp.id = ti.tree_planted_id
          WHERE tp.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$trees = [];
while ($row = $result->fetch_assoc()) {
    $tree_id = $row['id'];
    if (!isset($trees[$tree_id])) {
        $trees[$tree_id]['details'] = $row;
        $trees[$tree_id]['images'] = [];
    }
    if ($row['additional_image']) {
        $trees[$tree_id]['images'][] = $row['additional_image'];
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Kalasan Mapping - Your Planted Trees</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
    <link href="assets/css/custom-dashboard.css" rel="stylesheet">

    <style>
        .main-content { margin-top: 20px; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .tree-card { margin-bottom: 20px; }
        .tree-card img { width: 100%; height: 200px; object-fit: cover; }
        .card-title { font-size: 1.2em; font-weight: bold; }
        .btn-delete { background-color: #dc3545; color: white; }
        .additional-images img { width: 100px; height: 100px; margin: 5px; object-fit: cover; }
    </style>
</head>

<body>
<div class="wrapper">
    <div class="sidebar" data-color="white" data-active-color="danger">
        <div class="logo">
            <a href="./profile.php" class="simple-text logo-mini">
                <div class="logo-image-small">
                    <img src="assets/img/location icon.jpg" alt="Logo">
                </div>
            </a>
            <a href="#" class="simple-text logo-normal">Kalasan</a>
        </div>
        <div class="sidebar-wrapper">
            <ul class="nav">
                <li class="active">
                    <a href="./dashboard.php">
                        <i class="nc-icon nc-bank"></i>
                        <p>Home</p>
                    </a>
                </li>
                <li>
                    <a href="./map.php">
                        <i class="nc-icon nc-pin-3"></i>
                        <p>Maps</p>
                    </a>
                </li>
                <li>
                    <a href="./upload-plant.php">
                        <i class="nc-icon nc-cloud-upload-94"></i>
                        <p>Plant</p>
                    </a>
                </li>
                <li>
                    <a href="./planted_trees.php">
                        <i class="nc-icon nc-chart-bar-32"></i>
                        <p>Your Planted</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-panel">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
            <div class="container-fluid">
                <div class="navbar-wrapper">
                    <a class="navbar-brand" href="javascript:;">Home</a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navigation">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="nc-icon nc-single-02"></i>
                                <span><?php echo $_SESSION['username']; ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                                <a class="dropdown-item" href="profile.php">View Profile</a>
                                <a class="dropdown-item" href="settings.php">Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
<div class="wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="main-content">
                <h3>Your Planted Trees</h3>
                
                <?php if (isset($message)): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($trees as $tree): ?>
                        <div class="col-md-4 tree-card">
                            <div class="card">
                                <img src="<?php echo htmlspecialchars($tree['details']['image_path']); ?>" alt="Main Image" class="card-img-top">
                                <div class="card-body">
                                    <p class="card-text">Location: <?php echo htmlspecialchars($tree['details']['address']); ?></p>
                                    <p class="card-text">Date & Time: <?php echo htmlspecialchars($tree['details']['date_time']); ?></p>

                                    <?php if (!empty($tree['images'])): ?>
                                        <div class="additional-images">
                                            <h6>Additional Images:</h6>
                                            <?php foreach ($tree['images'] as $img): ?>
                                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Additional Image">
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="tree_id" value="<?php echo htmlspecialchars($tree['details']['id']); ?>">
                                        <input type="file" name="additional_images[]" multiple>
                                        <button type="submit" class="btn btn-primary mt-2">Add Images</button>
                                    </form>

                                    <form method="POST" action="">
                                        <input type="hidden" name="delete_tree_id" value="<?php echo htmlspecialchars($tree['details']['id']); ?>">
                                        <button type="submit" class="btn btn-danger btn-delete">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
