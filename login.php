<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/phpmailer/src/Exception.php';
// require 'vendor/phpmailer/src/PHPMailer.php';
// require 'vendor/phpmailer/src/SMTP.php';

session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['submit']))
{
$name=$_POST['fullname'];
$email=$_POST['emailid'];
$contactno=$_POST['contactno'];
$password=($_POST['password']);
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$email_verification_token = md5(uniqid(rand(), true));

$query=mysqli_query($con,"insert into users(name,email,contactno,password,email_verification_token) values('$name','$email','$contactno','$hashedPassword','$email_verification_token')");
if($query)
 {

	$mail = new PHPMailer(true);

	try {
		// Configure the email settings (SMTP, sender, etc.)
		//Server settings
		$mail->SMTPDebug = 0;                     // Enable verbose debug output (set to 2 for debugging)
		$mail->isSMTP();                          // Set mailer to use SMTP
		$mail->Host       = 'smtp.gmail.com';   // Specify your SMTP server
		$mail->SMTPAuth   = true;                 // Enable SMTP authentication
		$mail->Username   = 'manishshrestha743@gmail.com'; // SMTP username
		$mail->Password   = 'tbes llxw hrio gdaa';       // SMTP password
		$mail->SMTPSecure = 'tls';                // Enable TLS encryption
		$mail->Port       = 587;                  // TCP port to connect to (587 for TLS)

		// Recipients
		$mail->setFrom('manishshrestha743@gmail.com', 'Online Marketplace');
		$mail->addAddress($email); // User's email

		// Content
		$mail->isHTML(true);
		$mail->Subject = 'Welcome to Online Marketplace!';
		$confirmationLink = 'http://localhost/omp/confirm-email.php?token=' . $email_verification_token; // Replace with your website's confirmation URL
		$mail->Body = "Please click the following link to confirm your email: <a href='$confirmationLink'>$confirmationLink</a>";

		// Send the email
		$mail->send();

		echo "<script>alert('A confirmation email has been sent to your email address. Please check your inbox and click the confirmation link to complete the registration.')</script>";
	} catch (Exception $e) {
		echo "<script>alert('Email could not be sent. Mailer Error: {$mail->ErrorInfo}')</script>";
	}
} else {
	echo "<script>alert('Registration Failed. Please try again later !')</script>";
}
// 	echo "<script>alert('You are successfully register. Please Sign in to continue shopping');</script>";
// }
// else{
// echo "<script>alert('Not register something went worng');</script>";
// }
//Code for updating email_confirmation after user click on confirmation link

}




// Code for User login
// if(isset($_POST['login']))
// {
//    $email=$_POST['email'];
//    $password=md5($_POST['password']);
// $query=mysqli_query($con,"SELECT * FROM users WHERE email='$email' and password='$password'");
// $num=mysqli_fetch_array($query);
// if($num>0)
// {
// $extra="my-cart.php";
// $_SESSION['login']=$_POST['email'];
// $_SESSION['id']=$num['id'];
// $_SESSION['username']=$num['name'];
// $uip=$_SERVER['REMOTE_ADDR'];
// $status=1;
// $log=mysqli_query($con,"insert into userlog(userEmail,userip,status) values('".$_SESSION['login']."','$uip','$status')");
// $host=$_SERVER['HTTP_HOST'];
// $uri=rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
// header("location:http://$host$uri/$extra");
// exit();
// }
// else
// {
// $extra="login.php";
// $email=$_POST['email'];
// $uip=$_SERVER['REMOTE_ADDR'];
// $status=0;
// $log=mysqli_query($con,"insert into userlog(userEmail,userip,status) values('$email','$uip','$status')");
// $host  = $_SERVER['HTTP_HOST'];
// $uri  = rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
// header("location:http://$host$uri/$extra");
// $_SESSION['errmsg']="Invalid email id or Password";
// exit();
// }
// }

if(isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password']; // Don't hash the password here

    // Prepare a SQL statement using a prepared statement to prevent SQL injection
    $stmt = $con->prepare("SELECT id, name, password, email_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashedPassword, $email_verified);
        $stmt->fetch();

        // Verify the provided password against the stored hashed password
        if (password_verify($password, $hashedPassword)) {
            if ($email_verified == 1) {
                // Password is correct, email is confirmed, and user exists

                // Start a session and set session variables
                session_start();
                $_SESSION['login'] = $email;
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $name;

                $uip = $_SERVER['REMOTE_ADDR'];
                $status = 1;

                // Insert a log record
                $log = $con->prepare("INSERT INTO userlog (userEmail, userip, status) VALUES (?, ?, ?)");
                $log->bind_param("ssi", $email, $uip, $status);
                $log->execute();
                
                header("location: index.php");
                exit();
            } else {
                // Email is not confirmed
                $extra = "login.php";
                $uip = $_SERVER['REMOTE_ADDR'];
                $status = 0;

                // Insert a log record
                $log = $con->prepare("INSERT INTO userlog (userEmail, userip, status) VALUES (?, ?, ?)");
                $log->bind_param("ssi", $email, $uip, $status);
                $log->execute();

                header("location: $extra");
                $_SESSION['errmsg'] = "Your email is not confirmed. Please check your inbox for the confirmation link.";
                exit();
            }
        }
    }

    // Invalid email or password
    $extra = "login.php";
    $uip = $_SERVER['REMOTE_ADDR'];
    $status = 0;

    // Insert a log record
    $log = $con->prepare("INSERT INTO userlog (userEmail, userip, status) VALUES (?, ?, ?)");
    $log->bind_param("ssi", $email, $uip, $status);
    $log->execute();

    header("location: $extra");
    $_SESSION['errmsg'] = "Invalid email id or Password";
    exit();
}


?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">

	    <title>Online Marketplace| Signin | Signup</title>

	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/black.css">
	    <link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<!--<link rel="stylesheet" href="assets/css/owl.theme.css">-->
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">

		<link rel="stylesheet" href="assets/css/config.css">

		<link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
		<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">

		
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">

		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">


<script>
function userAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'email='+$("#email").val(),
type: "POST",
success:function(data){
$("#user-availability-status1").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});

}
// function userAvailability() {
//     $("#loaderIcon").show();
//     var email = $("#email").val();
//     var contactno = $("#contactno").val();

//     jQuery.ajax({
//         url: "check_availability.php",
//         data: {
//             email: email,
//             contactno: contactno
//         },
//         type: "POST",
//         success: function(data) {
//             $("#user-availability-status").html(data);
//             $("#loaderIcon").hide();
//         },
//         error: function() {}
//     });
// }

</script>

<script>
function contactAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'contactno='+$("#contactno").val(),
type: "POST",
success:function(data){
$("#user-availability-status2").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});

}
</script>

<script type="text/javascript">
function valid()
{
if(document.register.fullname.value=="")
{
alert("Current Password Filed is Empty !!");
document.chngpwd.cpass.focus();
return false;
}
else if(document.chngpwd.newpass.value=="")
{
alert("New Password Filed is Empty !!");
document.chngpwd.newpass.focus();
return false;
}
else if(document.chngpwd.cnfpass.value=="")
{
alert("Confirm Password Filed is Empty !!");
document.chngpwd.cnfpass.focus();
return false;
}
else if(document.chngpwd.newpass.value!= document.chngpwd.cnfpass.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.chngpwd.cnfpass.focus();
return false;
}
return true;
}
</script>

<script type="text/javascript">

function validateName() {
        var fullName = document.getElementById("fullname").value;
        
        // Regular expression for valid name (letters and spaces, 2 to 50 characters)
        var namePattern = /^[A-Za-z\s]{2,50}$/;

        if (!namePattern.test(fullName)) {
            document.getElementById("name-validation-message").textContent = "Invalid name. Please enter a valid name (2-50 characters, letters and spaces only).";
            return false; // Prevent form submission
        }
        document.getElementById("name-validation-message").textContent = ""; // Clear validation message
        return true; // Allow form submission
    }

	function validateEmail() {
        var email = document.getElementById("email").value;
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

        if (!emailPattern.test(email)) {
            document.getElementById("email-validation-message").textContent = "But, Please enter a valid email.";
            return false; // Prevent form submission
        }
        document.getElementById("email-validation-message").textContent = ""; // Clear validation message
        return true; // Allow form submission
    }

	function validatePhoneNumber() {
		var phoneNumber = document.getElementById("contactno").value;
		var phonePattern = /^9\d{9}$/;;

		if (!phonePattern.test(phoneNumber)) {
			document.getElementById("contactno-validation-message").textContent = "Please enter a valid mobile no.";
			return false; // Prevent form submission
		}
		document.getElementById("contactno-validation-message").textContent = ""; // Clear validation message
		return true; // Allow form submission
	}

	function validatePasswordMatch() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmpassword").value;

        if (password !== confirmPassword) {
            document.getElementById("password-match-message").textContent = "Passwords do not match. Please re-enter.";
            return false; // Prevent form submission
        }

        // Clear the validation message if the passwords match
        document.getElementById("password-match-message").textContent = "";
        return true; // Allow form submission
    }
</script>

<script>
        // JavaScript to prevent form submission by default
        document.getElementById('register').addEventListener('submit', function (e) {
            // Check if the submit button was not clicked
            if (!e.explicitOriginalTarget || e.explicitOriginalTarget.id !== 'submitButton') {
                e.preventDefault(); // Prevent the form from submitting
                // alert('Please click the Submit button to submit the form.');
            }
        });

	window.onload = function() {
    document.forms["myForm"].reset(); // Replace "myForm" with the actual form name or ID
}

</script>


	</head>
    <body class="cnt-home">
	
		
	
<header class="header-style-1">

<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>

</header>

<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="home.html">Home</a></li>
				<li class='active'>Authentication</li>
			</ul>
		</div>
	</div>
</div>

<div class="body-content outer-top-bd">
	<div class="container">
		<div class="sign-in-page inner-bottom-sm">
			<div class="row">
				<!-- Sign-in -->			
<div class="col-md-6 col-sm-6 sign-in">
	<h4 class="">sign in</h4>
	<p class="">Hello, Welcome to your account.</p>
	<form class="register-form outer-top-xs" method="post">
	<span style="color:red;" >
<?php
echo htmlentities($_SESSION['errmsg']);
?>
<?php
echo htmlentities($_SESSION['errmsg']="");
?>
	</span>
		<div class="form-group">
		    <label class="info-title" for="exampleInputEmail1">Email Address <span>*</span></label>
		    <input type="email" name="email" class="form-control unicase-form-control text-input" id="exampleInputEmail1" >
		</div>
	  	<div class="form-group">
		    <label class="info-title" for="exampleInputPassword1">Password <span>*</span></label>
		 <input type="password" name="password" class="form-control unicase-form-control text-input" id="exampleInputPassword1" >
		</div>
		<div class="radio outer-xs">
		  	<a href="forgot-password.php" class="forgot-password pull-right">Forgot your Password?</a>
		</div>
	  	<button type="submit" class="btn-upper btn btn-primary checkout-page-button" name="login">Login</button>
	</form>					
</div>



<div class="col-md-6 col-sm-6 create-new-account">
	<h4 class="checkout-subtitle">create a new account</h4>
	<p class="text title-tag-line">Create your own Shopping account.</p>
	<form class="register-form outer-top-xs" role="form" method="post" name="register" id="register" onSubmit="return valid();">
    <div class="form-group">
    <label class="info-title" for="fullname">Full Name <span>*</span></label>
    <input type="text" class="form-control unicase-form-control text-input" id="fullname" name="fullname" onblur="validateName()" required="required">
    <span id="name-validation-message" style="font-size:12px;"></span>
</div>
	

	<div class="form-group">
        <label class="info-title" for="exampleInputEmail2">Email Address <span>*</span></label>
        <input type="email" class="form-control unicase-form-control text-input" id="email" onblur="userAvailability(); validateEmail();" name="emailid" required>
        <span id="user-availability-status1" style="font-size:12px;"></span><br>
		<span id="email-validation-message" style="font-size:12px;"></span>
    </div>

   

    <div class="form-group">
        <label class="info-title" for="contactno">Mobile No. <span>*</span></label>
        <input type="text" class="form-control unicase-form-control text-input" id="contactno" name="contactno" onblur="validatePhoneNumber(); contactAvailability();" maxlength="10" required>
        <span id="user-availability-status2" style="font-size:12px;"></span><br>
		<span id="contactno-validation-message" style="font-size:12px;"></span>
    </div>

	<div class="form-group">
        <label class="info-title" for="password">Password <span>*</span></label>
        <input type="password" class="form-control unicase-form-control text-input" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label class="info-title" for="confirmpassword">Confirm Password <span>*</span></label>
        <input type="password" class="form-control unicase-form-control text-input" id="confirmpassword" name="confirmpassword" onblur="validatePasswordMatch()" required>
        <span id="password-match-message" style="font-size:12px;"></span>
    </div>
	

    <button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button" id="submit">Sign Up</button>
</form>


	<span class="checkout-subtitle outer-top-xs">Sign Up Today And You'll Be Able To :  </span>
	<div class="checkbox">
	  	<label class="checkbox">
		  	Speed your way through the checkout.
		</label>
		<label class="checkbox">
		Track your orders easily.
		</label>
		<label class="checkbox">
 Keep a record of all your purchases.
		</label>
	</div>
</div>	
			</div>
		</div>

</div>
</div>
<?php include('includes/footer.php');?>
	<script src="assets/js/jquery-1.11.1.min.js"></script>
	
	<script src="assets/js/bootstrap.min.js"></script>
	
	<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>
	
	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="assets/js/jquery.rateit.min.js"></script>
    <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>

	
	<script src="switchstylesheet/switchstylesheet.js"></script>
	
	<script>
		$(document).ready(function(){ 
			$(".changecolor").switchstylesheet( { seperator:"color"} );
			$('.show-theme-options').click(function(){
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>

	

</body>
</html>