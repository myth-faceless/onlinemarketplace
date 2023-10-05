<?php
include('includes/config.php');
// Get the token from the URL query parameter
$token = $_GET['token'];


$query=mysqli_query($con,"UPDATE users SET email_verified = 1, email_verification_token = NULL WHERE email_verification_token = '$token'");
if($query)
{
    echo "<script>alert('Email has been successfully confirmed. You can now log in.')</script>";
} else {
    echo "<script>alert('Email Confirmation Failed, Please try again !')</script>";
}
header("location: login.php");
exit();
?>


