 <?php  
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='FunctionID';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;
///fara (state si timestmap si name) done fata de instances
 $QUE ="select concat(Name,'(',PF.ID,')') Name,RS.SC 'Solution SC',PF.SC 'Function SC',FunctionID,RequestID from RequestsSolutions RS join PublicFunctions PF on(PF.ID=RS.FunctionID) where RequestID=".$_GET["ID"]." order by RS.SC desc";

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
	<button type="button" class="btn btn-primary" id="Propose">Propose solution</button>
	<button type="button" class="btn btn-danger" id="Support">Send 1 SC</button>
	  
	  <script>
		var RequestID= <?php echo $_GET["ID"]; ?>;
		var PublicFunctions=[];
		function L(data,str){return data[Col2Index[str]];};
		
		$("#Propose").click(function()
		{
			$.post( "phps/GetPublicFunctions.php", function( data )
			{
				var temp=data.trim().split(";");//trim pt ca se baga un \n la sfarsit de la post 
				for(var a=0;a<temp.length;a++)
				{
					var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
					var x=[];
					x.In=temp2[2];
					x.OutType=temp2[3];
					x.Name=temp2[1];
					PublicFunctions[temp2[0]]=x;
				}
				// alert("2");
				$("#exampleModalLongTitle").html("Pick options");
				//$("#exampleModalCenter .modal-footer").html
				//('<button type="button" class="btn btn-primary" id="modal_save_btn">Save changes</button>');
				
				
				var PublicList="";
				for(e in PublicFunctions)
					PublicList+="<option value="+e+">"+e+" "+PublicFunctions[e].Name+"</option>";
						  
				$("#exampleModalCenter .modal-body").html('\
					<div class="form-group">\
					  <label id="LPublic" for="Public">Public functions:</label>\
					  <select name="function" class="form-control" id="Public">\
						'+PublicList+'\
					  </select>\
					</div>\
					<input id="modal_save_btn" type="submit" class="btn btn-primary" value="Submit" name="submit">\
				');
				

				
				$('#modal_save_btn').unbind();
				$('#modal_save_btn').click(function()
				{
					var FunctionID=$("#Public :selected").val();
					// console.log("FID: "+FunctionID);
					// console.log("RID: "+RequestID);
					$.post( "phps/Operations.php", {Operation:"ProposeRequestSolution",FunctionID: FunctionID, RequestID: RequestID}, function( data )
					{
						//console.log(data);
						location.reload();
					});
			
				});
				$('#exampleModalCenter').modal('show');
			});
		
		
		
			// var data = $('#dataTable').DataTable().row('.selected').data();
			// if(data==undefined)return;
			// $.post( "phps/SponsorFunction.php", {ID: L(data,"ID")}, function( data )
			// {
				// //console.log(data);
				// location.reload();
			// });
		});
		$("#Support").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
			if(confirm("This will cost you 1 SC")==false)return;
			
			$.post( "phps/Operations.php", {Operation:"SupportRequestSolution",FunctionID: L(data,"FunctionID"), RequestID: L(data,"RequestID")}, function( data )
			{
				console.log(data);
				location.reload();
			});
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
