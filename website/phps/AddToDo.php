<?php
require '../loginDB.php'; // Includes Login Script
//header('Access-Control-Allow-Origin: *'); 

$InVar="";
foreach($_POST as $i => $stuff)
if (strpos($i, '_') !== false)
{
  $NewI=str_replace("_"," ",$i);
  $NewI=str_replace("(","[",$NewI);
  $NewI=str_replace(")","]",$NewI);
  $InVar.="$NewI=$stuff\n";
}
$UserID=$_SESSION["UID"];
foreach($_FILES as $i => $stuff)
{
	$DATA = mysqli_real_escape_string($con,file_get_contents($_FILES[$i]['tmp_name']));

	$Name=$_FILES[$i]['name'];
	$Que="INSERT INTO BlobFiles(BlobFile, FileName,UserID) VALUES ('$DATA','$Name',$UserID)";
	mysqli_query($con,$Que);
	
	
	if($con->error!="")
		die($con->error);
	else
	{
		$NewI=str_replace("_"," ",$i);
		$InVar.="$NewI=".$con->insert_id."\n";
	}
}
	
	$ForUser=GetSingleValue($con,"SELECT IF(AddInstancesFor='Me',ID,0) FROM Users where ID=$UserID");
	
	if($ForUser=="0")//1 sc ca sa pui public
	{
		$SECE=GetSingleValue($con,"select floor(SC) from Users where ID=$UserID");
		if($SECE>=1)
			mysqli_query($con,"Update Users set SC=SC-1 where ID=$UserID");
		else
			die("You need at least 1 SC to use public instances");
	}
	
	$Que="INSERT INTO Instances(InVar, OutValue, F, State,Starter,ForUser) VALUES ('$InVar','',".$_POST["function"].",'Idle',  $UserID , $ForUser)";
	mysqli_query($con,$Que);
	if($con->error!="")
		die($con->error);
	else
		echo "Added, instance id: ".$con->insert_id;
?>

