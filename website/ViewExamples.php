 <?php  
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='F';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;

$TruncateInstance=GetSingleValue($con,"Select TruncateInstance from Users where ID=".$_SESSION['UID']);

 $OutValueQue="OutValue";
 if($TruncateInstance=="Yes")
	 $OutValueQue="LEFT(OutValue,50) OutValue";

///fara (state si timestmap si name) done fata de instances
 $QUE ="select T.ID,T.InVar,$OutValueQue,ROUND(Duration/1000,3) 'Duration (s)',T.F from Instances T join PublicFunctions PF on(T.F=PF.ID) where T.ID in (select InstanceID from Examples where PublicID=".$_GET["F"].")  order by T.ID desc";
 


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
	<button type="button" class="btn btn-info" id="ViewState">View end state</button>
	<button type="button" class="btn btn-info" id="Download">Download out files</button>
	  
	  <script>
		function L(data,str){return data[Col2Index[str]];};
		
		
		$("#ViewState").click(function()
		{
			var data = $('#dataTable').DataTable().row( '.selected' ).data();
			if(data==undefined)return;
			
			var Link="ViewDoneInstance.php?ID="+L(data,"ID");
			console.log(Link);
			
			$('#Framed').attr('src',Link);  
			$('#IframeModal').modal('show');
		});
		$("#Download").click(function()
		{
			var data = $('#dataTable').DataTable().row( '.selected' ).data();
			if(data==undefined)return;
			//if(L(data,"State")=="Done")
				var win = window.open("phps/FilesOnTheFly.php?ID="+L(data,"ID"), '_blank');
		});
		
	  </script>
	  
	  <?php include('PhpInclude/CreateTable.php');  ?>
	  
     
    </div>
	
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	  
	  
	
	<script>
	
	function L(data,str)
	{
		return data[Col2Index[str]];
	}
	$('#dataTable').on('click', 'tr', function () {
		
		
        // var data = $('#dataTable').DataTable().row( this ).data();
		// if(data==undefined)return;
		
		
		// //Era:
		// if(L(data,"State")=="Done")
			// var win = window.open("phps/FilesOnTheFly.php?ID="+L(data,"ID"), '_blank');
		
		
		
		///sa vezi
		// var Link="ViewDoneInstance.php?ID="+L(data,"ID");
		// console.log(Link);
		
		// $('#Framed').attr('src',Link);  
		// $('#IframeModal').modal('show');
    } );
	

	$( document ).ready(function() {
		
		//var Cols = ["Value"]; 
		//$('#dataTable').DataTable().on('search.dt', function() { Total(Cols); });
		//Total(Cols);
			
		
	});
	</script>
  </div>
  
  

	
	  <?php include('PhpInclude/tail.php');  ?>
</body>

</html>
