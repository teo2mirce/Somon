 <!-- Example DataTables Card-->
      <div class="card mb-3">
			
		  <div class="card-header">
          <i class="fa fa-table"></i>
		  <?php  echo $Modul; ?>
		  </div>
        <div class="card-body">
			<div class="table-responsive">  
				 <script>$( document ).ready(function() {$(".table").show();});</script>
				 <table id="dataTable" class="display table-striped table-bordered dt-responsive compact nowrap" style="display: none;width:100%">  
				 
					  <?php
							$i=0;
							
							//foreach($ArrRes as $result)
							{
								while($data = mysqli_fetch_assoc($result))
								{
									if($i==0)
									{
										//Capete de col
										echo "<thead><tr>  ";
										foreach($data as $key => $value)
											echo "<td>".$key."</td>";
										echo "</tr></thead>";  
										
										if($ShowTotal)
										{
											echo "<tfoot>";
											echo "<tr>  ";//Total
											foreach($data as $key => $value)
												echo "<td></td>";
											echo "</tr>";
											
											echo "<tr>  ";//Average
											foreach($data as $key => $value)
												echo "<td></td>";
											echo "</tr>";
											
											echo "</tfoot>";  
										}
										
										
										$a=0;
										foreach($data as $key => $value)
										{
											echo "<script>Index2Col[$a]='$key';</script>";
											echo "<script>Col2Index['$key']='$a';</script>";
											$a++;
										}
									}
								
						  
								   echo '<tr>';
								   
								   foreach($data as $key => $value)
								   {
									   if(in_array($key,$Format))
											echo "<td>".number_format($value/100.0, 2)."</td>";
										else
										{
											if(array_key_exists($key,$Col2Image))//pun totusi valoarea ca sa se poata sorta dupa ea
												echo "<td><div style=\"display:none\">$value</div><img src=\"".$Col2Image[$key][$value]."\"></td>";
											else
												echo "<td>".$value."</td>";
										}
								   }
										
								   echo '</tr>';  
								   $i++;
							  }
							}
					  ?>  
				 </table>  
			</div>  
        </div>
      </div>