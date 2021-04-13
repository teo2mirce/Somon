<!DOCTYPE>

 <?php  
 
	include('loginDB.php'); // Includes Login Script
	if(!isset($_SESSION['login_user']))header("location: login.php");

 ?>  
 
<!-- This code is for demonstration purposes only.  You should not hotlink to Github, Rawgit, or files from the Cytoscape.js documentation in your production apps. -->
<html>
  <head>
	<script src="res/jquery-3.3.1.min.js"></script>
	
	<script>
	IDView= <?php echo $_GET["ID"]; ?>;
	IDF= <?php $F=GetSingleValue($con,"select F from Instances where ID=".$_GET["ID"]); echo $F; ?>;
	
	InVar= ",<?php echo GetSingleValue($con,"select InVar from PublicFunctions where ID=$F"); ?>,";
	OutVar= ",<?php echo GetSingleValue($con,"select OutVar from PublicFunctions where ID=$F");  ?>,";
	
	console.log(IDView);
	</script>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
    <link href="css/style.css?random=<?php echo time(); ?>" rel="stylesheet" />

    <script src="res/cytoscape.min.js"></script>

	<link rel="shortcut icon" href="res/favicon.ico" type="image/x-icon">
	<link rel="icon" href="res/favicon.ico" type="image/x-icon">

	

    <!-- for testing with local version of cytoscape.js -->
    <!--<script src="../cytoscape.js/build/cytoscape.js"></script>-->

    <script src="res/dagre.min.js"></script>
    <script src="res/cytoscape-dagre.js"></script>
	
	
	
    <!-- right click
    <script src="res/contextMenu.min.js"></script>
	<link rel="stylesheet" href="res/contextMenu.min.css">
	-->
	
	
    <!-- modal stuff -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
  
  
  </head>
  <body>
  
  
    <div id="cy"></div>
    <!-- Load appplication code at the end to ensure DOM is loaded -->
    <script src="js/ViewDone.js?random=<?php echo time(); ?>"></script>
	
	
	
	<!-- $('#exampleModalCenter').modal('show') -->
	<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			...
		  </div>
		  <div class="modal-footer">
		  </div>
		</div>
	  </div>
	</div>



  </body>
</html>
