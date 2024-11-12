<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dev_kalasan_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for dashboard stats
$sql = "SELECT COUNT(*) AS contributor_count FROM users WHERE id IS NOT NULL";
$result = $conn->query($sql);
$contributor_count = ($result->num_rows > 0) ? $result->fetch_assoc()['contributor_count'] : 0;

$sql = "SELECT COUNT(*) AS planted_tree FROM tree_planted WHERE id IS NOT NULL";
$result = $conn->query($sql);
$planted_tree = ($result->num_rows > 0) ? $result->fetch_assoc()['planted_tree'] : 0;

// Fetch data for the chart (trees by category)
$sql = "SELECT category, COUNT(*) AS count FROM tree_planted GROUP BY category";
$result = $conn->query($sql);
$chartData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chartData[] = $row;
    }
}

// Fetch data for the species breakdown chart
$sql = "SELECT species_name, COUNT(*) AS count FROM tree_planted GROUP BY species_name";
$result = $conn->query($sql);
$speciesData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $speciesData[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalasan Dashboard & Analytics</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/paper-dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .wrapper { display: flex; height: 100vh; }
        .sidebar { width: 260px; position: fixed; height: 100%; background-color: #f8f9fa; }
        .main-panel { margin-left: 250px; width: 100%; padding-top: 80px; } /* Add top padding for fixed navbar */
        .navbar-transparent { background-color: rgba(255, 255, 255, 0.8) !important; }
        .card-stats { margin-bottom: 20px; } /* Add space between cards */
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
    
    <!-- Main Panel -->
    <div class="main-panel">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="navbar-wrapper">
                    <a class="navbar-brand" href="javascript:;">Dashboard & Analytics</a>
                </div>
                <div class="collapse navbar-collapse justify-content-end">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: black;">
                                <i class="nc-icon nc-single-02"></i>
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
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
        </nav>

        <!-- Main content -->
        <div class=" container-fluid">
            <div class="row">
                <!-- Dashboard Card for Contributors -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-globe text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <p class="card-category">Contributors</p>
                                    <p class="card-title"><?php echo $contributor_count; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Card for Planted Trees -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-success">
                                        <i class="nc-icon nc-planet text-success"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <p class="card-category">Planted Trees</p>
                                    <p class="card-title"><?php echo $planted_tree; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart for Tree Planting Analytics by Category -->
            <h3>Tree Planting Analytics by Category</h3>
            <canvas id="categoryChart" width="400" height="200"></canvas>

            <!-- Chart for Tree Planting Analytics by Species -->
            <h3>Tree Planting Analytics by Species</h3>
            <canvas id="speciesChart" width="400" height="200"></canvas>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="text-center">© 2024, Kalasan Dashboard by Team</div>
            </div>
        </footer>
    </div>
</div>

<!-- JS Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    // Prepare data for the category chart
    const chartData = <?php echo json_encode($chartData); ?>;
    const categories = chartData.map(data => data.category);
    const categoryCounts = chartData.map(data => data.count);

    // Prepare data for the species chart
    const speciesData = <?php echo json_encode($speciesData); ?>;
    const speciesNames = speciesData.map(data => data.species_name);
    const speciesCounts = speciesData.map(data => data.count);

    // Initialize the category chart
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'Tree Count by Category',
                data: categoryCounts,
                backgroundColor: ['#4CAF50', '#FFCE56'],
                borderColor: ['#388E3C', '#FBC02D'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Initialize the species chart
    const ctxSpecies = document.getElementById('speciesChart').getContext('2d');
    new Chart(ctxSpecies, {
        type: 'bar',
        data: {
            labels: speciesNames,
            datasets: [{
                label: 'Tree Count by Species',
                data: speciesCounts,
                backgroundColor: '#42A5F5',
                borderColor: '#1E88E5',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
</body>
<script src="assets/js/core/jquery.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <script src="assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>
</html>
