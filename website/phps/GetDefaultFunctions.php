<?php

require '../loginDB.php'; // Includes Login Script

	$Que="Select ID,Name,InVar,OutType from DefaultFunctions";
	
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
		echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
	}

?>

