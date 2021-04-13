 <?php  
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;



 $QUE =
	 "
	 SELECT R.ID,Type,Name,Username,R.SC,State from Requests R join Users U on (R.User=U.ID)
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
	  
	<button type="button" class="btn btn-primary" id="AddRequest">Add request</button>
	<button type="button" class="btn btn-info" id="ViewSolutions">View solutions</button>
	<button type="button" class="btn btn-danger" id="Sponsor">Send 1 SC</button>
	<button type="button" class="btn btn-success" id="Done">Done</button>

	  <script>
		function L(data,str){return data[Col2Index[str]];};
		
		$("#ViewSolutions").click(function()
		{
			var data = $('#dataTable').DataTable().row( '.selected' ).data();
			if(data==undefined)return;
			var Link="ViewRequestsSolutions.php?ID="+L(data,"ID");
			console.log(Link);
			window.location.href = Link;
			
		});
		
	  $("#AddRequest").click(function()
		{
			$("#exampleModalLongTitle").html("Pick options");
			
			$("#exampleModalCenter .modal-body").html('\
			<form action="/phps/Operations.php" method="post" enctype="multipart/form-data" target="som">\
			<div class="form-group">\
				\
				<label for="Name" class="col-form-label">Name:</label>\
				<input name="Name" class="form-control" id="Name"/>\
				\
				<label for="modal_input_b" class="col-form-label">Description:</label>\
				<textarea style="resize:none" class="form-control" id="modal_input_b" cols="40" rows="5"></textarea>\
				\
				<label id="LType" for="Type">Type:</label>\
				<select name="Type" class="form-control" id="Type">\
					<option value="Public">Public</option>\
					<option value="Default">Default</option>\
				</select>\
			</div>\
			 <input type="hidden" value="AddRequest" name="Operation">\
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
		
			$.post( "phps/Operations.php", {Operation: "SponsorRequest",ID: L(data,"ID")}, function( data )
			{
				//console.log(data);
				location.reload();
			});
		});
		$("#Done").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
		
			$.post( "phps/Operations.php", {Operation: "DoneRequest",ID: L(data,"ID")}, function( data )
			{
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
  
  

	
<iframe style="display:none;" name="som" onLoad="if(this.contentWindow.document.body.innerHTML!='')location.reload();"></iframe>
	  <?php include('PhpInclude/tail.php');  ?>
</body>

</html>
