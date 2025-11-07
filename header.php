<!DOCTYPE html>
<html>
<head>
<title>Railway Website</title>
<link rel="stylesheet" href="s1.css" type="text/css">
<style type="text/css">
li {
font-family: sans-serif;
font-size: 18px;
list-style: none; /* Remove bullets */
}

/* Dropdown styles */
#dropdown {
position: relative;
display: inline-block;
}
#Logout {
position: absolute;
background-color: #f9f9f9;
min-width: 130px;
box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
z-index: 1;
top: 100%;
left: 0;
text-align: center;
display: none;
}
#Logout a {
color: black;
padding: 12px 16px;
text-decoration: none;
display: block;
}
#Logout a:hover {
background-color: #ddd;
}

/* Navbar container */
.Menu {
background-color: #ADD8E6; /* Light blue */
padding: 10px 25px;
border-radius: 5px;
text-align: center; /* Center all links */
}

/* Navbar links */
.Menu a, .Menu span {
color: black;
text-decoration: none;
padding: 8px 15px;
display: inline-block;
font-weight: bold;
}

.Menu a:hover {
color: white;
background-color: #87CEEB;
border-radius: 3px;
}

/* Inline menu */
#navmenu {
margin: 0;
padding: 0;
display: inline-block;
}
#navmenu li {
display: inline-block;
margin: 0 5px;
}
</style>

<script src="jquery.js"></script>
<script>
$(document).ready(function(){
$("#Logout").hide();
$("#user").hover(function(){
$("#Logout").toggle("slow");
});
$(document).on('click', function(event) {
if (!$(event.target).closest('#dropdown').length) {
$('#Logout').hide();
}
});
});
</script>
</head>
<body>
<div class="Menu">
<ul id="navmenu">
<li><a href="index.php">Home</a></li>
<li><a href="dashboard.php">Dashboard</a></li>
<li><a href="pnrstatus.php">PNR Status</a></li>
<li><a href="booktkt.php">Book&nbsp;a&nbsp;ticket</a></li>
<li><a href="reservation.php">Reservation</a></li>
<li><a href="train_list.php">Trains</a></li>
<li><a href="schedule_list.php">Schedules</a></li>
<li><a href="payment.php" style="color:white; background-color:#007BFF; border-radius:5px; padding:6px 12px;">Payment</a></li>
<li><a href="inquires.php">Inquires</a></li>
<li><a href="settings.php">Settings</a></li>
<li>
<?php
if(isset($_SESSION['user_info'])){
echo '<div id="dropdown"><span id="user">'.$_SESSION['user_info'].'</span><div id="Logout"><a href="logout.php">Logout</a></div></div>';
} else {
echo '<a href="register.php">Login/Register</a>';
}
?>
</li>
</ul>
</div>
</body>
</html>
