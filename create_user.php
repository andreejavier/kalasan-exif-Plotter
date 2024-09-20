<?php
// create_user.php

include 'db_connection.php';

// Example user data
$username = 'admin';
$plainPassword = 'admin123'; // Plain password to be hashed

// Hash the password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);

// Execute the statement
if ($stmt->execute()) {
    echo "User created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
