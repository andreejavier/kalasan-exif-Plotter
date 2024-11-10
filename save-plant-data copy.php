<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

// Include the database connection file
include 'db_connection.php';

// Check if form data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the current user's ID from the session
    $username = $_SESSION['username'];

    // Fetch user ID based on username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists
    if ($user) {
        $user_id = $user['id'];
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit();
    }

    $stmt->close();

    // Get other form inputs
    $latitude = $_POST['lat'];
    $longitude = $_POST['lon'];
    $date_time = $_POST['date'];
    $address = $_POST['address'];

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imagePath = 'uploads/' . uniqid() . '-' . basename($imageName);

        // Move uploaded file to the 'uploads' directory
        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            echo json_encode(['success' => false, 'error' => 'Image upload failed']);
            exit();
        }
    }

    // Prepare EXIF data
    $exif_data = json_encode([
        'latitude' => $latitude,
        'longitude' => $longitude,
        'date_time' => $date_time,
        'address' => $address
    ]);

    // Insert into tree_records table
    $stmt = $conn->prepare("INSERT INTO tree_records (user_id, latitude, longitude, date_time, address, image_path, exif_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iddssss", $user_id, $latitude, $longitude, $date_time, $address, $imagePath, $exif_data);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database insert failed']);
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
