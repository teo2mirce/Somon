 <?php  
 
	include('loginDB.php'); // Includes Login Script
	if(!isset($_SESSION['login_user']))header("location: login.php");

 ?>  
 
<!DOCTYPE html>


<html lang="en">

<?php include('PhpInclude/head.php');  ?>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  
	<?php include('PhpInclude/NavbarInclude.php');  ?>
	
  
  <div class="content-wrapper">
    <div class="container-fluid">
	 
    </div>
	
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	  
	  
	<script>
		var PublicFunctions=[];
		$( document ).ready(function() {
		
			$("#exampleModalLongTitle").html("Pick options");
					  
			$("#exampleModalCenter .modal-body").html('\
				<form action="/phps/ViewGraphs.php" method="post" enctype="multipart/form-data" >\
				<div class="form-group">\
				  <label id="LPublic" for="Public">Type:</label>\
				  <select name="function" class="form-control" id="Public">\
				  <option value="Bar">Bar</option>\
				  <option value="Line">Line</option>\
				  </select>\
				</div>\
				  <div class="form-group" id="ToAdd">\
				  <input name="CSV" class="InputClass" type="File" accept=".csv" id="CSV" > <label for="CSV">CSV</label><br>\
				  </div>\
				 <input type="submit" class="btn btn-primary" value="Submit" name="submit">\
				</form>\
			');
			$('#exampleModalCenter').modal('show');
	});
	</script>
	
  </div>
  
	  <?php include('PhpInclude/tail.php');  ?>
</body>

</html>
