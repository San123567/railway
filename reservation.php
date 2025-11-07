<?php
// Include header
include 'header.php';

// Database connection
$servername = "localhost";
$username   = "root";   // change if needed
$password   = "";       // change if needed
$dbname     = "railway"; // import reservation.sql into this database

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div style='color:red;'>Connection failed: " . $conn->connect_error . "</div>");
}

// Handle form submission for new reservation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_reservation'])) {
    $passenger_name = $conn->real_escape_string($_POST['passenger_name']);
    $schedule_code  = $conn->real_escape_string($_POST['schedule_code']);
    $train_number   = $conn->real_escape_string($_POST['train_number']);
    $seat_number    = $conn->real_escape_string($_POST['seat_number']);
    $departure_date = $conn->real_escape_string($_POST['departure_date']);
    $departure_time = $conn->real_escape_string($_POST['departure_time']);
    $status         = $conn->real_escape_string($_POST['status']);

    $insert = "INSERT INTO reservation 
        (passenger_name, schedule_code, train_number, seat_number, departure_date, departure_time, status)
        VALUES ('$passenger_name', '$schedule_code', '$ train_number', '$seat_number', '$departure_date', '$departure_time', '$status')";
    
    if ($conn->query($insert) === TRUE) {
        echo "<p style='color:green;'>New reservation added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}

// Handle search
$search = "";
$whereClause = "";
if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = $conn->real_escape_string($_GET['search']);
    $whereClause = "WHERE passenger_name LIKE '%$search%' OR schedule_code LIKE '%$search%' OR train_number LIKE '%$search%'";
}

// Pagination
$limit = 5; // records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total records
$countSql = "SELECT COUNT(*) AS total FROM reservation $whereClause";
$countResult = $conn->query($countSql);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch reservations with search + pagination
$sql = "SELECT * FROM reservation $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservations</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px;
            background: url(img/bg1.jpg) no-repeat center center fixed;
            background-size: cover;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        .box {
            background: #fff; 
            border: 2px solid #ccc; 
            border-radius: 8px; 
            padding: 20px; 
            margin: 0 auto 20px auto; 
            max-width: 900px; /* 👈 shorter white area */
        }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f4f4f4; }
        tr:nth-child(even) { background: #fafafa; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }

        /* 👇 shrink input fields */
      form input, form select {
    margin: 5px 0;
    padding: 6px;
    width: 50%;        /* narrower */
    max-width: 850px;  /* tighter cap */
    border: 1px solid #ccc;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

        form button {
            display: block;
            margin: 10px auto 0 auto;
            padding: 8px 16px;
            border: none;
            background: #007BFF;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background: #0056b3;
        }

        .pagination { text-align: center; margin-top: 15px; }
        .pagination a { margin: 0 5px; padding: 8px 12px; border: 1px solid #ccc; background: #fff; text-decoration: none; }
        .pagination a.active { background: #007BFF; color: white    ; }
        
    </style>
</head>
<body>

<h2>Train Reservation</h2>

<!-- Search -->
<div class="box">
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search by passenger, schedule or train..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<!-- Add Reservation -->
<div class="box">
    <form method="post" action="">
        <h3></h3>
        <input type="text" name="passenger_name" placeholder="Passenger Name" required>
        <input type="text" name="schedule_code" placeholder="Schedule Code" required>
        <input type="text" name="train_number" placeholder="Train Number" required>
        <input type="text" name="seat_number" placeholder="Seat Number" required>
        <input type="date" name="departure_date" required>
        <input type="time" name="departure_time" required>
        <select name="status" required>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <button type="submit" name="add_reservation">Add Reservation</button>
    </form>
</div>

<!-- Reservations Table -->
<div class="box">
<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Passenger Name</th>
                <th>Schedule Code</th>
                <th>Train Number</th>
                <th>Seat Number</th>
                <th>Departure Date</th>
                <th>Departure Time</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        $statusClass = "status-" . strtolower($row['status']);
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['passenger_name']}</td>
                <td>{$row['schedule_code']}</td>
                <td>{$row['train_number']}</td>
                <td>{$row['seat_number']}</td>
                <td>{$row['departure_date']}</td>
                <td>{$row['departure_time']}</td>
                <td class='{$statusClass}'>{$row['status']}</td>
                <td>{$row['created_at']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No reservations found.</p>";
}
?>
</div>

<!-- Pagination -->
<div class="box pagination">
<?php
if ($totalPages > 1) {
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $page ? "class='active'" : "";
        echo "<a href='?page=$i&search=$search' $active>$i</a>";
    }
}
?>
</div>

</body>
</html>

<?php
$conn->close();
?>
