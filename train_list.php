<?php
session_start();
if (empty($_SESSION['user_info'])) {
echo "<script type='text/javascript'>alert('Please login to manage trains!'); window.location.href='login.php';</script>";
exit();
}

$conn = mysqli_connect("localhost", "root", "", "railway");
if (!$conn) {
die("<script type='text/javascript'>alert('Database connection failed: " . mysqli_connect_error() . "');</script>");
}

// Handle Add/Edit Train
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$t_no = trim($_POST['t_no']);
$t_name = trim($_POST['t_name']);
$capacity = (int)$_POST['capacity'];

if (empty($t_no) || empty($t_name) || $capacity <= 0) {
echo "<script type='text/javascript'>alert('Train Number, Name, and Capacity are required and Capacity must be positive.');</script>";
} else {
if (!empty($_POST['original_t_no'])) {
// Edit existing train
$original_t_no = trim($_POST['original_t_no']);
$stmt = mysqli_prepare($conn, "UPDATE trains SET t_no = ?, t_name = ?, capacity = ? WHERE t_no = ?");
if ($stmt) {
mysqli_stmt_bind_param($stmt, 'ssis', $t_no, $t_name, $capacity, $original_t_no);
if (mysqli_stmt_execute($stmt)) {
echo "<script type='text/javascript'>alert('Train " . htmlspecialchars($t_name) . " updated successfully!'); window.location.href='train_list.php';</script>";
} else {
echo "<script type='text/javascript'>alert('Error updating train: " . mysqli_error($conn) . "');</script>";
}
mysqli_stmt_close($stmt);
} else {
echo "<script type='text/javascript'>alert('Database error preparing update statement: " . mysqli_error($conn) . "');</script>";
}
} else {
// Add new train
$stmt = mysqli_prepare($conn, "INSERT INTO trains (t_no, t_name, capacity, date_created) VALUES (?, ?, ?, NOW())");
if ($stmt) {
mysqli_stmt_bind_param($stmt, 'ssi', $t_no, $t_name, $capacity);
if (mysqli_stmt_execute($stmt)) {
echo "<script type='text/javascript'>alert('Train " . htmlspecialchars($t_name) . " added successfully!'); window.location.href='train_list.php';</script>";
} else {
echo "<script type='text/javascript'>alert('Error adding train (Train Number might already exist): " . mysqli_error($conn) . "');</script>";
}
mysqli_stmt_close($stmt);
} else {
echo "<script type='text/javascript'>alert('Database error preparing insert statement: " . mysqli_error($conn) . "');</script>";
}
}
exit(); // Exit after handling POST request
}
}

// Handle Delete Train
if (isset($_POST['delete_train_no'])) {
$t_no_to_delete = trim($_POST['delete_train_no']);
$stmt = mysqli_prepare($conn, "DELETE FROM trains WHERE t_no = ?");
if ($stmt) {
mysqli_stmt_bind_param($stmt, 's', $t_no_to_delete);
if (mysqli_stmt_execute($stmt)) {
echo "<script type='text/javascript'>alert('Train " . htmlspecialchars($t_no_to_delete) . " deleted successfully!'); window.location.href='train_list.php';</script>";
} else {
echo "<script type='text/javascript'>alert('Error deleting train: " . mysqli_error($conn) . "');</script>";
}
mysqli_stmt_close($stmt);
} else {
echo "<script type='text/javascript'>alert('Database error preparing delete statement: " . mysqli_error($conn) . "');</script>";
}
exit(); // Exit after handling POST request
}

// Pagination settings
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search parameters
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$search_params = [];
$search_types = '';
$where_clauses = [];

if (!empty($search_query)) {
$like_query = '%' . $search_query . '%';
$where_clauses[] = "t_no LIKE ?";
$search_params[] = $like_query;
$search_types .= 's';

$where_clauses[] = "t_name LIKE ?";
$search_params[] = $like_query;
$search_types .= 's';
}

$search_condition_sql = '';
if (!empty($where_clauses)) {
$search_condition_sql = " WHERE (" . implode(" OR ", $where_clauses) . ")";
}

// Fetch total number of records for pagination
$total_sql = "SELECT COUNT(*) FROM trains" . $search_condition_sql;
$total_stmt = mysqli_prepare($conn, $total_sql);

if ($total_stmt) {
if (!empty($search_params)) {
mysqli_stmt_bind_param($total_stmt, $search_types, ...$search_params);
}
mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_rows = mysqli_fetch_row($total_result)[0];
mysqli_stmt_close($total_stmt);
} else {
echo "<script type='text/javascript'>alert('Database error fetching total trains count: " . mysqli_error($conn) . "');</script>";
$total_rows = 0;
}
$total_pages = ceil($total_rows / $limit);

// Fetch train data
$sql = "SELECT t_no, t_name, capacity, date_created FROM trains" . $search_condition_sql . " LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
$all_params = array_merge($search_params, [$limit, $offset]);
$all_types = $search_types . 'ii';

mysqli_stmt_bind_param($stmt, $all_types, ...$all_params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
} else {
echo "<script type='text/javascript'>alert('Database error fetching train data: " . mysqli_error($conn) . "');</script>";
$result = false;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Trains</title>
<link rel="stylesheet" href="style.css">
<style>
body {
background: url(img/bg3.jpg) no-repeat center center fixed;
background-size: cover;
color: white;
}
#train-container {
margin: auto;
margin-top: 50px;
width: 80%;
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
.form-section {
background-color: rgba(255,255,255,0.1);
padding: 20px;
border-radius: 15px;
margin-bottom: 30px;
}
.form-section h2 {
color: #ADD8E6;
margin-bottom: 15px;
text-align: center;
}
.form-section label {
display: inline-block;
width: 120px;
margin-bottom: 10px;
color: white;
}
.form-section input[type="text"],
.form-section input[type="number"] {
padding: 8px;
border-radius: 5px;
border: 1px solid #ccc;
width: 250px;
margin-bottom: 10px;
color: #333;
}
.form-section input[type="submit"],
.search-form input[type="submit"] {
padding: 8px 15px;
border-radius: 5px;
border: none;
background-color: #4CAF50;
color: white;
cursor: pointer;
}
.search-form {
text-align: center;
margin-bottom: 20px;
}
.search-form input[type="text"] {
padding: 8px;
border-radius: 5px;
border: 1px solid #ccc;
width: 300px;
color: #333;
}
table {
width: 100%;
border-collapse: collapse;
margin-top: 20px;
color: white;
}
th, td {
padding: 10px;
border: 1px solid #ddd;
text-align: left;
}
th {
background-color: #333;
color: white;
}
.action-buttons {
display: flex;
gap: 5px;
}
.action-buttons .button {
padding: 5px 10px;
font-size: 14px;
width: auto;
text-decoration: none;
color: white;
background-color: #007bff;
border-radius: 5px;
border: none;
cursor: pointer;
}
.pagination {
text-align: center;
margin-top: 20px;
}
.pagination a {
color: white;
padding: 8px 16px;
text-decoration: none;
border: 1px solid #ddd;
margin: 0 4px;
border-radius: 5px;
}
.pagination a.active {
background-color: #4CAF50;
color: white;
border: 1px solid #4CAF50;
}
.pagination a:hover:not(.active) {
background-color: #555;
}
</style>
<script type="text/javascript">
function populateEditForm(t_no, t_name, capacity) {
document.getElementById('train_form_title').innerText = 'Edit Train';
document.getElementById('t_no_input').value = t_no;
document.getElementById('t_name_input').value = t_name;
document.getElementById('capacity_input').value = capacity;
document.getElementById('original_t_no_input').value = t_no; // Store original t_no for update query
document.getElementById('t_no_input').readOnly = false; // Allow editing train number if needed
document.getElementById('add_edit_train_submit').value = 'Update Train';
}

function resetTrainForm() {
document.getElementById('train_form_title').innerText = 'Add New Train';
document.getElementById('t_no_input').value = '';
document.getElementById('t_name_input').value = '';
document.getElementById('capacity_input').value = '';
document.getElementById('original_t_no_input').value = '';
document.getElementById('t_no_input').readOnly = false;
document.getElementById('add_edit_train_submit').value = 'Add Train';
}
</script>
</head>
<body>
<?php include('header.php'); ?>

<div id="train-container">
<h1>Manage Trains</h1>

<div class="form-section">
<h2 id="train_form_title">Add New Train</h2>
<form method="post" action="train_list.php">
<label for="t_no_input">Train Number:</label>
<input type="text" id="t_no_input" name="t_no" required><br>

<label for="t_name_input">Train Name:</label>
<input type="text" id="t_name_input" name="t_name" required><br>

<label for="capacity_input">Capacity:</label>
<input type="number" id="capacity_input" name="capacity" min="1" required><br>

<input type="hidden" id="original_t_no_input" name="original_t_no">
<input type="submit" id="add_edit_train_submit" name="add_edit_train" value="Add Train">
<input type="button" value="Reset Form" onclick="resetTrainForm()">
</form>
</div>

<form method="get" action="train_list.php" class="search-form">
<input type="text" name="search" placeholder="Search by Train No. or Name..." value="<?php echo htmlspecialchars($search_query); ?>">
<input type="submit" value="Search">
</form>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<table>
<thead>
<tr>
<th>Train Number</th>
<th>Train Name</th>
<th>Capacity</th>
<th>Date Created</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?php echo htmlspecialchars($row['t_no']); ?></td>
<td><?php echo htmlspecialchars($row['t_name']); ?></td>
<td><?php echo htmlspecialchars($row['capacity']); ?></td>
<td><?php echo htmlspecialchars($row['date_created']); ?></td>
<td class="action-buttons">
<button type="button" class="button" onclick="populateEditForm('<?php echo htmlspecialchars($row['t_no']); ?>', '<?php echo htmlspecialchars($row['t_name']); ?>', '<?php echo htmlspecialchars($row['capacity']); ?>')">Edit</button>
<form method="post" action="train_list.php" onsubmit="return confirm('Are you sure you want to delete train <?php echo htmlspecialchars($row['t_name']); ?> (<?php echo htmlspecialchars($row['t_no']); ?>)? This will also delete associated schedules and tickets!');">
<input type="hidden" name="delete_train_no" value="<?php echo htmlspecialchars($row['t_no']); ?>">
<input type="submit" value="Delete" class="button">
</form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<div class="pagination">
<?php for ($i = 1; $i <= $total_pages; $i++): ?>
<a href="train_list.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
</div>
<?php else: ?>
<p style="text-align: center; color: white;">No trains found.</p>
<?php endif; ?>
</div>

</body>
</html>
