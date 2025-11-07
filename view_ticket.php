<?php
session_start();
if (empty($_SESSION['user_info'])) {
echo "<script type='text/javascript'>alert('Please login to view ticket details!'); window.location.href='login.php';</script>";
exit();
}

$conn = mysqli_connect("localhost", "root", "", "railway");
if (!$conn) {
echo "<script type='text/javascript'>alert('Database failed');</script>";
die('Could not connect: ' . mysqli_connect_error());
}

$pnr = isset($_GET['pnr']) ? mysqli_real_escape_string($conn, $_GET['pnr']) : '';
$ticket_details = null;

if (!empty($pnr)) {
$sql = "SELECT tk.PNR, p.p_fname, p.p_lname, p.email, p.p_contact, p.p_age, p.p_gender, 
tr.t_name, tk.t_status, tk.seat_number, tk.schedule_code 
FROM tickets tk 
JOIN passengers p ON tk.email = p.email 
JOIN trains tr ON tk.t_no = tr.t_no 
WHERE tk.PNR = '$pnr'";
$result = mysqli_query($conn, $sql);
$ticket_details = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Ticket Details</title>
<LINK REL="STYLESHEET" HREF="STYLE.CSS">
<style type="text/css">
body {
background: url(img/bg7.jpg) no-repeat center center fixed;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;
background-size: cover;
color: white;
}
#ticket-details-container {
margin: auto;
margin-top: 50px;
width: 60%;
padding: 20px;
background-color: rgba(0,0,0,0.7);
border-radius: 25px;
box-shadow: 0 0 10px rgba(0,0,0,0.5);
}
h1 {
text-align: center;
color: white;
font-family: "Comic Sans MS", cursive, sans-serif;
margin-bottom: 30px;
}
.detail-row {
margin-bottom: 10px;
}
.detail-row strong {
display: inline-block;
width: 150px;
color: #ADD8E6; /* Light blue for labels */
}
.detail-row span {
color: white;
}
.back-button {
display: block;
width: 150px;
margin: 20px auto 0;
text-align: center;
padding: 10px;
background-color: #4CAF50;
color: white;
text-decoration: none;
border-radius: 5px;
}
</style>
</head>
<body>
<?php include('header.php'); ?>

<div id="ticket-details-container">
<h1>Ticket Details</h1>

<?php if ($ticket_details): ?>
<div class="detail-row">
<strong>PNR:</strong> <span><?php echo htmlspecialchars($ticket_details['PNR']); ?></span>
</div>
<div class="detail-row">
<strong>Passenger Name:</strong> <span><?php echo htmlspecialchars($ticket_details['p_fname'] . ' ' . $ticket_details['p_lname']); ?></span>
</div>
<div class="detail-row">
<strong>Email:</strong> <span><?php echo htmlspecialchars($ticket_details['email']); ?></span>
</div>
<div class="detail-row">
<strong>Contact:</strong> <span><?php echo htmlspecialchars($ticket_details['p_contact']); ?></span>
</div>
<div class="detail-row">
<strong>Age:</strong> <span><?php echo htmlspecialchars($ticket_details['p_age']); ?></span>
</div>
<div class="detail-row">
<strong>Gender:</strong> <span><?php echo htmlspecialchars($ticket_details['p_gender']); ?></span>
</div>
<div class="detail-row">
<strong>Train Name:</strong> <span><?php echo htmlspecialchars($ticket_details['t_name']); ?></span>
</div>
<div class="detail-row">
<strong>Ticket Status:</strong> <span><?php echo htmlspecialchars($ticket_details['t_status']); ?></span>
</div>
<div class="detail-row">
<strong>Seat Number:</strong> <span><?php echo htmlspecialchars($ticket_details['seat_number'] ?? 'N/A'); ?></span>
</div>
<div class="detail-row">
<strong>Schedule Code:</strong> <span><?php echo htmlspecialchars($ticket_details['schedule_code'] ?? 'N/A'); ?></span>
</div>
<a href="reservation.php" class="back-button">Back to Reservations</a>
<?php else: ?>
<p style="text-align: center; color: white;">Ticket details not found or invalid PNR.</p>
<a href="reservation.php" class="back-button">Back to Reservations</a>
<?php endif; ?>
</div>

</body>
</html>
