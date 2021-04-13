var COLORS = [
    '#4dc9f6',
    '#f67019',
    '#f53794',
    '#537bc4',
    '#acc236',
    '#166a8f',
    '#00a950',
    '#58595b',
    '#8549ba'
];
function RandColor()
{
	return '#'+Math.floor(Math.random()*16777215).toString(16);
}

//daca nu folosesti asta, internet explorer e tampit si cachue ajax requesturi
$.ajaxSetup({ cache: false });

function drawGauge(canvasId,min,max,now,Labels,Cols)
{
	if(Labels==[])
		Labels=[min,(min+max)/2.0,max];
	
	var culNow='rgb(255,255,0)';
	if(Cols==[])
	{
		if(max!=min) //https://stackoverflow.com/questions/6394304/algorithm-how-do-i-fade-from-red-to-green-via-yellow-using-rgb-values
		{
			var x=(now-min)/(1.0*max-min);
			culNow="rgb("+Math.floor(255*(2.0*(1.0-x)))+","+Math.floor(255*(2.0*x))+",0)";///ca sa fie (255,0,0)->(255,255,0)->(0,255,0) tre pus 255 in loc de 126, dar e prea luminoase
		}
	}
	else
	{
		for(var a=0;a<Cols.length-1;a++)
			if(Labels[a]<=now && now<=Labels[a+1])
				culNow=Cols[a];
	}
	
	if(now<min)
		min=now;
	if(now>max)
		max=now;


	// if(max!=min) //https://stackoverflow.com/questions/6394304/algorithm-how-do-i-fade-from-red-to-green-via-yellow-using-rgb-values
	// {
		// var x=(now-min)/(1.0*max-min);
		// culNow="rgb("+Math.floor(255*(2.0*(1.0-x)))+","+Math.floor(255*(2.0*x))+",0)";///ca sa fie (255,0,0)->(255,255,0)->(0,255,0) tre pus 255 in loc de 126, dar e prea luminoase
	// }

	var opts = {
	  angle: 0.1, // The span of the gauge arc
	  lineWidth: 0.3, // The line thickness
	  radiusScale: 1, // Relative radius
	  pointer: {
		length: 0.6, // // Relative to gauge radius
		strokeWidth: 0.035, // The thickness
		color: '#000000' // Fill color
	  },
	  limitMax: false,     // If false, max value increases automatically if value > maxValue
	  limitMin: false,     // If true, the min value of the gauge will be fixed
	  colorStart: 'rgb(0,0,0)',   // Colors
	  colorStop: culNow,    // just experiment with them
	  strokeColor: '#EEEEEE',  // to see which ones work best for you
	  generateGradient: false,
	  highDpiSupport: true,     // High resolution support
	  
	  staticLabels: {
		  font: "1em sans-serif",  // Specifies font
		  labels: Labels,  // Print labels at these values
		  color: "#000000",  // Optional: Label text color
		  fractionDigits: 0  // Optional: Numerical precision. 0=round off.
		},
  
	};
	var target = document.getElementById(canvasId); // your canvas element
	var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
	gauge.maxValue = max; // set max gauge value
	gauge.setMinValue(min);  // Prefer setter over gauge.minValue = 0
	gauge.animationSpeed = 32; // set animation speed (32 is default value)
	gauge.set(now); // set actual value


}

function Money(In)
{
	// return In;
	// alert(In);
	// return (In).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');  // 12,345.67
	return (In).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').replace(".00","");  // 12,345.67
}
function drawGraph(canvasId,data){
	data=JSON.parse(data);
	if(data.length<=0)return;
	
	var Cols=data[0];
	
	data.shift();
	data.sort(function(a,b){
		return (a[0] <= b[0]) ? -1 : 1;
		//return a[1] - b[1];
	});

	var datasets = [];
	var labels = [];
	
	
	for(var a=0;a<data.length;a++)
		labels.push(data[a][0]);
	
	glo=data;
	for(var a=1;a<Cols.length;a++)
	{
		var vals=[];
		for(var b=0;b<data.length;b++)
			vals.push(data[b][a]);
		
		var Color=a<COLORS.length?COLORS[a%COLORS.length]:RandColor();
		datasets.push({
			label: Cols[a],
			backgroundColor: Color,
			borderColor: Color,
			data: vals,
			fill: false,
			//steppedLine: true,
		});
	}
	
	//get canvas
	var ctx = $("#"+canvasId);

	var data = {
		labels : labels,
		datasets : datasets
	};

	var options = {
		
		//showLines: false,
		animation: {
			duration: 0, // general animation time
		},



		elements: {
				line: {
					tension: 0
				}
			},



		responsive: true,
		/*title: {
			display: true,
			text: 'Chart'
		},
		tooltips: {
			mode: 'index',
			intersect: false,
		},
		*/
		tooltips: {
			mode: 'index',
			intersect: false,
				callbacks: {
					label: function(tooltipItem) {
						return data.datasets[tooltipItem.datasetIndex].label+": "+Money(tooltipItem.yLabel);
						// return "$" + Number(tooltipItem.yLabel) + " and so worth it !";
					}
				}
		},

		hover: {
			mode: 'nearest',
			intersect: true
		},
		scales: {
			xAxes: [{
				type: "time",
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'Date'
				}
			}],
			yAxes: [{
				display: true,
				scaleLabel:
				{
					display: true,
					labelString: 'Value'
				},
			}]
		}
	};

	var chart = new Chart( ctx, {
		type : "line",
		data : data,
		options : options
	} );

}