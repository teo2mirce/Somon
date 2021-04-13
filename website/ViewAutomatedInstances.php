 <?php  
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;

 $QUE ="select AI.ID,Type,Name from AutomatedInstances AI join PublicFunctions PF on (AI.Function=PF.ID) where AI.UserID=".$_SESSION['UID']."    ";

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
		<input id="Add" type="image" src="res//Add.png" />
		<input id="Edit" type="image" src="res//Edit.png" />
		<input id="Delete" type="image" src="res//Delete.png" />
	  -->
		
		<button type="button" class="btn btn-primary" id="Add">Add</button>
		<button type="button" class="btn btn-danger" id="Delete">Delete</button>

		<script>
		var PublicFunctions=[];
		var PublicList="";
		$.post( "phps/GetPublicFunctions.php", function( data )
		{
			var temp=data.trim().split(";");//trim pt ca se baga un \n la sfarsit de la post 
			for(var a=0;a<temp.length;a++)
			{
				var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
				var x=[];
				x.In=temp2[2];
				if(x.In=="")//doar cele fara in
				{
					x.OutType=temp2[3];
					x.Name=temp2[1];
					PublicFunctions[temp2[0]]=x;
				}
			}
			for(e in PublicFunctions)
				PublicList+="<option value="+e+">"+e+" "+PublicFunctions[e].Name+"</option>";
		});
		$("#Add").click(function()
		{
			$("#exampleModalLongTitle").html("Pick options");
			
			$("#exampleModalCenter .modal-body").html('\
			<form action="/phps/Operations.php" method="post" enctype="multipart/form-data" target="som">\
			<div class="form-group">\
			  <label id="LPublic" for="Public">Public functions:</label>\
			  <select name="function" class="form-control" id="Public">\
				'+PublicList+'\
			  </select>\
			  <label id="LType" for="Type">Type:</label>\
			  <select name="Type" class="form-control" id="Type">\
				<option value="30 min">30 min</option>\
				<option value="1 hour">1 hour</option>\
				<option value="1 day">1 day</option>\
				<option value="1 month">1 month</option>\
			  </select>\
			</div>\
			 <input type="hidden" value="AddAutomatedInstance" name="Operation">\
			 <input type="submit" class="btn btn-primary" value="Submit" name="submit">\
			</form>\
			');
			
			$('#exampleModalCenter').modal('show');
		});
		
		function L(data,str){return data[Col2Index[str]];};
		
		
		$("#Delete").click(function()
		{
		    var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
		
			$.post( "phps/Operations.php", {Operation: "DeleteAutomatedInstance",ID: L(data,"ID")}, function( data )
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
		
		
		// var win = window.open("phps/FilesOnTheFly.php?ID="+L(data,"ID"), '_blank');
		
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
