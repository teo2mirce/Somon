<?php
require '../loginDB.php'; // Includes Login Script
//header('Access-Control-Allow-Origin: *'); 






?>



<html lang="en">

<head>

  <style> 
body
{
    background: rgb(45,45,45);
	
	overflow: auto;
}


.rcorners3 {
    border-radius: 25px;
    background: linear-gradient(rgb(255,255,255), rgb(150,150,150));
    padding: 20px; 
    // width: 600px;
    // height: 300px;   
    width: 90%;
    height: 90%;    
}

#Cent
{
    text-align:center;
}
#Cent div {
    display:inline-block;
    margin:auto;
}

#foo
{
	width:90%;
	height:90%;
}

#Title
{
	font-size: 3em;
	text-align: center;
	color: rgb(150,150,250);
}

.Numar
{
	font-size: 2em;
	color: rgb(50,50,150);
}
.Title
{
	margin: 0px;
	font-size: 2em;
	color: rgb(50,50,150);
}

</style>
</head>

<script src="/js/jquery-3.1.0.js"></script>
  <script src="/vendor/jquery/jquery.min.js"></script>
<script src="/js/Chart.bundle.min.js"></script>
<script src="/js/allGraphs.js"></script>

<body>

<div id="Cent">
	<div class="rcorners3"><canvas id="myChart1"></canvas></div>
</div>


<script>

//https://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-dollars-currency-string-in-javascript
Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
 
 
			
	<?php
		if($_POST["function"]=="Bar")
		{
			$CESEVE=explode("\r\n",file_get_contents($_FILES["CSV"]['tmp_name']));
			$Lab=$CESEVE[0];
			unset($CESEVE[current(array_keys($CESEVE))]);
			$Tot=array_count_values($CESEVE);
			echo "
			{
				
				var Label='".$Lab."';";
			echo "var ctx = document.getElementById('myChart1').getContext('2d');";
		
			//$con = mysqli_connect($dhost, $dusername, $dpassword, $ddatabase) or die ("Cannot connect to the database"); 
			//$res = mysqli_query($con,$Query[$key]);
			$Labels = array();
			$BackgroundColors = array();
			$BorderColors = array();
			$Data = array();
			//while($row = mysqli_fetch_assoc($res))
			foreach ($Tot as $keyy => $value)
			{
				array_push($Labels,htmlentities($keyy));
				array_push($Data,$value);
			}
			for($i=0;$i<count($Data);$i++)
				for($j=$i+1;$j<count($Data);$j++)
					if($Data[$i]<$Data[$j])
					{
						$D1=$Data[$i];
						$Data[$i]=$Data[$j];
						$Data[$j]=$D1;
						
						$D1=$Labels[$i];
						$Labels[$i]=$Labels[$j];
						$Labels[$j]=$D1;
					}
			$Limit=30;
			if(count($Data)>$Limit)
			{
				$Labels = array_slice($Labels, 0, $Limit);
				$Data = array_slice($Data, 0, $Limit);
				echo "alert('Data truncated to $Limit results');";
			}
			$Min=$Data[0];
			$Max=$Data[0];
			for($i=0;$i<count($Data);$i++)
			{
				$Min=min($Min,$Data[$i]);
				$Max=max($Max,$Data[$i]);
			}
			for($i=0;$i<count($Data);$i++)
			{
				$col="255,255,255";
				if($Min!=$Max)
				{
					$x=($Data[$i]-$Min)/(1.0*$Max-$Min);
					$col="".floor(255*(2.0*(1.0-$x))).",".floor(255*(2.0*$x)).",0";
				}
				array_push($BackgroundColors,'"rgba('.$col.',0.2)",');
				array_push($BorderColors,'"rgb('.$col.')",');
			}
			echo 'var Labels=[';for($i=0;$i<count($Data);$i++)echo '"'.$Labels[$i].'",';echo '];';
			echo 'var BackgroundColors=[';for($i=0;$i<count($Data);$i++)echo $BackgroundColors[$i];echo '];';
			echo 'var BorderColors=[';for($i=0;$i<count($Data);$i++)echo $BorderColors[$i];echo '];';
			echo 'var Data=[';for($i=0;$i<count($Data);$i++)echo $Data[$i].",";echo '];';
			
			
			echo 'new Chart
				(
					document.getElementById("myChart1"),
					{
						"type":"bar",
						"data":
						{
							"labels":Labels,
							"datasets":
							[{
								"label":Label,
								"data":Data,
								"fill":false,
								"backgroundColor":BackgroundColors,
								"borderColor":BorderColors,
								"borderWidth":1
							}]
						}
						,
						"options":
							{	
								"responsive": true,
								"scales":
								{
									xAxes: [{
									  ticks: {
										autoSkip: false
									  }
									}],
									"yAxes":[
									{"ticks":
									
									{                   
										beginAtZero: true,
										callback: function(value, index, values) {
											
										return (value).formatMoney(2, ".", ",");
									}}
									
									
									}]
								}
							}
					}
				);
			}';
		}
		else  ///line
		{
			$csv = array_map('str_getcsv', file($_FILES["CSV"]['tmp_name']));
			
			// echo count($csv[0]);
			
			echo "drawGraph('myChart1','".json_encode($csv)."' );";
			//echo "drawGraph('myChart1',[[1,2,3][1,2,3]]);";
			
			
			//$CESEVE=str_getcsv(file_get_contents($_FILES["CSV"]['tmp_name']));
			//echo "alert('".count($csv[0])."');";
			//$CESEVE=explode("\r\n",file_get_contents($_FILES["CSV"]['tmp_name']));
			//$Lab=$CESEVE[0];
			//unset($CESEVE[current(array_keys($CESEVE))]);
		}
	?>




</script>


</body>
</html>