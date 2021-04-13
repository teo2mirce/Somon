 <?php  
 
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;

$UserID=$_SESSION["UID"];
$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=$UserID");
$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=$UserID");
$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=$UserID");

 $QUE =
	 "
		SELECT C.ID,C.Name,C.InVar,U.Username,C.SC from Classes C join Users U on (C.UserID=U.ID)
		
		
		 where 
		 ((('$ShowAllMine'='All') or ('$ShowAllMine'='Mine' and C.UserID=$UserID)) and C.SC>=$ShowSC)
		 or
		 (C.UserID=$UserID and TimestampAdd >= DATE_SUB(NOW(),INTERVAL $ShowMineHours HOUR))
		 
		 order by C.SC desc,C.ID desc
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
	  
	<button type="button" class="btn btn-primary" id="AddClass">Add class</button>
	<button type="button" class="btn btn-danger" id="Sponsor">Send 1 SC</button>

	  <script>
		function L(data,str){return data[Col2Index[str]];};

	  $("#AddClass").click(function()
		{
			$("#exampleModalLongTitle").html("Pick options");
			
			$("#exampleModalCenter .modal-body").html('\
			<form action="/phps/Operations.php" method="post" enctype="multipart/form-data" target="som">\
			<div class="form-group">\
				\
				<label for="Name" class="col-form-label">Name:</label>\
				<input name="Name" class="form-control" id="Name"/>\
				\
				Fields:\
				\
				<input name="Field1" class="form-control" id="Field1"/>\
				<input name="Field2" class="form-control" id="Field2"/>\
				<input name="Field3" class="form-control" id="Field3"/>\
				<input name="Field4" class="form-control" id="Field4"/>\
				<input name="Field5" class="form-control" id="Field5"/>\
				<input name="Field6" class="form-control" id="Field6"/>\
				<input name="Field7" class="form-control" id="Field7"/>\
				<input name="Field8" class="form-control" id="Field8"/>\
				<input name="Field9" class="form-control" id="Field9"/>\
			</div>\
			 <input type="hidden" value="AddClass" name="Operation">\
			 <input type="submit" class="btn btn-primary" value="Submit" name="submit">\
			</form>\
			');
			
			$('#exampleModalCenter').modal('show');
		});
		
		$("#Sponsor").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
			if(confirm("This will cost you 1 SC")==false)return;
		
			$.post( "phps/Operations.php", {Operation: "SponsorClass",ID: L(data,"ID")}, function( data )
			{
				//console.log(data);
				location.reload();
			});
		});
	  </script>
	  
	  <?php include('PhpInclude/CreateTable.php');  ?>
	  
     
    </div>
	
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	  
	  
	
	<script>
	function L(data,str){return data[Col2Index[str]];}
	$('#dataTable').on('click', 'tr', function () {
		
        // var data = $('#dataTable').DataTable().row( this ).data();
		// if(data==undefined)return;
		// var Link="ViewPublicFunctionGraph.php?ID="+L(data,"ID")+"&InVar="+L(data,"In variables")+"&OutVar="+L(data,"Out variables");
		// console.log(Link);
		
		// $('#Framed').attr('src',Link);  
		// $('#IframeModal').modal('show');
		
		
		// window.location.href = Link;
	});

	$( document ).ready(function() {
		
		//var Cols = ["Value"]; 
		//$('#dataTable').DataTable().on('search.dt', function() { Total(Cols); });
		//Total(Cols);
			
		
	});
	</script>
  </div>
  
  

	
<iframe style="display:none;" name="som" onLoad="Txt=this.contentWindow.document.body.innerHTML;if(Txt=='Added\n')location.reload(); else if(Txt!='') alert(Txt);"></iframe>
	  <?php include('PhpInclude/tail.php');  ?>
</body>

</html>
