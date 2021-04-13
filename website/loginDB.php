<?php

$dhost     = "127.0.0.1";
$dusername = "";
$dpassword = "";
$ddatabase = "";

function Q(&$con,$q) {
    $res = mysqli_query($con,$q);
	if(mysqli_error($con)!="")
	{
		echo "QUERIUL $q a DAT: ".mysqli_error($con);
	}
	return $res;
}
function GetSingleValue(&$con,$String) 
{
	$res = Q($con,$String);
	while($data = mysqli_fetch_assoc($res))
		foreach($data as $key => $value)
			return $value;
	return "?";
}

session_start(); // Starting Session

$con = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die("Cannot connect to the database");
$error = ''; // Variable To Store Error Message
if (isset($_POST['submit']) && isset($_SESSION["UID"])==false)
{
	// Define $username and $password
	$username = $_POST['Email'];
	$password = $_POST['Password'];
	// Establishing Connection with Server by passing server_name, user_id and password as a parameter
	$con = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die("Cannot connect to the database");
	// To protect MySQL injection for Security purpose
	$username = stripslashes($username);
	$password = stripslashes($password);
	$username = mysqli_real_escape_string($con,$username);
	$password = mysqli_real_escape_string($con,$password);
	
	$res = Q($con,"SELECT ID,SC,Username from Users WHERE Email='$username' AND Password = '$password' ");
	// SQL query to fetch information of registerd users and finds user match.
	
	$rows     = mysqli_num_rows($res);
	echo $rows;
	if ($rows != 0)
	{
		// $_SESSION['login_user'] = $username; // Initializing Session
		$data = mysqli_fetch_assoc($res);
		$_SESSION['login_user'] = $data["Username"]; // Initializing Session
		$_SESSION['UID'] = $data["ID"]; // User ID
		
		
		$_SESSION['Email'] = $_POST['Email']; // User ID
		$_SESSION['Password'] = $_POST['Password']; // User ID
		//$_SESSION['USC'] = $data["SC"]; // USer DC
		
		$Restriction=array();
		$ToolTip=array();
		
		
					
		$_SESSION["Restriction"]=$Restriction;
		$_SESSION["ToolTip"]=$ToolTip;
		
		
		header("location: index.php"); // Redirecting To Other Page
	}
	else
	{
		$error = "Username or Password is invalid";
		// $error = "SELECT ID,SC,Username from Users WHERE Email='$username' AND Password = '$password' ";
	}
	mysqli_close($con); // Closing Connection
}

?>