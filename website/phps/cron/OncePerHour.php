<?php

//echo 'Int,Double,String,Bool';

$dhost = "127.0.0.1";
$dusername = "";
$dpassword = "";
$ddatabase = "";

$con = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die ("Cannot connect to the database"); 

	$Que="INSERT INTO Instances(InVar, OutValue, F, State,Starter) 
			select '','',Function,'Idle',UserID 
			from AutomatedInstances where Type='1 hour'";
	mysqli_query($con,$Que);
?>