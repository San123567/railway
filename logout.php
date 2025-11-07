<?php
session_start();
session_destroy(); // Destroy all session data
echo "<script type='text/javascript'>alert('You have been logged out successfully!'); window.location.href='index.php';</script>";
exit();
?>
