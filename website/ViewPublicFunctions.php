 <?php  
 
include('loginDB.php'); // Includes Login Script
if(!isset($_SESSION['login_user']))header("location: login.php");


echo "<script>EPost=0;StartHiding='Description';Col2Index=[];Index2Col=[];</script>";
$Format=array();
$Col2Image=array();
$ShowTotal=0;

$UserID=$_SESSION["UID"];
$ShowAllMine=GetSingleValue($con,"Select ShowAllMine from Users where ID=$UserID");
$ShowSC=GetSingleValue($con,"Select ShowSC from Users where ID=$UserID");
$ShowMineHours=GetSingleValue($con,"Select ShowMineHours from Users where ID=$UserID");


 $QUE =
	 "
	 SELECT 
	 PF.ID,PF.SC, Name, InVar 'In variables', OutVar 'Out variables',Username Creator,
	 (select GROUP_CONCAT(Name) from DefaultFunctions where FIND_IN_SET(ID,Dependency)) Dependency,
	 Description
	 FROM
	 PublicFunctions PF
	 join
	 Users U
	 on
	 (U.ID=PF.UserID)
	 
	 where 
	 ((('$ShowAllMine'='All') or ('$ShowAllMine'='Mine' and PF.UserID=$UserID)) and PF.SC>=$ShowSC)
	 or
	 (PF.UserID=$UserID and TimestampAdd >= DATE_SUB(NOW(),INTERVAL $ShowMineHours HOUR))
	 
	 order by PF.SC desc,PF.ID desc
	 ";

 //$connect = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die ("Cannot connect to the database");  
 $result = mysqli_query($con, $QUE);  
 if($con->error!="")die($QUE."<br>\n<br>".$con->error);
 ?>  
 
<!DOCTYPE html>


<html lang="en">

<?php include('PhpInclude/head.php');  ?>
<script src="res/cytoscape.min.js"></script>

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
	  
	  
	<button type="button" class="btn btn-success" id="AddFunction">Add/Copy function</button>
	<button type="button" class="btn btn-success" id="Guess">Guess function</button>
	<button type="button" class="btn btn-primary" id="Graph">View graph</button>
	<button type="button" class="btn btn-info" id="Description">Description</button>
	<button type="button" class="btn btn-info" id="Examples">Examples</button>
	<button type="button" class="btn btn-info" id="Simplify">Simplify</button>
	<button type="button" class="btn btn-danger" id="Support">Send 1 SC</button>
	  
	  <script>
		function L(data,str){return data[Col2Index[str]];};
		
		
		$("#AddFunction").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			var AIDI="-1";
			if(data!=undefined)
				AIDI=L(data,"ID");
			
			$.redirect("AddFunction.php", {AIDI: AIDI}, "POST"); 
			// alert(AIDI);
		});
		$("#Guess").click(function()
		{
			
			$("#exampleModalLongTitle").html("Pick options");
			
			$("#exampleModalCenter .modal-body").html('\
				<div class="form-group">\
				\
				<label for="VarIn" class="col-form-label">VarIn:</label>\
				<input name="VarIn" class="form-control" id="VarIn"/>\
				<label for="VarOut" class="col-form-label">VarOut:</label>\
				<input name="VarOut" class="form-control" id="VarOut"/>\
				\
				Guesses:\
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
			 <input id="GSubmit" type="submit" class="btn btn-primary" value="Submit" name="submit">\
			');
			
			$('#exampleModalCenter').modal('show');
			
			$("#GSubmit").click(function()
			{
				
				//var rez= Guesss(["Int a","String b"],"Bool");
				 var rez=Guesss($("#VarIn")[0].value.split(','),$("#VarOut")[0].value);
			});
			
		});
		
		
		$("#Graph").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
			var Link="ViewPublicFunctionGraph.php?ID="+L(data,"ID")+"&InVar="+L(data,"In variables")+"&OutVar="+L(data,"Out variables");
			console.log(Link);
			
			$('#Framed').attr('src',Link);  
			$('#IframeModal').modal('show');
		
		});
		
		$("#Description").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
			
			
			$("#exampleModalLongTitle").html("Pick options");
			
			$("#exampleModalCenter .modal-body").html('\
				<label for="modal_input_b" class="col-form-label">Description:</label>\
				<textarea style="resize:none" class="form-control" id="modal_input_b" cols="40" rows="10">'+L(data,"Description")+'</textarea>\
			');
			$('#exampleModalCenter').modal('show');
		});
		
		$("#Examples").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
			
			var Link="ViewExamples.php?F="+L(data,"ID");
			console.log(Link);
			window.location.href = Link;
			

		});
		
		$("#Support").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
			if(confirm("This will cost you 1 SC")==false)return;
		
			$.post( "phps/Operations.php", {Operation:"SupportFunction",ID: L(data,"ID")}, function( data )
			{
				//console.log(data);
				location.reload();
			});
		});
		
		$("#Simplify").click(function()
		{
			var data = $('#dataTable').DataTable().row('.selected').data();
			if(data==undefined)return;
			
			PublicFunctions=[];
			
			var AIDI=L(data,"ID");
			CompareCY=function(cyMare,cyMic)//cat de probabil e ca Mic sa fie parte din Mare
			{
				if(cyMare.$("node").length<cyMic.$("node").length)return "";
				ret = 0;
				var EdgeMic=GetEdges(cyMic).split("\n");
				var EdgeMare=GetEdges(cyMare).split("\n");
				if(EdgeMare.length<EdgeMic.length)return "";
				
				for(e1 in EdgeMic)//cat mai multe din mic sa fie in mare
				{
					var gasit=0;
					for(e2 in EdgeMare)
						if(EdgeMic[e1]==EdgeMare[e2])
							gasit=1;
					ret+=gasit;
				}
				if(ret==2)//2=1 muchie (ca \n=\n) = nu are sens sa trec asta
					return "";
				return ret-1;
			};
			GetEdges=function(cy)
			{
				var StrEdges="";
				var Edges=cy.$("edge");
				for(var a=0;a<Edges.length;a++)
				{
					var EdgeData=Edges[a].data();
					var SourceData=cy.$("#"+EdgeData.source).data();
					var TargetData=cy.$("#"+EdgeData.target).data();
					
					if(SourceData.Type=="Variable")SourceData.Value="";//nu imi pasa cum se numeste variabila
					if(TargetData.Type=="Variable")TargetData.Value="";
					
					var Source=SourceData.Type+" "+SourceData.Value+" "+SourceData.OutType;//out type nu are sens pentru functii (ca sunt egale si fara) dar are sens pt variabile
					var Target=TargetData.Type+" "+TargetData.Value+" "+TargetData.OutType;
					StrEdges+=(Source+" -> "+Target)+"\n";
				}
				return StrEdges;
			};
			//$.post( "phps/Operations.php", {Operation: "GetFunctionJson",AIDI: AIDI}, function( data )
			//{
			
			
			$.post( "phps/Operations.php", {Operation: "GetPublicFunctionsWithJson"}, function( data )
			{
				glo=data;
				var temp=data.trim().split(";");//trim pt ca se baga un \n la sfarsit de la post 
				for(var a=0;a<temp.length;a++)
				if(temp[a]!="")
				{
					var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
					var x=[];
					
					var cy=cytoscape();	
					cy.json(JSON.parse(temp2[2].slice(1, -1)));
					x.cy=cy;
					x.ID=temp2[0];
					x.Name=temp2[1];
					
					PublicFunctions[temp2[0]]=x;
				}
				var GoodFirst=[];
				var GoodSecond=[];
				
				for(e in PublicFunctions)
				if(e!=AIDI)
				{
					var Rez=CompareCY(PublicFunctions[AIDI].cy,PublicFunctions[e].cy);//console.log("Ce functii mici as putea sa folosesc:");
					if(Rez!="")
					{
						console.log("First: "+e+" : "+Rez);
						var xx=[];
						xx.ID=e;
						xx.Name=PublicFunctions[e].Name;
						xx.Score=Rez;
						glo1=xx;
						GoodFirst.push(xx);
					}
					Rez=CompareCY(PublicFunctions[e].cy,PublicFunctions[AIDI].cy);//console.log("La ce functii ar merge asta:");
					if(Rez!="")
					{
						console.log("Second: "+e+" : "+Rez);
						var xx=[];
						xx.ID=e;
						xx.Name=PublicFunctions[e].Name;
						xx.Score=Rez;
						GoodSecond.push(xx);
					}
				}
				GoodFirst.sort(function (a, b) {  return b.Score - a.Score;  });
				GoodSecond.sort(function (a, b) {  return b.Score - a.Score;  });
				
				
				GoodFirst=GoodFirst.map(x => x.Name+"("+x.ID+") : "+x.Score); 
				GoodSecond=GoodSecond.map(x => x.Name+"("+x.ID+") : "+x.Score); 
				
				
				GoodFirst=GoodFirst.join("\n");
				GoodSecond=GoodSecond.join("\n");
				
				$("#exampleModalLongTitle").html(" ");
				
				$("#exampleModalCenter .modal-body").html('\
					<label for="First" class="col-form-label">Smaller functions that may be used here:</label>\
					<textarea style="resize:none" class="form-control" id="First" cols="40" rows="10">'+GoodFirst+'</textarea>\
					\
					<label for="Second" class="col-form-label">Bigger functios that may use this:</label>\
					<textarea style="resize:none" class="form-control" id="Second" cols="40" rows="10">'+GoodSecond+'</textarea>\
				');
				$('#exampleModalCenter').modal('show');
	
	
			});
			
		});
		
		
		
		
		
		
		
		function Guesss(UnPaired,Target)
		{
			Functions=[];
			//Ordine: Def, Publice dupa SC, Clase dupa SC
			$.post( "phps/GetDefaultFunctions.php", function( data )
			{
				var temp=data.trim().split(";");
				for(var a=0;a<temp.length;a++)
				{
					var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
					var x=[];
					x.Name=temp2[1];
					x.In=temp2[2];
					x.OutType=temp2[3];
					x.ID="D "+temp2[0];
					Functions.push(x);
				}
	
			
				$.post( "phps/Operations.php", {Operation: "GetPublicFunctions"}, function( data )
				{
					var temp=data.trim().split(";");//trim pt ca se baga un \n la sfarsit de la post 
					for(var a=0;a<temp.length;a++)
					if(temp[a]!="")
					{
						var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
						var x=[];
						x.Name=temp2[1];
						x.In=temp2[2];
						x.OutType=temp2[3];
						x.ID="P "+temp2[0];
						Functions.push(x);
					}
					$.post( "phps/Operations.php", {Operation: "GetClasses"}, function( data )
					{
						var temp=data.trim().split(";");
						for(var a=0;a<temp.length;a++)
						{
							var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
							var x=[];
							x.Name=temp2[1];
							x.In=temp2[2];
							x.OutType=temp2[3];
							x.ID="C "+temp2[0];
							Functions.push(x);
						}
						
						for(var a=1;a<=9;a++)
							$("#Field"+a).val("");
						
						
						var List=[];
						var temp=[];
						var How=[];
						// var UnPaired=["Int a","String b"];
						// var Target="Bool";
						// var UnPaired=["Pdf b"];
						// var Target="Int";
						temp.How=How;
						temp.UnPaired=UnPaired;
						List.push(temp);
						var iteratie=1;
						var Alegeri=0;
						while(List.length != 0 && Alegeri<9 && iteratie/Functions.length<=10)//nu exista, 10 raspunsuri, maxim 10 noduri
						{
							//console.log(iteratie);
							var Head=List[0];
							if(Head.UnPaired.length==1 && Head.UnPaired[0].split(" ")[0]==Target)
							{
								console.log(Head.How);
				
								Alegeri++;
								//alert("Gasit!");
								//return;
								$("#Field"+Alegeri).val(Head.How);
							}
							//console.log(Head);
							List.shift(1);
							
							for(e in Functions)
							{
								//deep copy:  https://stackoverflow.com/questions/122102/what-is-the-most-efficient-way-to-deep-clone-an-object-in-javascript
								var Cops=jQuery.extend(true, {}, Head);
								
								var Ports=Functions[e].In.split(",");
								var ok=1;
								//console.log(Cops.UnPaired.length);
								for(var a=0;a<Ports.length;a++)
								{
									var gasit=0;
									for(var b=0;b<Cops.UnPaired.length && gasit==0;b++)
										if(Ports[a].split(" ")[0]==Cops.UnPaired[b].split(" ")[0])
										{
											Cops.How.push(Functions[e].Name+" "+Ports[a]+" -> "+Cops.UnPaired[b]);
											
											Cops.UnPaired.splice(b,1);///remove b
											gasit=1;
										}
									if(gasit==0)//console.log("nu am gasit "+Ports[a]);
										ok=0;
								}
								if(ok)//console.log(e+" OK");
								{
									Cops.UnPaired.push(Functions[e].OutType+" "+iteratie);
									List.push(Cops);
								}
							}
							iteratie++;
						}
					});
				});
				
			});
			
		}
	  </script>
	  
	  
	  <?php include('PhpInclude/CreateTable.php');  ?>
	  
     
    </div>
	
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	  
	  
	
	<script>
	function L(data,str){return data[Col2Index[str]];}
	$('#dataTable').on('click', 'tr', function () {
		
        
		
		
		// window.location.href = Link;
	});

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
