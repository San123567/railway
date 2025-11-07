<?php
session_start();
// Check if user is logged in, otherwise redirect to login page
if (empty($_SESSION['user_info'])) {
echo "<script type='text/javascript'>alert('Please login to view the dashboard!'); window.location.href='login.php';</script>";
exit();
}

$conn = mysqli_connect("localhost", "root", "", "railway");
if (!$conn) {
echo "<script type='text/javascript'>alert('Database connection failed: " . mysqli_connect_error() . "');</script>";
die('Could not connect: ' . mysqli_connect_error());
}

// Fetch total trains
$total_trains = 0;
$stmt_trains = mysqli_prepare($conn, "SELECT COUNT(*) FROM trains");
if ($stmt_trains) {
mysqli_stmt_execute($stmt_trains);
mysqli_stmt_bind_result($stmt_trains, $total_trains);
mysqli_stmt_fetch($stmt_trains);
mysqli_stmt_close($stmt_trains);
} else {
echo "<script type='text/javascript'>alert('Error fetching total trains: " . mysqli_error($conn) . "');</script>";
}

// Fetch total schedules
$total_schedules = 0;
$stmt_schedules = mysqli_prepare($conn, "SELECT COUNT(*) FROM schedules");
if ($stmt_schedules) {
mysqli_stmt_execute($stmt_schedules);
mysqli_stmt_bind_result($stmt_schedules, $total_schedules);
mysqli_stmt_fetch($stmt_schedules);
mysqli_stmt_close($stmt_schedules);
} else {
echo "<script type='text/javascript'>alert('Error fetching total schedules: " . mysqli_error($conn) . "');</script>";
}

// Fetch total reserved passengers
$total_reserved_passengers = 0;
$stmt_reserved = mysqli_prepare($conn, "SELECT COUNT(*) FROM reservation");
if ($stmt_reserved) {
mysqli_stmt_execute($stmt_reserved);
mysqli_stmt_bind_result($stmt_reserved, $total_reserved_passengers);
mysqli_stmt_fetch($stmt_reserved);
mysqli_stmt_close($stmt_reserved);
} else {
echo "<script type='text/javascript'>alert('Error fetching reserved passengers: " . mysqli_error($conn) . "');</script>";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<style type="text/css">
body {
background: url(img/train8.jpg) no-repeat center center fixed;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;
background-size: cover;
color: white;
}
#dashboard-container {
margin: auto;
margin-top: 50px;
width: 60%;
padding: 30px;
background-color: rgba(0,0,0,0.7);
border-radius: 25px;
box-shadow: 0 0 10px rgba(0,0,0,0.5);
text-align: center;
}
h1 {
color: white;
font-family: "Comic Sans MS", cursive, sans-serif;
margin-bottom: 40px;
}
.stats-grid {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
gap: 20px;
justify-content: center;
}
.stat-box {
background-color: rgba(255,255,255,0.1);
padding: 25px;
border-radius: 15px;
border: 1px solid rgba(255,255,255,0.2);
box-shadow: 0 0 8px rgba(0,0,0,0.3);
}
.stat-box h2 {
font-size: 24px;
margin-bottom: 10px;
color: #ADD8E6;
}
.stat-box p {
font-size: 36px;
font-weight: bold;
color: #FFF;
}
</style>
</head>
<body>
<?php include('header.php'); ?>

<div id="dashboard-container">
<h1>System Dashboard</h1>

<div class="stats-grid">
<div class="stat-box">
<h2>Total Trains</h2>
<p><?php echo htmlspecialchars($total_trains); ?></p>
</div>
<div class="stat-box">
<h2>Total Schedules</h2>
<p><?php echo htmlspecialchars($total_schedules); ?></p>
</div>
<div class="stat-box">
<h2>Reserved Passengers</h2>
<p><?php echo htmlspecialchars($total_reserved_passengers); ?></p>
</div>
</div>
</div>

</body>
</html>
