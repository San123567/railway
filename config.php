<?php
$conn = mysqli_connect("localhost", "root", "", "railway");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
