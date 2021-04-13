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
	  
	  <?php //include('PhpInclude/CreateTable.php');  ?>
	  
     
    </div>
	
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	  
	  
	
	<script>
		var PublicFunctions=[];
		$( document ).ready(function()
		{
			$.post( "phps/Operations.php", {Operation: "GetPublicFunctions"}, function( data )
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
					<form action="/phps/AddToDo.php" method="post" enctype="multipart/form-data" target="som">\
					<div class="form-group">\
					  <label id="LPublic" for="Public">Public functions:</label>\
					  <select name="function" class="form-control" id="Public">\
						'+PublicList+'\
					  </select>\
					</div>\
					  <div class="form-group" id="ToAdd">\
					  </div>\
					 <input type="submit" class="btn btn-primary" value="Submit" name="submit">\
					</form>\
				');
				
				var MainTypeChanged=function()
				{
					var Variabile="";
					var ID=$("#Public :selected").val();
					var Splits=PublicFunctions[ID].In.split(",");
					
					if(PublicFunctions[ID].In!="")
					for(var a=0;a<Splits.length;a++)
					{
						var e=Splits[a];//todo sa fie type (html type) la input in functie de type real
						
						var type=e.split(" ")[0];
						var typeHTML="text";
						var Accept="";//='image/png, image/jpeg';
						
						if(type=="Image")
							Accept='image/*';
						if(type=="Video")
							Accept='video/*';
						if(type=="Gif")
							Accept='.gif';
						if(type=="Mp3")
							Accept='.mp3';
						if(type=="Wav")
							Accept='.wav';
						if(type=="Mid")
							Accept='.mid';
						if(type=="Pdf")
							Accept='.pdf';
						if(type=="CSV")
							Accept='.csv';
						if(type=="SVM")
							Accept='.svm';
						if(type=="Zip")
							Accept='.zip';
						
						if(Accept!="")
							typeHTML="File";
						
						//nu te lasta hostingeru cu mai multe fisiere (adica [Images], tre puse in zip sau ceva :'(  )
						
						//nu merge in name cu '[' si  ']'  tre schimbat si in addtodo.php
						var eEscaped=e.replace("[","(").replace("]",")");
						//e=eEscaped;
						Variabile+="<input name='"+eEscaped+"' class='InputClass' type='"+typeHTML+"' accept='"+Accept+"' id='"+e+"'   > <label for='"+e+"'>"+e+"</label><br>";
					}
					$("#ToAdd").html(Variabile);
				};
				
				MainTypeChanged();
				$( "#Public" ).change(function(){MainTypeChanged();});
				
				$('#exampleModalCenter').modal('show');
			});
		});
	</script>
	
  </div>
  
  
<iframe style="display:none;" name="som" onLoad="if(this.contentWindow.document.body.innerHTML!='')alert(this.contentWindow.document.body.innerHTML);"></iframe>
	
	  <?php include('PhpInclude/tail.php');  ?>
</body>

</html>
