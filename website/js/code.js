var AllTypes=["Variable","Constant","IF","Default","Public","Done","Untyped","Recursive","Class","ClassGetter","Filter"]; 
	
	
//https://stackoverflow.com/questions/45680792/how-to-change-label-of-cytoscape-nodes
var cy = window.cy = cytoscape({
  container: document.getElementById('cy'),

  boxSelectionEnabled: false,
  autounselectify: true,
  wheelSensitivity: 0.1,

  layout: {
    name: 'dagre'
  },

  // style: [
    // {
      // selector: 'node',
      // style: {
        // 'content': function( ele )
		// {
			// if(ele.data('Type')=="Variable")
				// return ele.data('id')+' ('+ele.data('Value')+')';
			// if(ele.data('Type')=="Default")
				// return ele.data('Value');
			
			
			// return ele.data('id');
		// },
        // 'text-opacity': 0.5,
        // 'text-valign': 'center',
        // 'text-halign': 'right',
        // 'background-color': '#11479e'
      // }
    // },

    // {
      // selector: 'edge',
      // style: {
        // 'curve-style': 'bezier',
		
        // 'target-label': function( ele ) {if(ele.target().data().Type=="Default")return ele.data().Out;return "";},
        // 'target-text-offset': '10px',
		// 'text-outline-color': '#ffffff',
		// 'text-outline-width': '1px',
		// 'text-outline-opacity': 0.5,
		
		'text-rotation': 'autorotate',
        // 'width': 4,
        // 'target-arrow-shape': 'triangle',
        // 'line-color': '#9dbaea',
        // 'target-arrow-color': '#9dbaea'
      // }
    // }
  // ],

});
var VariableTypes=[];
var DefaultFunctions=[];
var PublicFunctions=[];
var Classes=[];


function SetStyle()
{
	cy.style()
	.selector('node')
		  .style({
			'content': function( ele )
			{
				if(ele.data('Type')=="Variable")
					return ele.data('Value')+' ('+ele.data('OutType')+')';
				if(ele.data('Type')=="IF")
					return 'IF ('+ele.data('OutType')+')';
				if(ele.data('Type')=="Default")
					if(ele.data('Value')!=undefined)
						if(DefaultFunctions[ele.data('Value')]!=undefined)
							return DefaultFunctions[ele.data('Value')].Name;
				if(ele.data('Type')=="Public")
					if(ele.data('Value')!=undefined)
						if(PublicFunctions[ele.data('Value')]!=undefined)
							return PublicFunctions[ele.data('Value')].Name;
						
				if(ele.data('Type')=="Filter")
					if(ele.data('Value')!=undefined)
						if(PublicFunctions[ele.data('Value').split(" ")[0]]!=undefined)
							return "Filter "+PublicFunctions[ele.data('Value').split(" ")[0]].Name;
						
				if(ele.data('Type')=="Constant")
					return ele.data('Value');
				if(ele.data('Type')=="Recursive")
					return "Recursive";
				
				if(ele.data('Type')=="Class")
					return ele.data('OutType');
				if(ele.data('Type')=="ClassGetter")
					return "Get "+ele.data('Value').substring(ele.data('Value').indexOf(' ') + 1);
				
				return ele.data('id');
			},
			'text-valign': 'center',
			'text-halign': 'right',
			'background-color': '#11479e',
			
			'text-opacity': 1,
			'text-outline-color': '#ffffff',
			'text-outline-width': '1px',
			'text-outline-opacity': 0.5,
		  }
		)
		.update();
	cy.style()
	.selector('edge')
		  .style({
			'curve-style': 'bezier',
			
			'target-label': function( ele )//label pe muhcie
			{
				return "";
			},
			'label': function( ele )//label pe muhcie
			{
				if(ele.target().data().Type=="Default")//daca e functie default
					if(DefaultFunctions[ele.target().data().Value].In.split(",").length!=1)//daca are mai mult de o var de intrare
						return ele.data().Port;
				if(ele.target().data().Type=="Public")//daca e functie publica
					if(PublicFunctions[ele.target().data().Value].In.split(",").length!=1)//daca are mai mult de o var de intrare
						return ele.data().Port;
				if(ele.target().data().Type=="Class")//daca e constructor
					return ele.data().Port;
				return "";
			},
			// 'target-text-offset': '10px',
			'text-outline-color': '#ffffff',
			'text-outline-width': '1px',
			'text-outline-opacity': 0.5,
			'text-opacity': 0.5,
			
			//'text-rotation': 'autorotate',
			'width': 4,
			'target-arrow-shape': 'triangle',
			'line-color': '#9dbaea',
			'target-arrow-color': '#9dbaea',
		  }
		)
		.update();
}
cy.ready(function()
{
	$.post( "phps/Operations.php", {Operation: "GetVariables"}, function( data )
	{
		VariableTypes=data.trim().split(",");
		$.post( "phps/Operations.php", {Operation: "GetPublicFunctions"}, function( data )
		{
			//console.log(data);
			var temp=data.trim().split(";");//trim pt ca se baga un \n la sfarsit de la post 
			glo=temp;
			for(var a=0;a<temp.length;a++)
			if(temp[a]!="")
			{
				var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
  				var x=[];
				x.Name=temp2[1];
				x.In=temp2[2];
				x.OutType=temp2[3];
				x.Dependency=temp2[4].split(",");
				PublicFunctions[temp2[0]]=x;
			}
			
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
					DefaultFunctions[temp2[0]]=x;
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
						Classes[temp2[0]]=x;
					}
					
					if(AIDI!=-1)//A dat copy la o functie 
					{
						$.post( "phps/Operations.php", {Operation: "GetFunctionJson",AIDI: AIDI}, function( data )
						{
							cy.json(JSON.parse(data.slice(1, -3)));
							var Nodes=cy.$("node");
							for(var a=0;a<Nodes.length;a++)
								Nodes[a].style({'background-color': "#204020"});
							var Edges=cy.$("edge");
							for(var a=0;a<Edges.length;a++)
								EdgeColor(Edges[a].id(),"#204020");
							SetStyle();
							cy.fit();
						});
					}
					else
						SetStyle();
				});
			});
		});
	
	});
	
	
	
});
function CanHave(Func,NodeIn,OutType)//Check if a function contains all the input a node have and if it has the same output type
{
	if(OutType!="" && OutType!=Func.OutType)
		return false;
	var Ins=[];
	for(a in Func.In.split(','))
	{
		var str=Func.In.split(',')[a];
		if(Ins.indexOf(str.split(' ')[0])==-1)
			Ins.push(str.split(' ')[0]);
	}
	//daca am in NodeIn ceva ce nu este in Functie (intra un nod int, care nu are treaba cu && care e pe 2 booluri) nu e buna functia
	return (($(NodeIn).not(Ins).get()).length==0);
}

// Encode/decode htmlentities
String.prototype.toHtmlEntities = function() {
    return this.replace(/./gm, function(s) {
        return "&#" + s.charCodeAt(0) + ";";
    });
};
String.fromHtmlEntities = function(string) {
    return (string+"").replace(/&#\d+;/gm,function(s) {
        return String.fromCharCode(s.match(/\d+/gm)[0]);
    })
};
	
	
function SetNodeData(NodeID)
{
	$("#exampleModalLongTitle").html("");
	glo=NodeID;
	var NodeType=cy.$("#"+NodeID).data().Type;
	var NodeValue=cy.$("#"+NodeID).data().Value;
	var NodeOutType=cy.$("#"+NodeID).data().OutType;
	
	var InTypes=[];
	var inc=cy.$('#'+NodeID).incomers('edge');
	var out=cy.$('#'+NodeID).outgoers('edge');
	
	
	
	for(var c=0;c<inc.length;c++)
	{
		OT=inc[c].source().data().OutType;
		if(OT!=undefined)
			if(InTypes.indexOf(OT.split(' ')[0])==-1)
				InTypes.push(OT.split(' ')[0]);
	}
	
	var ProbOutType="";
	
	///candva poate era bine ce e mai jos, dar acu nu are sens si da buguri, ce nu are sens, ce buguri. stiu ca era ceva cand editai un if parca, dar fmm
	for(var c=0;c<out.length;c++)
	{
		var temp=out[c].target().data().OutType;
		if(temp!=undefined && out[c].target().data().Type=="Variable")
		{
			if(ProbOutType=="")
				ProbOutType=temp;
			else
			{
				if(ProbOutType!=temp)
				{
					alert("All out types should be the same (for example "+ProbOutType+")");
					return;
				}
			}
		}
	}
	
	
	//todo, sa fie si aici can have, si interesant e ca daca ai din asta sa iasa catre un obiect care are int si string si un alt obiect care are doar int: doar int sa fie
	var Variables="";
	for(a in VariableTypes)
		if((NodeType=="Variable" || NodeType=="Constant" || NodeType=="Recursive") && VariableTypes[a]==NodeOutType)
			Variables+="<option selected>"+VariableTypes[a]+"</option>";//daca are deja o functie/var sa fie selectata
		else
			Variables+="<option>"+VariableTypes[a]+"</option>";
	
	
	var DefaultList="";
	for(e in DefaultFunctions)
		if(CanHave(DefaultFunctions[e],InTypes,ProbOutType))
		{
			if(NodeType=="Default" && e==NodeValue)
				DefaultList+="<option selected value="+e+">"+DefaultFunctions[e].Name+"</option>";//daca are deja o functie/var sa fie selectata
			else
				DefaultList+="<option value="+e+">"+DefaultFunctions[e].Name+"</option>";
		}
		
		
		
		
	var ClassList="";
	for(e in Classes)
		if(CanHave(Classes[e],InTypes,ProbOutType))
		{
			if(NodeType=="Class" && e==NodeValue)
				ClassList+="<option selected value="+e+">"+Classes[e].Name+"</option>";//daca are deja o functie/var sa fie selectata
			else
				ClassList+="<option value="+e+">"+Classes[e].Name+"</option>";
		}
		
		
	var ClassListGetter="";
	for(e in Classes)
	{
		if(NodeType=="ClassGetter" && e==NodeValue.split(" ")[0])
			ClassListGetter+="<option selected value="+e+">"+Classes[e].Name+"</option>";//daca are deja o functie/var sa fie selectata
		else
			ClassListGetter+="<option value="+e+">"+Classes[e].Name+"</option>";
	}
	
		
	var PublicList="";
	for(e in PublicFunctions)
		if(CanHave(PublicFunctions[e],InTypes,ProbOutType))
		{
			if(NodeType=="Public" && e==NodeValue)
				PublicList+="<option selected value="+e+">"+e+" "+PublicFunctions[e].Name+"</option>";//daca are deja o functie/var sa fie selectata
			else
				PublicList+="<option value="+e+">"+e+" "+PublicFunctions[e].Name+"</option>";
		}
	
	
	var PublicListFilter="";
	for(e in PublicFunctions)
		if(PublicFunctions[e].OutType=="Bool" && PublicFunctions[e].In!="")//Bool si daca are cel putin un input (poate e o functie constanta bool, nu are sens filter..)
		{
			if(NodeType=="Filter" && e==NodeValue.split(" ")[0])
				PublicListFilter+="<option selected value="+e+">"+e+" "+PublicFunctions[e].Name+"</option>";//daca are deja o functie/var sa fie selectata
			else
				PublicListFilter+="<option value="+e+">"+e+" "+PublicFunctions[e].Name+"</option>";
		}
		
		
	$(".modal-footer").html
	('<button type="button" class="btn btn-primary" id="modal_save_btn">Save changes</button>\
	  <button type="button" class="btn btn-primary" id="modal_self_btn">Self edge</button>\
	  <button type="button" class="btn btn-danger" id="modal_delete_btn">Delete</button>');
			  
			  
	var MainType;
	for(a in AllTypes)//daca e untyped -> Variabila. Daca e atceva, ramane acel ceva
		if((AllTypes[a]==NodeType && NodeType!="Untyped") || (NodeType=="Untyped" && AllTypes[a]=="Variable"))
			MainType+="<option selected>"+AllTypes[a]+"</option>";
		else
			if(AllTypes[a]!="Untyped")
				MainType+="<option>"+AllTypes[a]+"</option>";
		
	$(".modal-body").html('\
	    <form onsubmit="return false;">\
			\
			<div class="form-group">\
			  <label for="MainType">Type:</label>\
			  <select class="form-control" id="MainType">\
			  '+MainType+'\
			  </select>\
			</div>\
			\
          <div class="form-group">\
            <label id="LVariableName" for="VariableName" class="col-form-label" >Name:</label>\
			<input class="form-control" value="'+(NodeValue==undefined?"":NodeValue.toHtmlEntities())+'" id="VariableName"/>\
          </div>\
			\
			<div class="form-group">\
			  <label id="LVariableType" for="VariableType">Variable:</label>\
			  <select class="form-control" id="VariableType">\
				'+Variables+'\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label id="LDefault" for="Default">Default functions:</label>\
			  <select class="form-control" id="Default">\
				'+DefaultList+'\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label id="LPublic" for="Public">Public functions:</label>\
			  <select class="form-control" id="Public">\
				'+PublicList+'\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label id="LClasses" for="Classes">Classes:</label>\
			  <select class="form-control" id="Classes">\
				'+ClassList+'\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label id="LClassesForGetter" for="ClassesForGetter">Classes:</label>\
			  <select class="form-control" id="ClassesForGetter">\
				'+ClassListGetter+'\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label id="LClassField" for="ClassField">Field:</label>\
			  <select class="form-control" id="ClassField">\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label id="LFilterFunction" for="FilterFunction">Function:</label>\
			  <select class="form-control" id="FilterFunction">\
				'+PublicListFilter+'\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label id="LFilterField" for="FilterField">Field:</label>\
			  <select class="form-control" id="FilterField">\
			  </select>\
			</div>\
        </form>\
	');
	
	var MainTypeChanged=function()
	{
		var Type=$("#MainType :selected").text();
		//Hide all
		
		$("#VariableName").hide();$("#LVariableName").hide();
		$("#VariableType").hide();$("#LVariableType").hide();
		$("#Default").hide();$("#LDefault").hide();
		$("#Public").hide();$("#LPublic").hide();
		$("#Classes").hide();$("#LClasses").hide();
		
		$("#ClassesForGetter").hide();$("#LClassesForGetter").hide();
		$("#ClassField").hide();$("#LClassField").hide();
		
		$("#FilterFunction").hide();$("#LFilterFunction").hide();
		$("#FilterField").hide();$("#LFilterField").hide();
		
	
		if(Type=="Variable")
		{
			$("#VariableName").show();$("#LVariableName").show();
			$("#VariableType").show();$("#LVariableType").show();
		}
		if(Type=="Constant")
		{
			$("#VariableType").show();$("#LVariableType").show();
			$("#VariableName").show();$("#LVariableName").show();//todo nu prea imi place asta, tre sa fie in functie de tip de date
		}
		if(Type=="Default")
		{
			$("#Default").show();$("#LDefault").show();
		}
		if(Type=="Public")
		{
			$("#Public").show();$("#LPublic").show();
		}
		if(Type=="IF")
		{
			$("#VariableType").show();$("#LVariableType").show();
		}
		if(Type=="Recursive")
		{
			$("#VariableType").show();$("#LVariableType").show();
		}
		if(Type=="Class")
		{
			$("#Classes").show();$("#LClasses").show();
		}
		if(Type=="ClassGetter")
		{
			$("#ClassesForGetter").show();$("#LClassesForGetter").show();
			$("#ClassField").show();$("#LClassField").show();
		}
		if(Type=="Filter")
		{
			$("#FilterFunction").show();$("#LFilterFunction").show();
			$("#FilterField").show();$("#LFilterField").show();
		}
	};
	
	
	var ClassFieldChanged=function()
	{
		if($("#MainType :selected").text()!="ClassGetter")
			return;
		var HashTeMeLe="";
		var temp=Classes[$("#ClassesForGetter option:selected").val()].In.split(",");
		
		for(var c=0;c<temp.length;c++)
		if(NodeValue!=undefined && temp[c]==NodeValue.substring(NodeValue.indexOf(' ') + 1))
			HashTeMeLe+="<option selected>"+temp[c]+"</option>";
		else
			HashTeMeLe+="<option>"+temp[c]+"</option>";
		
		$("#ClassField").html(HashTeMeLe);
	};
	var FilterChanged=function()
	{
		if($("#MainType :selected").text()!="Filter")
			return;
		var HashTeMeLe="";
		var temp=PublicFunctions[$("#FilterFunction option:selected").val()].In.split(",");
		console.log(NodeValue);
		for(var c=0;c<temp.length;c++)
		if(NodeValue!=undefined && temp[c]==NodeValue.substring(NodeValue.indexOf(' ') + 1))
			HashTeMeLe+="<option selected>"+temp[c]+"</option>";
		else
			HashTeMeLe+="<option>"+temp[c]+"</option>";
		
		$("#FilterField").html(HashTeMeLe);
	};

	ClassFieldChanged();
	$( "#ClassesForGetter" ).change(function() {ClassFieldChanged();});
	
	FilterChanged();
	$( "#FilterFunction" ).change(function() {FilterChanged();});
	
	
	MainTypeChanged();
	$( "#MainType" ).change(function() {MainTypeChanged();ClassFieldChanged();FilterChanged();});
	
	
	$('#modal_self_btn').unbind();
	$('#modal_self_btn').click(function()
	{
		var Edge=cy.add({ group: "edges", data: { Port: "", source: NodeID, target: NodeID} });
		EdgeClicked(Edge,1);
		$('#exampleModalCenter').modal('hide');
	});
	
	$('#modal_delete_btn').unbind();
	$('#modal_delete_btn').click(function() {
		cy.remove("#"+NodeID);
		$('#exampleModalCenter').modal('hide');
	});
	
				
	$('#modal_save_btn').unbind();
	$('#modal_save_btn').click(function()
	{	
		//todo, exit daca e de ex: "Public" si lista e goala 
		
		var Type=$("#MainType :selected").text();
	
		if(Type=="Variable")
		{
			var VarName=$("#VariableName").val();
			var NodesWithThisName=cy.nodes("[Type='Variable'][Value='"+VarName+"']");
			if (!RegExp("^[a-zA-Z][a-zA-Z0-9]*?$").test(VarName) || (NodesWithThisName.length>0 && NodesWithThisName[0].id()!=NodeID))//daca e nume valid
			{
				alert("Invalid variable name");
				return;
			}
			cy.$("#"+NodeID).data("Value",VarName);
			cy.$("#"+NodeID).data("OutType",$("#VariableType option:selected").text());
		}
		if(Type=="Constant")
		{
			cy.$("#"+NodeID).data("Value",$("#VariableName").val());//todo nu prea imi place asta
			cy.$("#"+NodeID).data("OutType",$("#VariableType option:selected").text());
		}
		if(Type=="Class")
		{
			cy.$("#"+NodeID).data("Value",$("#Classes option:selected").val());
			cy.$("#"+NodeID).data("OutType",Classes[cy.$("#"+NodeID).data().Value].OutType);
		}
		if(Type=="ClassGetter")
		{
			var Field=$("#ClassField option:selected").text();
			cy.$("#"+NodeID).data("Value",$("#ClassesForGetter option:selected").val()+" "+Field);
			cy.$("#"+NodeID).data("OutType",Field.split(" ")[0]);
		}
		if(Type=="Filter")
		{
			var Field=$("#FilterField option:selected").text();
			cy.$("#"+NodeID).data("Value",$("#FilterFunction option:selected").val()+" "+Field);
			cy.$("#"+NodeID).data("OutType","["+Field.split(" ")[0]+"]");
		}
		if(Type=="Default")
		{
			cy.$("#"+NodeID).data("Value",$("#Default option:selected").val());
			cy.$("#"+NodeID).data("OutType",DefaultFunctions[cy.$("#"+NodeID).data().Value].OutType);
		}
		if(Type=="Public")//value = ID
		{
			cy.$("#"+NodeID).data("Value",$("#Public option:selected").val());
			cy.$("#"+NodeID).data("OutType",PublicFunctions[cy.$("#"+NodeID).data().Value].OutType);
		}
		if(Type=="Done")
		{
			cy.add({data: { id: "DONE" },position: cy.$("#"+NodeID).position() });
			Unify();
			$('#exampleModalCenter').modal('hide');
			cy.$("#"+NodeID).data("Type",Type);
			return;
		}
		if(Type=="Recursive")
		{
			cy.$("#"+NodeID).data("Value","");
			cy.$("#"+NodeID).data("OutType",$("#VariableType option:selected").text());
		}
		if(Type=="IF")
		{
			cy.$("#"+NodeID).data("Value","");
			cy.$("#"+NodeID).data("OutType",$("#VariableType option:selected").text());
		}
		
		
		
		cy.$('#'+NodeID).style({'background-color': "#204020"});
		cy.$("#"+NodeID).data("Type",Type);
		
		// NOD=cy.$("#"+NodeID);
		// glo1=NodeOutType;
		// glo2=cy.$("#"+NodeID).data().OutType;
		// if(
			// NodeOutType!=cy.$("#"+NodeID).data().OutType
			// ||
			// (NodeValue==cy.$("#"+NodeID).data().Value && (Type=="Default" || Type=="Public")))
		{
			console.log("Out changed");
			var inc=cy.$('#'+NodeID).incomers('edge');
			var out=cy.$('#'+NodeID).outgoers('edge');
			for(var c=0;c<out.length;c++)
			{
				var Edge=cy.add({ group: "edges", data: {Port:"", source: NodeID, target: out[c].target().id() } });
				EdgeClicked(Edge,1);
				cy.remove(out[c]);
			}
			for(var c=0;c<inc.length;c++)
			{
				var Edge=cy.add({ group: "edges", data: {Port:"", source: inc[c].source().id(), target: NodeID } });
				EdgeClicked(Edge,1);
				cy.remove(inc[c]);
			}
		}
		
		$('#exampleModalCenter').modal('hide');
	});
	
	$('#exampleModalCenter').modal('show');
	
}
function Done()
{
	var Nodes=cy.$("node");
	
	//todo, la constante sa scot din value -> si \n ca sa nu se strice datele astea
	// var StrEdges="";
	// var Edges=cy.$("edge");
	// for(var a=0;a<Edges.length;a++)
	// {
		// var EdgeData=Edges[a].data();
		// var SourceData=cy.$("#"+EdgeData.source).data();
		// var TargetData=cy.$("#"+EdgeData.target).data();
		// var Source=SourceData.Type+" "+SourceData.Value+" "+SourceData.OutType;
		// var Target=TargetData.Type+" "+TargetData.Value+" "+TargetData.OutType;
		// StrEdges+=(Source+" -> "+Target)+"\n";
	// }
	// StrEdges = StrEdges.slice(0, -1); // "12345.0"
	// console.log(StrEdges);
	
	
	
	
	//todo, constantele sa nu aibe muchii de intrare
	
	//DONE sa nu aibe muchii
	if(cy.$("#DONE").incomers('edge').length!=0 || cy.$("#DONE").outgoers('edge').length!=0)
	{
		alert("Done should not have edges");
		return;
	}
	for(var a=Nodes.length-1;a>=0;a--)//de ce nu e crescator? probabil asa era de unde am luat (unify si acolo avea sens)
	{
		var NodData=cy.$("#"+Nodes[a].id()).data();
		//Sa nu mai fie untyped
		if(NodData.Type=='Untyped')
		{
			alert("Everything should be typed");
			return;
		}
		//functile sa primeasca toti parametrii
		if(NodData.Type=="Recursive")
		{
			if(Nodes[a].outgoers('edge').length==0 || Nodes[a].incomers('edge').length==0)
			{
				alert("Recursive function does not have in/out variable");
				return;
			}
			
		}
		//tipuri de noduri care au nevoie de toti parametrii todo, si filter
		if(NodData.Type=='Default' || NodData.Type=='Public' || NodData.Type=='Class' || NodData.Type=='IF')
		{
			var sety = new Set([]); 
			var ID=NodData.Value;
			
			var Trebuie,Name;
			if(NodData.Type=='Default')
			{
				Trebuie=DefaultFunctions[ID].In.split(",");
				Name=DefaultFunctions[ID].Name;
			}
			if(NodData.Type=='Public')
			{
				Trebuie=PublicFunctions[ID].In.split(",");
				Name=PublicFunctions[ID].Name;
			}
			if(NodData.Type=='Class')
			{
				if(Classes[ID]==undefined)
				{
					alert("You are using unkown classes");
					return;
				}
			
			
				Trebuie=Classes[ID].In.split(",");
				Name=Classes[ID].Name;
			}
			if(NodData.Type=='IF')
			{
				Trebuie=["Bool cond",NodData.OutType+" a"];
				Name="IF ("+NodData.OutType+")";
			}
			
			for(var c=0;c<Trebuie.length;c++)
				if(Trebuie[c]!="")
					sety.add(Trebuie[c]);
			
			var inc=Nodes[a].incomers('edge');
			for(var c=0;c<inc.length;c++)
				sety.delete(inc[c].data().Port);
			
			console.log(sety.size);
			
			if(sety.size!=0)
			{
				glo=NodData;
				var IHave="";
				for (let item of sety) IHave+=" "+item;
				alert("Function "+Name+" does not have all in parameters, missing: "+IHave);
				return;
			}
		}
	}
	
	//sa se potriveasca datele (int->int) aka sa fie completate muchile
	var Edges=cy.$("edge");
	for(var a=0;a<Edges.length;a++)
		if(Edges[a].data().Port==undefined)
		{
			alert("Define all edges");
			return;
		}
	
	//sa aleaga var de iesire:
	var PossIn="";//are ceva in "out"
	var PossOut="";//are ceva in "in" si nu are "out"
	var Nodes=cy.$("node");
	for(var a=0;a<Nodes.length;a++)
	if(Nodes[a].data().Type=="Variable")
	{
		if(Nodes[a].outgoers('edge').length!=0)
			PossIn+="<option selected>"+Nodes[a].data().OutType+" "+Nodes[a].data().Value+"</option>";
		if(Nodes[a].outgoers('edge').length==0 && Nodes[a].incomers('edge').length!=0)
			PossOut+="<option selected>"+Nodes[a].data().OutType+" "+Nodes[a].data().Value+"</option>";
	}
	if(PossOut==0)
	{
		alert("Not enough out variables");
		return;
	}
	
	$("#exampleModalLongTitle").html("Save function");
	
	
	
	$(".modal-footer").html
	('<button type="button" class="btn btn-primary" id="modal_save_btn">Save changes</button>');
			  
	$(".modal-body").html('\
		<form onsubmit="return false;">\
		  <div class="form-group">\
			<label for="modal_input_a" class="col-form-label">Name:</label>\
			<input class="form-control" id="modal_input_a"/>\
		  </div>\
		  <div class="form-group">\
			<label for="modal_input_b" class="col-form-label">Description:</label>\
			<textarea style="resize:none" class="form-control" id="modal_input_b" cols="40" rows="5"></textarea>\
		  </div>\
			\
			<div class="form-group">\
			  <label for="sel1">In:</label>\
			  <select multiple class="form-control" id="sel1">\
				'+PossIn+'\
			  </select>\
			</div>\
			\
			<div class="form-group">\
			  <label for="sel2">Out:</label>\
			  <select multiple class="form-control" id="sel2">\
				'+PossOut+'\
			  </select>\
			</div>\
		</form>\
	');
	
	
	$('#modal_save_btn').unbind();
	$('#modal_save_btn').click(function()
	{
		var NewName=$("#modal_input_a").val();
		var NewDescription=$("#modal_input_b").val();
		var NewIn="";
		var NewOut="";
		
		var Sel1=document.getElementById("sel1");
		for(var a=0;a<Sel1.length;a++)
			if(Sel1.options[a].selected)
				NewIn=NewIn+Sel1.options[a].text+",";
		var Sel2=document.getElementById("sel2");
		for(var a=0;a<Sel2.length;a++)
			if(Sel2.options[a].selected)
				NewOut=NewOut+Sel2.options[a].text+",";
			
		
		for(var a=0;a<Sel1.length;a++)
		for(var b=0;b<Sel2.length;b++)
			if(Sel1.options[a].selected)
			if(Sel2.options[b].selected)
				if(Sel1.options[a].text==Sel2.options[b].text)
				{
					alert("You cant have the same variable in both In and Out");
					return;
				}
				
		
		
	
		for(var a=0;a<Sel2.length;a++)
		for(var b=a+1;b<Sel2.length;b++)
			if(Sel2.options[a].selected)
			if(Sel2.options[b].selected)
				if(Sel2.options[a].text.split(" ")[0]!=Sel2.options[b].text.split(" ")[0])
				{
					alert("All out variables should have the same type");
					return;
				}
			
		if(NewIn!="")
			NewIn = NewIn.slice(0, -1);
		if(NewOut!="")
			NewOut = NewOut.slice(0, -1);
			
		if(NewOut=="")
		{
			alert("You need at least an out variable");
			return;
		}
		if(NewName=="")
		{
			alert("Try a better name");
			return;
		}		
		if (!RegExp("^[a-zA-Z][a-zA-Z0-9]*?$").test(NewName))
		{
			alert("Invalid function name");
			return;
		}
		
		if(cy.$("node[Type='Recursive']").length)
		{
			//tre sa fie un singur in
			if (NewIn.indexOf(',') > -1 || NewIn=="")
			{
				alert("A recursive function have exactly one input");
				return;
			}
			var InType=NewIn.split(" ")[0];
			//tre sa se potriveasca cu out
			for(var a=0;a<Sel2.length;a++)
				if(Sel2.options[a].selected)
					if(Sel2.options[a].text.split(" ")[0]!=InType)
					{
						alert("In type and out type should be the same");
						return;
					}
			//tre sa se potriveasca cu tot ce a promis (toate nodurile recursiv)
			var Nodes=cy.$("node[Type='Recursive']")
			for(var a=0;a<Nodes.length;a++)
			if(Nodes[a].data().OutType!=InType)
			{
				alert("You promised that all recursive nodes are "+InType);
				return;
			}
		}
		
		
		var PublicFunctionsUsed=[];//cele directe U (reuniune) Publicele lor
		var tempPublic=cy.$("node[Type='Public']");
		for(var a=0;a<tempPublic.length;a++)
			PublicFunctionsUsed.push(tempPublic[a].data().Value);
		var tempPublic=cy.$("node[Type='Filter']");
		for(var a=0;a<tempPublic.length;a++)
			PublicFunctionsUsed.push(tempPublic[a].data().Value.split(" ")[0]);
		PublicFunctionsUsed=[...new Set(PublicFunctionsUsed.map(Number).sort(function (a, b) {  return a - b;  }))];
		
		var DefaultFunctionsUsed=[];//cele directe U defaultele din PublicFunctionsUsed
		var tempDef=cy.$("node[Type='Default']");
		for(var a=0;a<tempDef.length;a++)
			DefaultFunctionsUsed.push(tempDef[a].data().Value);
		for(var a=0;a<PublicFunctionsUsed.length;a++)
			DefaultFunctionsUsed= DefaultFunctionsUsed.concat(PublicFunctions[PublicFunctionsUsed[a]].Dependency);
		DefaultFunctionsUsed=[...new Set(DefaultFunctionsUsed.map(Number).sort(function (a, b) {  return a - b;  }))];
			
			
		
		
		if(cy.$("node").length>20)
			if(confirm("20+ nodes are hard to follow, continue?")==false)return;
		
		$('#exampleModalCenter').modal('hide');
		$.post( "phps/Operations.php",{Operation: "AddUserFunction",  Q: JSON.stringify(cy.json()),Name: NewName,Description: NewDescription,In: NewIn, Out: NewOut,Dependency: DefaultFunctionsUsed.toString()}, function( data )
		{
			console.log("DATA: "+data);
			window.location.href = "ViewPublicFunctions.php";	
		});
	});
	$('#exampleModalCenter').modal('show');
}
IDS='a';
function newNodeID()
{
	IDS= ((parseInt(IDS, 36)+1).toString(36)).replace(/0/g,'a');
	IDS=IDS.replace('1','a');
	
	if(cy.$('#'+IDS).length==0)
		return IDS;
	else
		return newNodeID();
}
function PointInBox(Node1,Node2)
{
	var Point=Node1.position();
	var Box=Node2.boundingbox({includeEdges: false,includeLabels: false,includeOverlays: false});
	if(Box.x1<=Point.x && Point.x<=Box.x2)
		if(Box.y1<=Point.y && Point.y<=Box.y2)
			return true;
	return false;
}
function Unify()
{
	console.log("uning");
	var Nodes=cy.$("node");
	for(var a=Nodes.length-1;a>=0;a--)
		for(var b=a-1;b>=0;b--)
			if(PointInBox(Nodes[a],Nodes[b]) || PointInBox(Nodes[b],Nodes[a]))
			{
				/*
				var ta=a,tb=b;
				if(Nodes[a].data().Type!='Untyped' && Nodes[a].data().Type=='Untyped')//then we keep b
				{
					alert("in if");
					var tc=ta;
					ta=tb;
					tb=tc;
				}
				var a=ta,b=tb;
				*/
				//the actual unify, we keep a for example
				var inc=Nodes[b].incomers('edge');
				var out=Nodes[b].outgoers('edge');
				
				for(var c=0;c<inc.length;c++)
				{
					var va=inc[c].source().id();
					if(va==Nodes[b].id())
						va=Nodes[a].id();
					cy.add({ group: "edges", data: {Port:"", source: va, target: Nodes[a].id() } });
					cy.remove(inc[c]);
				}
				for(var c=0;c<out.length;c++)
				{
					//nu facem si aici, ca am facut deja sus (ce? ceva cu muchiile)
					cy.add({ group: "edges", data: {Port:"", source: Nodes[a].id(), target: out[c].target().id() } });
					cy.remove(out[c]);
				}
				
				cy.remove(Nodes[b]);
				Unify();
				return;
			}
}

SelectedNode=0;
SelectedID="";
function SelectNode(node)
{
	if(SelectedNode==0)
	{
		SelectedNode=1;
		SelectedID=node.id();
		node.style({'background-color': "green"});
		return;
	}
	
	if(cy.$('#'+SelectedID).length==0)
	{
		SelectedID=node.id();
		node.style({'background-color': "green"})
		return;
	}
		
	if(SelectedID!=node.id())
	{
		var Edge=cy.add({ group: "edges", data: { Port: "", source: SelectedID, target: node.id() } });
		EdgeClicked(Edge,1);
	}
	else//self click
	{
		if(node.id()=="DONE")
			Done();
		else
			SetNodeData(node.id());
	}
	
	if(cy.$("#"+SelectedID).data().Type=='Untyped')
		cy.$('#'+SelectedID).style({'background-color': "#11479e"});
	else
		cy.$('#'+SelectedID).style({'background-color': "#204020"});
	
	SelectedNode=0;
	SelectedID="";
}

cy.on('tapend', 'node', function(evt)
{
	Unify();
});
cy.on('tap', 'node', function(evt)
{
	SelectNode(evt.target);
});
function EdgeColor(ID,Cul)
{
	cy.$('#'+ID).style({'line-color': Cul});
	cy.$('#'+ID).style({'target-arrow-color': Cul});
}


cy.on('tap', 'edge', function(evt)//click pe muchie
{
	EdgeClicked(evt.target,0);
});
function EdgeClicked(Edge,JustAdded)
{
	glo=Edge;
	//Special,Untyped,Variable,Default
	var SourceID1=Edge.source().id();
	var SourceID2=Edge.target().id();
	var Type1=Edge.source().data().Type;
	var Type2=Edge.target().data().Type;
	var Value1=Edge.source().data().Value;
	var Value2=Edge.target().data().Value;
	var OutType1=Edge.source().data().OutType;
	var OutType2=Edge.target().data().OutType;
	
	
	if(Type1=="Untyped" || Type2=="Untyped")//nu este definit unu din noduri
	{
		if(JustAdded)
			return;
		cy.remove("#"+Edge.id());
		return;
	}
	var Options="";
	
	if(Type2=="Default" || Type2=="Public" || Type2=="Class" || Type2=="Filter" || Type2=="IF")
	{
		var Vals;
		if(Type2=="IF")
			Vals=["Bool cond",OutType2+" a"];;
		if(Type2=="Class")
			Vals=Classes[Value2].In.split(",");
		if(Type2=="Default")
			Vals=DefaultFunctions[Value2].In.split(",");
		if(Type2=="Public")
			Vals=PublicFunctions[Value2].In.split(",");
		if(Type2=="Filter")
		{
			var i = Value2.indexOf(' ');
			var splits = [Value2.slice(0,i), Value2.slice(i+1)];
			Vals=PublicFunctions[splits[0]].In.split(",");
			for(var a=0;a<Vals.length;a++)
				if(Vals[a]==splits[1])
					Vals[a]="["+splits[1].split(" ")[0]+"] "+splits[1].split(" ")[1];
		}
		for(var a=0;a<Vals.length;a++)
			if(Vals[a].split(" ")[0]==OutType1)
			{
				if(Vals[a]==Edge.data().Port)
					Options+="<option selected>"+Vals[a]+"</option>";
				else
					Options+="<option>"+Vals[a]+"</option>";
			}
	}
	if(Type2=="Variable" && OutType2==OutType1)
		Options+="<option></option>";
	if(Type2=="Recursive" && OutType2==OutType1)
		Options+="<option></option>";
	if(Type2=="ClassGetter" && OutType1.split("-")[1]==Value2.split(" ")[0])//OutType1: "Person-3"    Value2: "3 Int Age"
		Options+="<option></option>";
		
	
	// alert("!"+OutType1+"! vs !"+OutType2+"!");

	console.log("Macar");
	if(Options=="")
	{
		cy.remove("#"+Edge.id());
		if(JustAdded==0)	
			alert("not compatible");
		return;
	}		
	
	$(".modal-footer").html
	('<button type="button" class="btn btn-primary" id="modal_save_btn">Save changes</button>\
	  <button type="button" class="btn btn-danger" id="modal_delete_btn">Delete</button>');
	$(".modal-body").html('\
		<form onsubmit="return false;">\
			<div class="form-group">\
			  <label for="sel2">Destination:</label>\
			  <select class="form-control" id="sel2">\
				'+Options+'\
			  </select>\
			</div>\
		</form>\
	');
	
	
	var FunctionOK=function()
	{
		// var Out=document.getElementById("sel2").options[document.getElementById("sel2").selectedIndex].text;
		EdgeColor(Edge.id(),"#204020");
		Edge.data("Port",$("#sel2 :selected").text());
		console.log(Edge.id());
			
		$('#exampleModalCenter').modal('hide');
	}
	if($("#sel2")[0].length==1 && cy.$('#'+Edge.id()).style()['line-color']!="#204020")//ca sa pot sa sterg muchie intre variabile
		FunctionOK();
	else
	{
		if(JustAdded)
			return;
		
		$('#modal_save_btn').unbind();
		$('#modal_save_btn').click(FunctionOK);
		$('#modal_delete_btn').unbind();
		$('#modal_delete_btn').click(function() {
			cy.remove("#"+Edge.id());
			$('#exampleModalCenter').modal('hide');
		});
		$('#exampleModalCenter').modal('show');
	}
	
};
cy.on('tap', function(event){
	
  // target holds a reference to the originator
  // of the event (core or element)
  var evtTarget = event.target;

  if( evtTarget === cy )//click on blank
  {
	  var ID=newNodeID();
	  cy.add({data: { id: ID },position: event.position });
	  cy.$("#"+ID).data("Type","Untyped");
	  Unify();
  }
  
});










