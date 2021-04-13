function Money(In)
{
	// return (In).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');  // 12,345.67
	return (In).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').replace(".00","");  // 12,345.67
}
// Remove the formatting to get integer data for summation
function intVal( i ) 
{
	return typeof i === 'string' ?
		i.replace(/[\$,]/g, '')*1 :
		typeof i === 'number' ?
			i : 0;
};
	
function Total(Cols)
{
	var table = $('#dataTable').DataTable();
	// //number of filtered rows
	// console.log(table.rows( { filter : 'applied'} ).nodes().length);
	// //filtered rows data as arrays
	// console.log(table.rows( { filter : 'applied'} ).data());    
	
	
	
	//le fac pe toate ""
	Toate=table.columns()[0];
	if(StartHiding!="")
		Toate=Math.min(Toate,Col2Index[StartHiding]);
	for(var a=0;a<Toate.length;a++)
	{
		table.table().footer().childNodes[0].childNodes[a].innerHTML ="";
		table.table().footer().childNodes[1].childNodes[a].innerHTML ="";
		// $( table.column(Toate[a]).footer() ).html("");
	}
	// $( table.column(0).footer() ).html("Total");
	table.table().footer().childNodes[0].childNodes[0].innerHTML ="Total";
	table.table().footer().childNodes[1].childNodes[0].innerHTML ="Average";
	
	for(var i=0;i<Cols.length;i++)
	{
		
		// Total over this filter
		filterTotal = table
			.column( Col2Index[Cols[i]], { filter : 'applied'} )
			.data()
			.reduce( function (a, b) {
				return intVal(a) + intVal(b);
			}, 0 );
		NumOfRows=Math.max(1,table.rows({ filter : 'applied'})[0].length);//ca sa nu impartim la 0...
			
		table.table().footer().childNodes[0].childNodes[Col2Index[Cols[i]]].innerHTML =Money(filterTotal);
		table.table().footer().childNodes[1].childNodes[Col2Index[Cols[i]]].innerHTML =Money(filterTotal/NumOfRows);
		
		//$( table.column( Col2Index[Cols[i]] ).footer() ).html(Money(filterTotal));
		//$('tr:eq(0) td:eq(1)', table.column( Col2Index[Cols[i]] ).footer()).html('Masa');
	}
};