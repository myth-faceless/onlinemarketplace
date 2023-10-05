<?php 
require_once("includes/config.php");
if(!empty($_POST["email"])) {
	$email= $_POST["email"];
	
		$result =mysqli_query($con,"SELECT  email FROM  users WHERE  email='$email'");
		$count=mysqli_num_rows($result);
if($count>0)
{
echo "<span style='color:red'> Email already exists .</span>";
 echo "<script>$('#submit').prop('disabled',true);</script>";
} else{
	
	echo "<span style='color:green'> Email available. .</span>";
 echo "<script>$('#submit').prop('disabled',false);</script>";
}
}

?>

<?php 
// require_once("includes/config.php");
if(!empty($_POST["contactno"])) {
	$contactno= $_POST["contactno"];
	
		$res =mysqli_query($con,"SELECT  contactno FROM  users WHERE  contactno='$contactno'");
		$count=mysqli_num_rows($res);
if($count>0)
{
echo "<span style='color:red'> Mobile no already exists .</span>";
 echo "<script>$('#submit').prop('disabled',true);</script>";
} else{
	
	echo "<span style='color:green'> Mobile no available to register. .</span>";
 echo "<script>$('#submit').prop('disabled',false);</script>";
}
}

?>