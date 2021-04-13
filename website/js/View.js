
// Special,Untyped,Variable,Default,Mine,Public
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
//var VariableTypes=[];
var DefaultFunctions=[];
var PublicFunctions=[];

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
			'background-color': 
			function( ele )
			{
				//alert("?");
				return '#11979e';
			},
			
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

function EdgeColor(ID,Cul)
{
	cy.$('#'+ID).style({'line-color': Cul});
	cy.$('#'+ID).style({'target-arrow-color': Cul});
}
cy.ready(function()
{
	//todo not compatible sa stearga muchia respectiva SAU sa iti modifice destinatia (daca e variabila de exemplu)

		$.post( "../phps/GetPublicFunctions.php", function( data )
		{
			var temp=data.trim().split(";");//trim pt ca se baga un \n la sfarsit de la post 
			for(var a=0;a<temp.length;a++)
			{
				var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
  				var x=[];
				x.Name=temp2[1];
				x.In=temp2[2];
				x.OutType=temp2[3];
				x.Dependency=temp2[4].split(",");
				PublicFunctions[temp2[0]]=x;
			}
			
			$.post( "../phps/GetDefaultFunctions.php", function( data )
			{
				var temp=data.trim().split(";");
				for(var a=0;a<temp.length;a++)
				{
					var temp2=temp[a].split("_");//echo $line['ID'].'_'.$line['Name'].'_'.$line['InVar'].'_'.$line['OutType'];
					var x=[];
					x.In=temp2[2];
					x.OutType=temp2[3];
					x.Name=temp2[1];
					DefaultFunctions[temp2[0]]=x;
				}
				
				
				// SetStyle();
				$.post( "phps/Operations.php", {Operation: "GetFunctionJson",AIDI: IDView}, function( data )
				{
					cy.json(JSON.parse(data.slice(1, -3)));
					
					var Nodes=cy.$("node");
					for(var a=0;a<Nodes.length;a++)
					{
						if(Nodes[a].data().Type=="Constant" || (Nodes[a].data().Type=="Variable" && InVar.indexOf(" "+Nodes[a].data().Value+",")!=-1 ))
							Nodes[a].style({'background-color': "#202080"});
						else 
						{
							if(Nodes[a].data().Type=="Variable" && OutVar.indexOf(" "+Nodes[a].data().Value+",")!=-1 )
								Nodes[a].style({'background-color': "#802020"});
							else
								Nodes[a].style({'background-color': "#204020"});
						}
					}
					var Edges=cy.$("edge");
					for(var a=0;a<Edges.length;a++)
						EdgeColor(Edges[a].id(),"#204020");
					SetStyle();
					cy.fit();
				});
			});
		});
	
});
