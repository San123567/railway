<?php
session_start();
if (empty($_SESSION['user_info'])) {
    echo "<script>alert('Unauthorized access.'); window.location.href='login.php';</script>";
    exit();
}

include 'config.php';

$system_name = mysqli_real_escape_string($conn, $_POST['system_name']);
$username = mysqli_real_escape_string($conn, $_POST['username']);
$about_us = mysqli_real_escape_string($conn, $_POST['about_us']);

// Check if settings exist
$result = mysqli_query($conn, "SELECT * FROM settings");
if (mysqli_num_rows($result) > 0) {
    // Update
    $sql = "UPDATE settings SET system_name='$system_name', username='$username', about_us='$about_us' LIMIT 1";
} else {
    // Insert
    $sql = "INSERT INTO settings (system_name, username, about_us) VALUES ('$system_name', '$username', '$about_us')";
}

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Settings updated successfully!'); window.location.href='settings.php';</script>";
} else {
    echo "<script>alert('Error updating settings: " . mysqli_error($conn) . "');</script>";
}
?>
