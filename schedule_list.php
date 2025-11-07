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
<?php
session_start();
if(empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();
}

$conn = mysqli_connect("localhost", "root", "", "railway");
if(!$conn) {
die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables for edit mode
$edit_mode = false;
$edit_id = 0;
$current_schedule = null;

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
if(isset($_POST['add_schedule'])) {
// Add/Edit schedule code
$train_no = $_POST['train_no'];
$schedule_type = $_POST['schedule_type'];
$route_from = $_POST['route_from'];
$route_to = $_POST['route_to'];
$departure_time = $_POST['departure_time'];
$arrival_time = $_POST['arrival_time'];
$fare = $_POST['fare'];
$capacity = $_POST['capacity'];

if($schedule_type == 'daily') {
$days_of_week = isset($_POST['days_of_week']) ? implode(",", $_POST['days_of_week']) : '';
$schedule_date = NULL;
} else {
$schedule_date = $_POST['schedule_date'];
$days_of_week = NULL;
}

if(isset($_POST['schedule_id']) && !empty($_POST['schedule_id'])) {
// Update existing schedule
$schedule_id = $_POST['schedule_id'];
$sql = "UPDATE schedules SET 
train_no = '$train_no', 
schedule_type = '$schedule_type', 
route_from = '$route_from', 
route_to = '$route_to', 
departure_time = '$departure_time', 
arrival_time = '$arrival_time', 
fare = $fare, 
capacity = $capacity, 
schedule_date = " . ($schedule_date ? "'$schedule_date'" : "NULL") . ", 
days_of_week = " . ($days_of_week ? "'$days_of_week'" : "NULL") . "
WHERE schedule_id = $schedule_id";

$message = "Schedule updated successfully!";
} else {
// Add new schedule
$sql = "INSERT INTO schedules (train_no, schedule_type, route_from, route_to, departure_time, arrival_time, fare, capacity, schedule_date, days_of_week)
VALUES ('$train_no', '$schedule_type', '$route_from', '$route_to', '$departure_time', '$arrival_time', $fare, $capacity, " . 
($schedule_date ? "'$schedule_date'" : "NULL") . ", " . ($days_of_week ? "'$days_of_week'" : "NULL") . ")";

$message = "Schedule added successfully!";
}

if(mysqli_query($conn, $sql)) {
$_SESSION['message'] = $message;
} else {
$_SESSION['error'] = "Error: " . mysqli_error($conn);
}

header("Location: ".$_SERVER['PHP_SELF']);
exit();
}
}

// Handle edit request
if(isset($_GET['edit'])) {
$edit_id = intval($_GET['edit']);
$result = mysqli_query($conn, "SELECT * FROM schedules WHERE schedule_id = $edit_id");
if(mysqli_num_rows($result) > 0) {
$current_schedule = mysqli_fetch_assoc($result);
$edit_mode = true;
}
}

// Handle delete request
if(isset($_GET['delete'])) {
$delete_id = intval($_GET['delete']);
$sql = "DELETE FROM schedules WHERE schedule_id = $delete_id";
if(mysqli_query($conn, $sql)) {
$_SESSION['message'] = "Schedule deleted successfully!";
} else {
$_SESSION['error'] = "Error deleting schedule: " . mysqli_error($conn);
}
header("Location: ".$_SERVER['PHP_SELF']);
exit();
}

// Fetch trains for dropdown
$trains = mysqli_query($conn, "SELECT t_no, t_name FROM trains ORDER BY t_name");

// Fetch schedules
$schedules = mysqli_query($conn, "SELECT s.*, t.t_name FROM schedules s JOIN trains t ON s.train_no = t.t_no ORDER BY departure_time");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Train Schedule Management</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
body {
background-color: #f3f4f6;
font-family: 'Comic Sans MS', Tahoma, Geneva, Verdana, sans-serif;
}
.card {
background: white;
border-radius: 0.5rem;
box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.tab-active {
border-bottom: 3px solid #3b82f6;
font-weight: 600;
}
.schedule-type {
padding: 0.25rem 0.5rem;
border-radius: 0.25rem;
font-weight: 500;
font-size: 0.75rem;
}
.daily {
background-color: #e0f2fe;
color: #0369a1;
}
.onetime {
background-color: #d1fae5;
color: #065f46;
}
</style>
</head>
<body class="bg-gray-100">
<BODY  background="img/bg7.jpg" link="white" alink="white" vlink="white" width="1024" height="768">
<div class="container mx-auto px-4 py-8">
<div class="flex justify-between items-center mb-8">
<h1 class="text-3xl font-bold text-gray-800">Train Schedule</h1>
<button onclick="openModal(false)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
<i class="fas fa-plus mr-2"></i> Add Schedule
</button>
</div>

<!-- Notification Messages -->
<?php if(isset($_SESSION['message'])): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
<span class="block sm:inline"><?php echo $_SESSION['message']; ?></span>
<?php unset($_SESSION['message']); ?>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
<span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
<?php unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<!-- Schedule List -->
<div class="card overflow-hidden mb-8">
<div class="overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Train</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timing</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fare</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Availability</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
<?php while($schedule = mysqli_fetch_assoc($schedules)): ?>
<tr>
<td class="px-6 py-4 whitespace-nowrap">
<div class="font-medium text-gray-900"><?php echo $schedule['t_name']; ?></div>
<div class="text-gray-500 text-sm">#<?php echo $schedule['train_no']; ?></div>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<div class="text-gray-900"><?php echo $schedule['route_from']; ?></div>
<div class="text-gray-500 text-sm">to <?php echo $schedule['route_to']; ?></div>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<div class="text-gray-900">Dep: <?php echo date("h:i A", strtotime($schedule['departure_time'])); ?></div>
<div class="text-gray-500 text-sm">Arr: <?php echo date("h:i A", strtotime($schedule['arrival_time'])); ?></div>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<span class="schedule-type <?php echo $schedule['schedule_type']; ?>">
<?php echo ucfirst($schedule['schedule_type']); ?>
</span>
<?php if($schedule['schedule_type'] == 'daily'): ?>
<div class="text-gray-500 text-xs mt-1">Days: <?php echo $schedule['days_of_week']; ?></div>
<?php else: ?>
<div class="text-gray-500 text-xs mt-1">Date: <?php echo $schedule['schedule_date']; ?></div>
<?php endif; ?>
</td>
<td class="px-6 py-4 whitespace-nowrap text-gray-900">
₹<?php echo number_format($schedule['fare'], 2); ?>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<div class="flex items-center">
<?php 
$available = $schedule['capacity'] - rand(0, $schedule['capacity'] * 0.8); 
$percentage = ($available / $schedule['capacity']) * 100;
?>
<div class="w-24 mr-2">
<div class="h-2 bg-gray-200 rounded-full overflow-hidden">
<div 
class="h-full <?php echo $percentage > 50 ? 'bg-green-500' : ($percentage > 20 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
style="width: <?php echo $percentage; ?>%"
></div>
</div>
</div>
<span class="text-sm text-gray-600"><?php echo $available; ?>/<?php echo $schedule['capacity']; ?></span>
</div>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
<button onclick="openModal(true, <?php echo $schedule['schedule_id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
<a href="?delete=<?php echo $schedule['schedule_id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</div>

<!-- Add/Edit Schedule Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
<div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
<div class="flex justify-between items-center mb-4">
<h3 id="modalTitle" class="text-xl font-bold text-gray-800"><?php echo $edit_mode ? 'Edit Schedule' : 'Add New Schedule'; ?></h3>
<button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
<i class="fas fa-times"></i>
</button>
</div>

<form action="" method="POST" id="scheduleForm">
<?php if($edit_mode && $current_schedule): ?>
<input type="hidden" name="schedule_id" value="<?php echo $current_schedule['schedule_id']; ?>">
<?php endif; ?>

<div class="space-y-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Train</label>
<select name="train_no" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
<option value="">Select Train</option>
<?php 
mysqli_data_seek($trains, 0); // Reset pointer to beginning
while($train = mysqli_fetch_assoc($trains)): 
?>
<option value="<?php echo $train['t_no']; ?>" 
<?php if($edit_mode && $current_schedule['train_no'] == $train['t_no']) echo 'selected'; ?>>
<?php echo $train['t_name']; ?> (#<?php echo $train['t_no']; ?>)
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Schedule Type</label>
<select name="schedule_type" id="scheduleType" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
<option value="">Select Type</option>
<option value="daily" <?php if($edit_mode && $current_schedule['schedule_type'] == 'daily') echo 'selected'; ?>>Daily</option>
<option value="one-time" <?php if($edit_mode && $current_schedule['schedule_type'] == 'one-time') echo 'selected'; ?>>One-Time</option>
</select>
</div>

<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Route From</label>
<input type="text" name="route_from" value="<?php if($edit_mode) echo $current_schedule['route_from']; ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Route To</label>
<input type="text" name="route_to" value="<?php if($edit_mode) echo $current_schedule['route_to']; ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
</div>
</div>

<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Departure Time</label>
<input type="time" name="departure_time" value="<?php if($edit_mode) echo date('H:i', strtotime($current_schedule['departure_time'])); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Arrival Time</label>
<input type="time" name="arrival_time" value="<?php if($edit_mode) echo date('H:i', strtotime($current_schedule['arrival_time'])); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
</div>
</div>

<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Fare (₹)</label>
<input type="number" step="0.01" min="0" name="fare" value="<?php if($edit_mode) echo $current_schedule['fare']; ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
<input type="number" min="1" name="capacity" value="<?php if($edit_mode) echo $current_schedule['capacity']; ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
</div>
</div>

<div id="dailyScheduleFields" class="<?php echo (!$edit_mode || ($edit_mode && $current_schedule['schedule_type'] != 'daily')) ? 'hidden' : ''; ?>">
<label class="block text-sm font-medium text-gray-700 mb-1">Days of Operation</label>
<div class="grid grid-cols-4 gap-2">
<?php 
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$selected_days = $edit_mode && $current_schedule['schedule_type'] == 'daily' ? explode(',', $current_schedule['days_of_week']) : [];
?>
<?php foreach($days as $day): ?>
<label class="flex items-center">
<input type="checkbox" name="days_of_week[]" value="<?php echo $day; ?>" 
<?php if(in_array($day, $selected_days)) echo 'checked'; ?> class="mr-2"> <?php echo $day; ?>
</label>
<?php endforeach; ?>
</div>
</div>

<div id="oneTimeScheduleFields" class="<?php echo (!$edit_mode || ($edit_mode && $current_schedule['schedule_type'] != 'one-time')) ? 'hidden' : ''; ?>">
<label class="block text-sm font-medium text-gray-700 mb-1">Schedule Date</label>
<input type="date" name="schedule_date" value="<?php if($edit_mode && $current_schedule['schedule_type'] == 'one-time') echo $current_schedule['schedule_date']; ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
</div>

<div class="pt-4">
<button type="submit" name="add_schedule" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
<?php echo $edit_mode ? 'Update Schedule' : 'Add Schedule'; ?>
</button>
</div>
</div>
</form>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
function openModal(isEdit, scheduleId = null) {
const modal = document.getElementById('scheduleModal');
const modalTitle = document.getElementById('modalTitle');

if(isEdit && scheduleId) {
window.location.href = `?edit=${scheduleId}`;
} else {
modal.classList.remove('hidden');
modalTitle.textContent = 'Add New Schedule';
document.getElementById('scheduleForm').reset();
}
}

function closeModal() {
document.getElementById('scheduleModal').classList.add('hidden');
window.location.href = window.location.pathname;
}

// Show/hide schedule type fields
document.getElementById('scheduleType')?.addEventListener('change', function() {
const scheduleType = this.value;
document.getElementById('dailyScheduleFields').classList.add('hidden');
document.getElementById('oneTimeScheduleFields').classList.add('hidden');

if(scheduleType === 'daily') {
document.getElementById('dailyScheduleFields').classList.remove('hidden');
} else if(scheduleType === 'one-time') {
document.getElementById('oneTimeScheduleFields').classList.remove('hidden');
}
});

// If in edit mode, open modal automatically
<?php if($edit_mode): ?>
window.addEventListener('DOMContentLoaded', function() {
document.getElementById('scheduleModal').classList.remove('hidden');
});
<?php endif; ?>
</script>
</body>
</html>
