<?php
// dashboard.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kalasan Mapping</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2 class="sidebar-title">Kalasan</h2>
            <ul class="sidebar-menu">
                <li><a href="#overview">Stats</a></li>
                <li><a href="map.php">Map</a></li>
                <li><a href="#community">Community</a></li>
                <li><a href="upload-plant.php">Plant</a></li>
                <li><a href="#settings">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="main-header">
                <input type="text" placeholder="Species">
                <input type="text" placeholder="Location">
                <div class="stats">
                    <div class="stat-item">Species: <span>0</span></div>
                    <div class="stat-item">Trees Planted: <span>0</span></div>
                </div>
            </header>
            <div id="dashboard-map" class="map-container"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize Leaflet map
        const map = L.map('dashboard-map').setView([12.8797, 121.7740], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    </script>
</body>
</html>
