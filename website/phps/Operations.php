<?php
require '../loginDB.php'; // Includes Login Script
//header('Access-Control-Allow-Origin: *'); 


$UserID=$_SESSION["UID"];



if($_POST["Operation"]=="AddClass")//ViewClasses.php
{
	//furat de la GetVariables
	$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=$UserID");
	$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=$UserID");
	$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=$UserID");

	$Clas="Select concat(Name,'-',ID) Name from Classes
	 
	 where 
	 ((('$ShowAllMine'='All') or ('$ShowAllMine'='Mine' and UserID=$UserID)) and SC>=$ShowSC)
	 or
	 (UserID=$UserID and TimestampAdd >= DATE_SUB(NOW(),INTERVAL $ShowMineHours HOUR))
	 
	 ";
	 
	 
	$Que="SELECT GROUP_CONCAT(Name) Types
		  FROM
		  (
			select Name from VariableTypes
			union
			($Clas)
		  ) A";
	
	if (!$result = $con->query($Que))
		die("Sorry, the website is experiencing problems.");
	
	$line = $result->fetch_assoc();
	$AllVariables=explode(',',$line["Types"]);
	//End of furat
	
	
	
	
	
	if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*?$/',$_POST["Name"])) 
		die("Invalid class name"); 

	 //die(GetAllVariables());
	 //$AllVariables=explode(' ',GetAllVariables());
	
	$InVar="";
	for($i=1;$i<=9;$i++)
	{
		if($_POST["Field$i"]=="")
			$i=10;
		else//tre verificat ca e bun ce e acolo: tip de date valid (type sau class) si dupa spatiu sa fie ceva ca o variabila
		{			
			
			
			
			if(substr_count($_POST["Field$i"],' ')!=1)///sa fie un singur ' '
				die("invalid field name: ".$_POST["Field$i"]);
			
			
			$FieldType=explode(' ',$_POST["Field$i"])[0];
			if (!in_array($FieldType,$AllVariables))
				die("Invalid field type: $FieldType"); 
			
			
			$FieldName=explode(' ',$_POST["Field$i"])[1];
			if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*?$/',$FieldName))
				die("Invalid field name: $FieldName"); 
			
			$InVar.=$_POST["Field$i"].",";
		}
	}
	$InVar = rtrim($InVar, ',');
	if(substr_count($InVar,",")<1)//cel putin 2 var, intre 2 zile nu-i decat o noapte
		die("You need at least 2 fields");
	$Que="INSERT INTO Classes(Name,UserID, InVar) VALUES ('".$_POST["Name"]."',$UserID,'$InVar'   )";
	mysqli_query($con,$Que);
	if($con->error!="")
		die($con->error);
	echo "Added";
}
if($_POST["Operation"]=="AddUserFunction")//code.js
{
	$OutType=substr($_POST["Out"], 0, strpos($_POST["Out"], ' '));
	
	$Json=str_replace("'", "\'", json_encode($_POST['Q']));
	
	$Dependency=$_POST["Dependency"];
	
	$Que="INSERT INTO PublicFunctions(Name, Description,UserID, InVar, OutVar,OutType,JsonString,Dependency) VALUES ('".$_POST["Name"]."','".$_POST["Description"]."',".$_SESSION["UID"].",'".$_POST["In"]."','".$_POST["Out"]."','$OutType','".$Json."','$Dependency')";
	mysqli_query($con,$Que);
	echo $con->error;
}


if($_POST["Operation"]=="GetVariables")//code.js
{
	$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=$UserID");
	$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=$UserID");
	$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=$UserID");

	$Clas="Select concat(Name,'-',ID) Name from Classes
	 
	 where 
	 ((('$ShowAllMine'='All') or ('$ShowAllMine'='Mine' and UserID=$UserID)) and SC>=$ShowSC)
	 or
	 (UserID=$UserID and TimestampAdd >= DATE_SUB(NOW(),INTERVAL $ShowMineHours HOUR))
	 
	 ";
	 
	 
	$Que="SELECT GROUP_CONCAT(Name) Types
		  FROM
		  (
			select Name from VariableTypes
			union
			($Clas)
		  ) A";
	
	if (!$result = $con->query($Que))
		die("Sorry, the website is experiencing problems.");
	
	$line = $result->fetch_assoc();
	echo $line["Types"];
}
if($_POST["Operation"]=="AddRequest")//viewRequests.php
{
	$Que="INSERT INTO Requests(Type, Name, Description,User) VALUES ('".$_POST["Type"]."','".$_POST["Name"]."','".$_POST["Description"]."',$UserID       )";
	mysqli_query($con,$Que);
	if($con->error!="")
		die($con->error);
	else
		echo "Added, instance id: ".$con->insert_id;
}
if($_POST["Operation"]=="SponsorRequest")//viewRequests.php
{
	$SC=GetSingleValue($con,"select least(1,SC) from Users where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Users set SC=SC-$SC where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Requests set SC=SC+$SC where ID=".$_POST["ID"]);
	if($con->error!="")die($con->error);
}
if($_POST["Operation"]=="SponsorUser")//Users.php
{
	if($_POST["ID"]==$UserID)
		die("You dense...");
	$SC=GetSingleValue($con,"select least(1,SC) from Users where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Users set SC=SC-$SC where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Users set SC=SC+$SC where ID=".$_POST["ID"]);
	if($con->error!="")die($con->error);
}
if($_POST["Operation"]=="SponsorClass")//ViewClasses.php
{
	$SC=GetSingleValue($con,"select least(1,SC) from Users where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Users set SC=SC-$SC where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Classes set SC=SC+$SC where ID=".$_POST["ID"]);
	if($con->error!="")die($con->error);
}
if($_POST["Operation"]=="DoneRequest")//viewRequests.php
{
	$Que="update Requests set State= 'Done' where User=$UserID and ID= ".$_POST["ID"];
	mysqli_query($con,$Que);
	if($con->error!="")die($con->error);
	else
		echo "";
}

if($_POST["Operation"]=="AddAutomatedInstance")//ViewAutomatedInstances.php
{
	$Que="INSERT INTO AutomatedInstances(Type, UserID, Function) VALUES ('".$_POST["Type"]."',$UserID,".$_POST["function"]."  )";
	mysqli_query($con,$Que);
	if($con->error!="")
		die($con->error);
	else
		echo "Added, instance id: ".$con->insert_id;
}
if($_POST["Operation"]=="DeleteAutomatedInstance")//ViewAutomatedInstances.php
{
	$Que="delete from AutomatedInstances where ID= ".$_POST["ID"];
	mysqli_query($con,$Que);
	if($con->error!="")
		die($con->error);
	else
		echo "Added, instance id: ".$con->insert_id;
}
	
	
	
if($_POST["Operation"]=="GetDoneJson")//ViewDone.js
{
	$Que="select EdgeSizes from Instances where ID=".$_POST["ID"];
	echo $con->query($Que)->fetch_object()->EdgeSizes; 
	if($con->error!="")die($con->error);
}
if($_POST["Operation"]=="GetClasses") //code.js
{
	$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=$UserID");
	$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=$UserID");
	$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=$UserID");

	$Que="Select ID,concat(Name,'-',ID) Name,InVar,concat(Name,'-',ID) OutType from Classes
	 
	 where 
	 ((('$ShowAllMine'='All') or ('$ShowAllMine'='Mine' and UserID=$UserID)) and SC>=$ShowSC)
	 or
	 (UserID=$UserID and TimestampAdd >= DATE_SUB(NOW(),INTERVAL $ShowMineHours HOUR))
	 
	 ";
	
	if (!$result = $con->query($Que))
		die("Sorry, the website is experiencing problems: $Que");

	if($con->error!="")die($con->error);
	$i=0;
	while ($line = $result->fetch_assoc())
	{
		if($i!=0)
			echo ";";
		$i++;
		echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
	}
}
if($_POST["Operation"]=="GetPublicFunctionsWithJson")//ViewPublicFunctions.php
{
	$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=$UserID");
	$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=$UserID");
	$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=$UserID");

	$Que="Select ID,Name,JsonString from PublicFunctions
	
	 where 
	 ((('$ShowAllMine'='All') or ('$ShowAllMine'='Mine' and UserID=$UserID)) and SC>=$ShowSC)
	 or
	 (UserID=$UserID and TimestampAdd >= DATE_SUB(NOW(),INTERVAL $ShowMineHours HOUR))
	 
	 order by ID Desc
	 ";
	
	if (!$result = $con->query($Que))
		die("Sorry, the website is experiencing problems: $Que");

	if($con->error!="")die($con->error);
	$i=0;
	while ($line = $result->fetch_assoc())
	{
		if($i!=0)
			echo ";";
		$i++;
		echo $line['ID'].'_'.$line['Name'].'_'.$line['JsonString'];
	}
}
if($_POST["Operation"]=="GetPublicFunctions")//addinstance.php
{
	$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=$UserID");
	$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=$UserID");
	$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=$UserID");

	$Que="Select ID,Name,InVar,OutType,Dependency from PublicFunctions
	
	 where 
	 ((('$ShowAllMine'='All') or ('$ShowAllMine'='Mine' and UserID=$UserID)) and SC>=$ShowSC)
	 or
	 (UserID=$UserID and TimestampAdd >= DATE_SUB(NOW(),INTERVAL $ShowMineHours HOUR))
	 
	 order by SC desc,ID desc
	 ";
	
	if (!$result = $con->query($Que))
		die("Sorry, the website is experiencing problems: $Que");

	if($con->error!="")die($con->error);
	$i=0;
	while ($line = $result->fetch_assoc())
	{
		if($i!=0)
			echo ";";
		$i++;
		echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'].'_'.$line['Dependency'];
	}
}
if($_POST["Operation"]=="SettingsInstances")
{
	$SolveInstancesFor=$_POST["SolveInstancesFor"];
	$AddInstancesFor=$_POST["AddInstancesFor"];
	$TruncateInstance=$_POST["TruncateInstance"];
	mysqli_query($con,"Update Users set SolveInstancesFor='$SolveInstancesFor',AddInstancesFor='$AddInstancesFor',TruncateInstance='$TruncateInstance' where ID=$UserID");
	if($con->error!="")die($con->error);
}
if($_POST["Operation"]=="SettingsShow")
{
	$ShowAllMine=$_POST["ShowAllMine"];
	$ShowSC=$_POST["ShowSC"];
	$ShowMineHours=$_POST["ShowMineHours"];
	mysqli_query($con,"Update Users set ShowAllMine='$ShowAllMine',ShowSC=$ShowSC,ShowMineHours=$ShowMineHours where ID=$UserID");
	if($con->error!="")die($con->error);
}
if($_POST["Operation"]=="GetFunctionJson")//code.js   View.js    ViewDone.js
{
	$Que="select JsonString from PublicFunctions where ID=".$_POST["AIDI"];
	
	if($con->error!="")
		echo "ERROR";
	
	echo $con->query($Que)->fetch_object()->JsonString; 
}
if($_POST["Operation"]=="SupportFunction")
{
	$SC=GetSingleValue($con,"select least(1,SC) from Users where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Users set SC=SC-$SC where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update PublicFunctions set SC=SC+$SC where ID=".$_POST["ID"]);
	if($con->error!="")die($con->error);
	echo "Done";
}
if($_POST["Operation"]=="MakeExample")
{
	$Que="INSERT INTO Examples(PublicID, InstanceID) VALUES ('".$_POST["PublicID"]."','".$_POST["InstanceID"]."'     )";
	mysqli_query($con,$Que);
	if($con->error!="")
		echo "You already did this";
		//die($con->error);
	else
		echo "Done";
}
///Requests
if($_POST["Operation"]=="ProposeRequestSolution")
{
	$Que="INSERT INTO RequestsSolutions(RequestID, FunctionID) VALUES ('".$_POST["RequestID"]."','".$_POST["FunctionID"]."'     )";
	mysqli_query($con,$Que);
	if($con->error!="")
		echo "Someone already did this";
		//die($con->error);
	else
		echo "Done";
}
if($_POST["Operation"]=="SupportRequestSolution")
{
	$SC=GetSingleValue($con,"select least(1,SC) from Users where ID=$UserID");
	// echo "Update RequestsSolutions set SC=SC+$SC where FunctionID=".$_POST["FunctionID"]." and RequestID=".$_POST["RequestID"];
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update Users set SC=SC-$SC where ID=$UserID");
	if($con->error!="")die($con->error);
	mysqli_query($con,"Update RequestsSolutions set SC=SC+$SC where FunctionID=".$_POST["FunctionID"]." and RequestID=".$_POST["RequestID"]);
	if($con->error!="")die($con->error);
	echo "Done";
}
?>

