 <?php  
 
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;


 ?>  
 
<!DOCTYPE html>


<html lang="en">

<?php include('PhpInclude/head.php');  ?>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  
	<?php include('PhpInclude/NavbarInclude.php');  ?>
	
  
  <div class="content-wrapper">
    <div class="container-fluid">
	  <!-- Butoane 
	  
		<input id="Refresh" type="image" src="res//refresh-n.png" />
		<input id="Export" type="image" src="res//export-n.png" />
		<input id="Help" type="image" src="res//help-n.png" />
		<input id="Add" type="image" src="res//add-n.png" />
		<input id="Edit" type="image" src="res//edit-n.png" />
		<input id="Delete" type="image" src="res//delete-n.png" />
	  -->
	  
	  
	  <?php //include('PhpInclude/CreateTable.php');  ?>
	  
	 <div class="card mb-3">
			
		<div class="card-header">
			View public functions / classes
		</div>
		  
		  <?php
			$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=".$_SESSION['UID']);
			$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=".$_SESSION['UID']);
			$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=".$_SESSION['UID']);
		  ?>
		  
		<div class="card-body">
			
			<form action="phps/Operations.php" method="post" enctype="multipart/form-data" target="som" >
			<div class="form-group">
				
				
				<label id="LShow" for="ShowAllMine">Show:</label>
				<select name="ShowAllMine" class="form-control" id="ShowAllMine">
					<option <?php echo ($ShowAllMine=="All")?"selected":""; ?> >All</option>
					<option <?php echo ($ShowAllMine=="Mine")?"selected":""; ?> >Mine</option>
				</select>
				
				<label for="ShowSC" class="col-form-label">With at least (SC):</label>
				<input name="ShowSC" type="number" step="0.001" value="<?php echo $ShowSC ?>" class="form-control" id="ShowSC"/>
				<br>
				OR
				<br>
				<label for="ShowMineHours" class="col-form-label">Mine created in the last (hours):</label>
				<input name="ShowMineHours" type="number"  value="<?php echo $ShowMineHours ?>" class="form-control" id="ShowMineHours"/>
				
			</div>
			 <input type="hidden" value="SettingsShow" name="Operation">
			 <input type="submit" class="btn btn-primary" value="Submit" name="submit">
			</form>
		</div>
		
	</div>
	
	
	 <div class="card mb-3">
		
		<div class="card-header">
			Instances
		</div>
		  
		  <?php
			$SolveInstancesFor=GetSingleValue($con,"Select SolveInstancesFor from Users where ID=".$_SESSION['UID']);
			$AddInstancesFor=GetSingleValue($con,"Select AddInstancesFor from Users where ID=".$_SESSION['UID']);
			$TruncateInstance=GetSingleValue($con,"Select TruncateInstance from Users where ID=".$_SESSION['UID']);
		  ?>
		  
		<div class="card-body">
			
			<form action="phps/Operations.php" method="post" enctype="multipart/form-data" target="som" >
			<div class="form-group">
				
				<label for="AddInstancesFor">Add instances to be solved by:</label>
				<select name="AddInstancesFor" class="form-control" id="AddInstancesFor">
					<option <?php echo ($AddInstancesFor=="Me")?"selected":""; ?> >Me</option>
					<option <?php echo ($AddInstancesFor=="All")?"selected":""; ?> >All</option>
				</select>
				<br>
				<label for="SolveInstancesFor">Solve instances for:</label>
				<select name="SolveInstancesFor" class="form-control" id="SolveInstancesFor">
					<option <?php echo ($SolveInstancesFor=="Me")?"selected":""; ?> >Me</option>
					<option <?php echo ($SolveInstancesFor=="All")?"selected":""; ?> >All</option>
				</select>
				<br>
				<label for="TruncateInstance">Truncate output of instances to 50:</label>
				<select name="TruncateInstance" class="form-control" id="TruncateInstance">
					<option <?php echo ($TruncateInstance=="Yes")?"selected":""; ?> >Yes</option>
					<option <?php echo ($TruncateInstance=="No")?"selected":""; ?> >No</option>
				</select>
				
				<!--
				<br>
				<label id="LAllowFunctions" for="AllowFunctions">Allow only:</label>
				<select multiple name="AllowFunctions" class="form-control" id="AllowFunctions" style='overflow:hidden'>
				
				<?php
				// $Que="Select ID,Name,InVar,OutType from DefaultFunctions";
	
				// if (!$result = $con->query($Que))
					// die ("Sorry, the website is experiencing problems.");
				// while ($L = $result->fetch_assoc())
					// echo "<option value=".$L["ID"]." >".$L["Name"]."</option>";
				?>
				
				</select>
				!-->
			</div>
			 <input type="hidden" value="SettingsInstances" name="Operation">
			 <input type="submit" class="btn btn-primary" value="Submit" name="submit">
			</form>
		</div>
	  
     </div>
	  
	  

     
    </div>
	
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	  
	  
	
	<script>


	$( document ).ready(function() {
	
	   // $("#AllowFunctions").attr("size",$("#AllowFunctions option").length);
		// function SolveInstancesForChanged()
		// {
			// var Type=$("#SolveInstancesFor :selected").text();
			// $("#AllowFunctions").hide();$("#LAllowFunctions").hide();
			// if(Type=="All")
			// {
				// $("#AllowFunctions").show();$("#LAllowFunctions").show();
			// }
		// };
		// SolveInstancesForChanged();
		// $( "#SolveInstancesFor" ).change(function() {SolveInstancesForChanged();});
		
	});
	</script>
  </div>
  
  

	
<iframe style="display:none;" name="som" onLoad=""></iframe>

	  <?php include('PhpInclude/tail.php');  ?>
</body>

</html>
