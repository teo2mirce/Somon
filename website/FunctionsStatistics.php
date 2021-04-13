 <?php  
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;

 $QUE ="
		SELECT Type,concat(Name,' (',FU.ID,')') Name,Nr FROM 
		FunctionUsage FU
		join 
		DefaultFunctions DF
		on(FU.Type='Default' and FU.ID=DF.ID)

		UNION

		SELECT Type,concat(Name,'(',FU.ID,')') Name,Nr FROM 
		FunctionUsage FU
		join 
		PublicFunctions PF
		on(FU.Type='Public' and FU.ID=PF.ID)

		order by Nr desc
		";

 $connect = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die ("Cannot connect to the database");  
 $result = mysqli_query($connect, $QUE);  
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
	  
	  
	  <?php include('PhpInclude/CreateTable.php');  ?>
	  
     
    </div>
	
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	  
	  
	
	<script>
	function L(data,str){return data[Col2Index[str]];}
	$('#dataTable').on('click', 'tr', function () {
		
	});

	$( document ).ready(function() {
	
			
		
	});
	</script>
  </div>
  
  

	
	  <?php include('PhpInclude/tail.php');  ?>
</body>

</html>
