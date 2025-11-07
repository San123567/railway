<?php
session_start();
if (empty($_SESSION['user_info'])) {
echo "<script type='text/javascript'>alert('Please login to view inquiries!'); window.location.href='login.php';</script>";
exit();
}

$conn = mysqli_connect("localhost", "root", "", "railway");
if (!$conn) {
echo "<script type='text/javascript'>alert('Database failed');</script>";
die('Could not connect: ' . mysqli_connect_error());
}

// Handle inquiry deletion
if (isset($_POST['delete_inquiry_id'])) {
$inquiry_id_to_delete = mysqli_real_escape_string($conn, $_POST['delete_inquiry_id']);
$delete_sql = "DELETE FROM inquiries WHERE id = '$inquiry_id_to_delete'";
if (mysqli_query($conn, $delete_sql)) {
echo "<script type='text/javascript'>alert('Inquiry deleted successfully!'); window.location.href='inquiries.php';</script>";
} else {
echo "<script type='text/javascript'>alert('Failed to delete inquiry.');</script>";
}
}

// Fetch all inquiries
$sql = "SELECT id, inquirer_name, inquirer_email, message, timestamp FROM inquiries ORDER BY timestamp DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>User Inquiries</title>
<LINK REL="STYLESHEET" HREF="STYLE.CSS">
<style type="text/css">
body {
background: url(img/bg2.jpg) no-repeat center center fixed;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;
background-size: cover;
color: white;
}
#inquiries-container {
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
}
</style>
</head>
<body>
<?php include('header.php'); ?>

<div id="inquiries-container">
<h1>User Inquiries</h1>

<?php if (mysqli_num_rows($result) > 0): ?>
<table>
<thead>
<tr>
<th>Inquirer Name</th>
<th>Email</th>
<th>Message</th>
<th>Date/Time</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?php echo htmlspecialchars($row['inquirer_name']); ?></td>
<td><?php echo htmlspecialchars($row['inquirer_email']); ?></td>
<td><?php echo htmlspecialchars($row['message']); ?></td>
<td><?php echo htmlspecialchars($row['timestamp']); ?></td>
<td class="action-buttons">
<a href="mailto:<?php echo htmlspecialchars($row['inquirer_email']); ?>" class="button">Reply</a>
<form method="post" action="inquiries.php" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
<input type="hidden" name="delete_inquiry_id" value="<?php echo htmlspecialchars($row['id']); ?>">
<input type="submit" value="Delete" class="button">
</form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<p style="text-align: center; color: white;">No inquiries found.</p>
<?php endif; ?>
</div>

</body>
</html>
