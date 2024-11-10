<?php
session_start();

// Check if the user is logged in; redirect to login if not
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection file
include 'db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and fetch current user's username from the session
$username = htmlspecialchars($_SESSION['username']);

// Prepare a statement to fetch user details safely
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // You can fetch user details like profile picture, email, etc. here if needed.
    } else {
        // If user not found, destroy session and redirect to login
        session_destroy();
        header("Location: index.php");
        exit();
    }

    // Close statement
    $stmt->close();
} else {
    // Handle error if the query fails
    die("Failed to prepare statement: " . $conn->error);
}

// Close the database connection if not used elsewhere
$conn->close();
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Plant Capture</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar Starts -->
        <div class="sidebar" data-color="white" data-active-color="danger">
            <div class="logo">
                <a href="#" class="simple-text logo-mini">
                    <img src="assets/img/tree icon.png" alt="Tree Icon">
                </a>
                <a href="#" class="simple-text logo-normal">
                    Kalasan
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="nc-icon nc-bank"></i>
                            <p>Home</p>
                        </a>
                    </li>
                    <li>
                        <a href="./map.php">
                            <i class="nc-icon nc-pin-3"></i>
                            <p>Map</p>
                        </a>
                    </li>
                    <li>
                        <a href="./upload-plant.php">
                            <i class="nc-cloud-upload-94"></i>
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
        <!-- Sidebar Ends -->

        <!-- Main Panel Starts -->
        <div class="main-panel">
            <div class="content">
                <div class="container mt-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img id="imagePreview" class="img-fluid" src="https://www.freeiconspng.com/uploads/no-image-icon-6.png" alt="Preview">
                        </div>
                        <div class="col-md-8">
                            <h5 class="card-title">Upload</h5>
                            <!-- Camera Input -->
                            <input type="file" id="imageInputCamera" class="d-none" accept="image/*" capture="environment" onchange="handleImageInput(this);" />
                            <button class="btn btn-primary mt-3" onclick="document.getElementById('imageInputCamera').click();">Open Camera</button>

                            <!-- Gallery Input -->
                            <input type="file" id="imageInputGallery" class="d-none" accept="image/*" onchange="handleImageInput(this);" />
                            <button class="btn btn-secondary mt-3" onclick="document.getElementById('imageInputGallery').click();">Open Gallery</button>

                            <form id="plantDataForm" class="mt-3" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="latitudeInput" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitudeInput" name="lat" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="longitudeInput" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitudeInput" name="lon" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="dateTime" class="form-label">Datetime</label>
                                    <input type="text" class="form-control" id="dateTime" name="date" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="locationAddress" class="form-label">Location Address</label>
                                    <input type="text" id="locationAddress" class="form-control" name="address" readonly>
                                </div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Save Data</button>
                                <button id="clearButton" class="btn btn-secondary" type="button" onclick="clearForm()">Clear</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Starts -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Upload Details?</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">Do you want to save the data?</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="submitForm()">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Ends -->
            </div>
        </div>
        <!-- Main Panel Ends -->
    </div>

    <!-- JavaScript Libraries and EXIF JS -->
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
    <script>
        // JavaScript functions for image handling and geolocation
        function handleImageInput(input) {
            // Load and process image metadata (EXIF data)
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('imagePreview').src = e.target.result;

                    EXIF.getData(file, function () {
                        const lat = EXIF.getTag(this, "GPSLatitude");
                        const lon = EXIF.getTag(this, "GPSLongitude");
                        const dateTime = EXIF.getTag(this, "DateTimeOriginal");

                        if (lat && lon) {
                            const latitude = convertDMSToDD(lat);
                            const longitude = convertDMSToDD(lon);
                            document.getElementById('latitudeInput').value = latitude;
                            document.getElementById('longitudeInput').value = longitude;
                            getAddress(latitude, longitude);
                        }
                        if (dateTime) {
                            document.getElementById('dateTime').value = dateTime;
                        }
                    });
                };
                reader.readAsDataURL(file);
            }
        }

        // Convert DMS to Decimal Degrees for GPS coordinates
        function convertDMSToDD(dms) {
            return dms[0] + dms[1] / 60 + dms[2] / 3600;
        }

        // Function to retrieve address via reverse geocoding
        function getAddress(latitude, longitude) {
            const url = `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('locationAddress').value = data.display_name || 'Address not found';
                })
                .catch(error => {
                    console.error('Error fetching address:', error);
                    document.getElementById('locationAddress').value = 'Error fetching address';
                });
        }

        function submitForm() {
    const formData = new FormData(document.getElementById('plantDataForm'));
    
    // Append the image file
    const imageInputCamera = document.getElementById('imageInputCamera').files[0];
    const imageInputGallery = document.getElementById('imageInputGallery').files[0];
    if (imageInputCamera) {
        formData.append('image', imageInputCamera);
    } else if (imageInputGallery) {
        formData.append('image', imageInputGallery);
    }

    fetch('save-plant-data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Data saved successfully!');
        } else {
            alert('Error saving data: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the data.');
    });
}




    </script>
</body>
</html>
