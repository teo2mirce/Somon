<?php

// echo 'Plus
// Int a,Int b
// Int r
// Minus
// Int a,Int b
// Int r';

//header('Access-Control-Allow-Origin: *'); 
$dhost = "127.0.0.1";
$dusername = "";
$dpassword = "";
$ddatabase = "";

$con = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die ("Cannot connect to the database"); 

	$Que="Select ID,Name,InVar,OutType,Dependency from PublicFunctions";
	
	if (!$result = $con->query($Que))
	{
		echo "Sorry, the website is experiencing problems.";
		exit;
	}

	$i=0;
	while ($line = $result->fetch_assoc())
	{
		if($i!=0)
			echo ";";
		$i++;
		echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'].'_'.$line['Dependency'];
	}

?>

