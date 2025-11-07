<?php 
// payment.php 
session_start();
if(empty($_SESSION['user_info'])){
    echo "<script type='text/javascript'>alert('Please login before proceeding!'); window.location.href='login.php';</script>";
    exit();
}

$conn = mysqli_connect("localhost","root","","railway");
if(!$conn){  
    echo "<script type='text/javascript'>alert('Database failed');</script>";
    die('Could not connect: '.mysqli_connect_error());  
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $name = mysqli_real_escape_string($conn, $_POST['name']); 
    $email = mysqli_real_escape_string($conn, $_POST['email']); 
    $amount = mysqli_real_escape_string($conn, $_POST['amount']); 
    $status = "Success"; 
 
    // Insert into database 
    $sql = "INSERT INTO payments (name, email, amount, status) VALUES ('$name', '$email', '$amount', '$status')"; 
    if (mysqli_query($conn, $sql)) { 
        header("Location: payment_success.php?name=" . urlencode($name) . "&amount=" . urlencode($amount)); 
        exit(); 
    } else { 
        echo "<script type='text/javascript'>alert('Error: " . mysqli_error($conn) . "');</script>"; 
    } 
} 
?> 
 
<!DOCTYPE html> 
<html> 
<head> 
    <title>Payment - Railway Reservation System</title> 
    <LINK REL="STYLESHEET" HREF="STYLE.CSS">
    <style type="text/css"> 
        body {
            font-family: Arial, sans-serif; 
            background: url(img/bg3.jpg) no-repeat center center fixed; 
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            color: white;
            margin: 0;
            padding: 0;
        }
        
        #payment-container {
            margin: auto;
            margin-top: 50px;
            width: 40%;
            padding: 40px;
            background-color: rgba(0,0,0,0.7);
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        
        h2 {
            text-align: center;
            color: white;
            font-family: "Comic Sans MS", cursive, sans-serif;
            margin-bottom: 30px;
            font-size: 32px;
        }
        
        form {
            background: rgba(255,255,255,0.1); 
            padding: 30px; 
            border-radius: 15px;
        }
        
        label {
            display: block;
            color: #ADD8E6;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        input[type=text], input[type=email], input[type=number] { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 20px; 
            border: 1px solid #ccc; 
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 15px;
            color: #333;
        }
        
        button { 
            background-color: #007BFF; 
            color: white; 
            border: none; 
            padding: 12px 25px; 
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        } 
        
        button:hover {
            background-color: #0056b3;
        }
        
        .payment-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .payment-icon img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #ADD8E6;
        }
    </style> 
</head> 
<body> 

<?php include('header.php'); ?>

<div id="payment-container">
    <div class="payment-icon">
        <img src="img/logo6.jpg" alt="Payment Icon">
    </div>
    
    <h2>Make a Payment</h2> 
 
    <form method="POST" onsubmit="return validatePayment()"> 
        <label>Name:</label> 
        <input type="text" name="name" id="name" placeholder="Enter your full name" required>
        
        <label>Email:</label> 
        <input type="email" name="email" id="email" placeholder="Enter your email address" required>
        
        <label>Amount (₹):</label> 
        <input type="number" name="amount" id="amount" placeholder="Enter amount" min="1" required>
        
        <button type="submit">Pay Now</button> 
    </form> 
</div>

<script type="text/javascript">
function validatePayment() {
    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var amount = document.getElementById("amount").value;
    
    if(name.trim() == "") {
        alert("Please enter your name");
        return false;
    }
    
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.length) {
        alert("Please enter a valid email address");
        return false;
    }
    
    if(amount <= 0) {
        alert("Please enter a valid amount");
        return false;
    }
    
    return true;
}
</script>
 
</body> 
</html>