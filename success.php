<?php 
session_start();

// Check if the success parameter is set to true in the URL
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    include('includes/config.php'); // Assuming this is where your database connection is set up

    // Update the paymentMethod in the orders table to "eSewa"
    $user_id = $_SESSION['id'];
    $sql = "UPDATE orders SET paymentMethod='eSewa', paymentStatus = '1' WHERE userId='$user_id' AND orderStatus IS NULL";

    if (mysqli_query($con, $sql)) {
        echo "Payment method updated successfully.";
		header('location:order-history.php');
        
    } else {
        echo "Error updating payment method: " . mysqli_error($con);
    }
}

// Rest of your code...
