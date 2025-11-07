<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <style>
        body {text-align:center; font-family:Arial; background-color:#e7f7e9;}
        .success-box {
            background:white; display:inline-block; padding:30px; border-radius:8px; margin-top:60px;
            box-shadow:0 0 10px rgba(0,0,0,0.2);
        }
        h2 {color:green;}
    </style>
</head>
<body>

<div class="success-box">
    <h2>✅ Payment Successful!</h2>
    <p>Thank you, <strong><?php echo htmlspecialchars($_GET['name']); ?></strong></p>
    <p>Amount Paid: ₹<?php echo htmlspecialchars($_GET['amount']); ?></p>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
