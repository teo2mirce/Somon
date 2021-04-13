<?php
$dhost = "127.0.0.1";
$dusername = "";
$dpassword = "";
$ddatabase = "";

$ERR="";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$User = strtolower(test_input($_POST["User"]));//ca sa nu existe 1 l I si d-astea
if (!preg_match('/^[\w]+$/',$User)) 
  $ERR = "Invalid username"; 


$Email = test_input($_POST["Email"]);
if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) 
  $ERR = "Invalid email format"; 

if($ERR=="")
{
	
	$con = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die ("Cannot connect to the database"); 

	$Que="Select count(*) C from Users where Email='$Email' or Username='$User'";
	$res=mysqli_query($con,$Que);
	echo $con->error;
	if($res->fetch_assoc()["C"]!="0")
		echo "Username or email already exists";
	else
	{
		$Pass=substr(hash('ripemd160', "The quick brown $Email lazy dog."),0,10);
		$msg = "Your email: $Email\nYour username: $User\nYour password: $Pass";
		
		if(mail($Email,"Somon password",$msg))
		{	
			mysqli_query($con,"Insert into Users(Email,Password,Username) Values ('$Email','$Pass','$User')");
			echo "Email sent";
		}
		else
			echo "Error";
	}
	
}
else
	echo $ERR;

?>

