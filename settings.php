<?php
session_start();
if (empty($_SESSION['user_info'])) {
echo "<script>alert('Please login to view settings'); window.location.href='login.php';</script>";
exit();
}
include 'config.php';

// Load current settings
$system_name = '';
$username = '';
$about_us = '';

$query = "SELECT * FROM settings LIMIT 1";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
$row = mysqli_fetch_assoc($result);
$system_name = $row['system_name'];
$username = $row['username'];
$about_us = $row['about_us'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Settings</title>
<link rel="stylesheet" href="style.css">
<style>
body {
background: url('img/bg2.jpg') no-repeat center center fixed;
background-size: cover;
color: white;
font-family: Arial, sans-serif;
}
.settings-container {
width: 60%;
margin: 80px auto;
background: rgba(0, 0, 0, 0.75);
padding: 30px;
border-radius: 15px;
}
h2 {
text-align: center;
color: #ADD8E6;
margin-bottom: 30px;
}
label {
display: block;
margin-top: 20px;
color: #fff;
}
input[type="text"], textarea {
width: 100%;
padding: 10px;
margin-top: 5px;
border-radius: 8px;
border: none;
outline: none;
}
textarea {
resize: vertical;
height: 100px;
}
.btn {
margin-top: 30px;
padding: 12px 30px;
border: none;
background: #ADD8E6;
color: #000;
font-weight: bold;
border-radius: 8px;
cursor: pointer;
}
.btn:hover {
background: #87CEEB;
}
.logout-btn {
background: crimson;
float: right;
}
</style>
</head>
<body>
<?php include('header.php'); ?>
<div class="settings-container">
<div class="settings-container">
    <form action="update_settings.php" method="POST">
        <h2 class="comic-sans">System Settings</h2>
    </form>
</div>
<style>
    .comic-sans {
        font-family: "Comic Sans MS", cursive, sans-serif;
    }
    .settings-container {
        background-color: rgba(0, 0, 0, 0.8);
        padding: 30px;
        border-radius: 10px;
        color: white;
        width: 60%;
        margin: 20px auto; /* smaller top margin */
        border: 1px solid #000; /* overall border */
        border-top: 2px solid #000;
    }
</style>
<label for="system_name">System Name:</label>
<input type="text" name="system_name" id="system_name" value="<?php echo htmlspecialchars($system_name); ?>" required>

<label for="username">Admin Username:</label>
<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>

<label for="about_us">About Us:</label>
<textarea name="about_us" id="about_us"><?php echo htmlspecialchars($about_us); ?></textarea>

<button type="submit" class="btn">Save Changes</button>
<a href="logout.php" class="btn logout-btn">Logout</a>
</form>
</div>
</body>
</html>
