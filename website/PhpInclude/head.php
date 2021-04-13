<head>

  <link href="css//all.css" rel="stylesheet" type="text/css">
  <link href="css//buttons.dataTables.min.css" rel="stylesheet" type="text/css">
  <script src="js//all.js"></script>

  <link rel="shortcut icon" type="image/png" href="res/favicon.ico"/>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Somon</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
  
  <script src="vendor/jquery/jquery.min.js"></script>
  
  <style>
	.navbar-sidenav{
		overflow-y: auto;
	}

  .card-body {
	  -webkit-box-flex: 1;
	  -ms-flex: 1 1 auto;
	  flex: 1 1 auto;
	  padding: 0.5rem !important;
	}
</style>
  
  <script>
  	$( document ).ready(function() {
		
		$(".display").show();
		$('#dataTable').DataTable( {
			"autoWidth": false,
			"order": [],
			
			colReorder: true,
			
			scrollY:        "70%",
			scrollX:        true,
			scrollCollapse: true,
			paging:         false,
				
			"dom": 't<"inline"f>  <"inline"B> <"inline"i>',
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
				'pdfHtml5'
			],
			
			"preDrawCallback": function( settings ) {
				if(StartHiding!="")
				{
					var table = $('#dataTable').DataTable();
					var Toate=table.columns()[0];
					for(var a=Col2Index[StartHiding];a<Toate.length;a++)
						table.column(a).visible(0,false);
				}
			 }
		} );
		
		var table = $('#dataTable').DataTable();
		$('#dataTable tbody').on( 'click', 'tr', function ()
		{
			if ( $(this).hasClass('selected') ) 
				$(this).removeClass('selected');
			else
			{
				table.$('tr.selected').removeClass('selected');
				$(this).addClass('selected');
			}
		} );
	 
		$('#button').click( function () { table.row('.selected').remove().draw( false );} );
	
		$('#dataTable').DataTable().columns.adjust().draw();
	});
	</script>
</head>


<!-- Old head

<head>

  <link href="css//all.css" rel="stylesheet" type="text/css">
  <script src="js//all.js"></script>

  <link rel="shortcut icon" type="image/png" href="res/favicon.ico"/>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Somon</title>
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="css/sb-admin.css" rel="stylesheet">
  
  <script src="vendor/jquery/jquery.min.js"></script>
  
</head>

-->
