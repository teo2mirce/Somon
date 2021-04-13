  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="index.php"> <?php echo $_SESSION["login_user"].' - '.GetSingleValue($con,"SELECT ROUND(SC,6) from Users WHERE ID=".$_SESSION['UID'])." SC"; ?>  </a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
		
		
			<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
			  <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseSystemPages" data-parent="#exampleAccordion">
				<i class="fa fa-fw fa-file"></i>
				<span class="nav-link-text">Instances</span>
			  </a>
			  <ul class="sidenav-second-level collapse" id="collapseSystemPages">
			  
					<li><a href="ViewInstances.php">View instances</a></li>
					<li><a href="AddInstance.php">Add instance</a></li>
					<li><a href="ViewAutomatedInstances.php">Automated instances</a></li>
			  </ul>
			</li>
		
			<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
			  <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseExamplePages" data-parent="#exampleAccordion">
				<i class="fa fa-fw fa-file"></i>
				<span class="nav-link-text">Functions</span>
			  </a>
			  <ul class="sidenav-second-level collapse" id="collapseExamplePages">
			  
					<li><a href="ViewPublicFunctions.php">Public functions</a></li>
					<li><a href="ViewDefaultFunctions.php">Default functions</a></li>
					<!-- <li><a href="AddFunction.php">Add function</a></li> -->
					<li><a href="ViewRequests.php">Requests</a></li>
			  </ul>
			</li>
		
		
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Classes">
          <a class="nav-link" href="ViewClasses.php">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">Classes</span>
          </a>
        </li>
		
		
		
		<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
		  <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseSystemPages2" data-parent="#exampleAccordion2">
			<i class="fa fa-fw fa-file"></i>
			<span class="nav-link-text">Download</span>
		  </a>
		  <ul class="sidenav-second-level collapse" id="collapseSystemPages2">
				<li><a href="Miner.zip">Miner</a></li>
				<li><a href="phps/ConfigOnTheFly.php">Config</a></li>
		  </ul>
		</li>
		
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="View graphs">
          <a class="nav-link" href="ViewGraphs.php">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">View graphs</span>
          </a>
        </li>
			
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Users">
          <a class="nav-link" href="Users.php">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">Users</span>
          </a>
        </li>
		
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Functions statistics">
          <a class="nav-link" href="FunctionsStatistics.php">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">Functions statistics</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Settings">
          <a class="nav-link" href="Settings.php">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">Settings</span>
          </a>
        </li>
		
		
		
		
		
      </ul>
	  
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
	  
	  
	  
	  
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
            <i class="fa fa-fw fa-sign-out"></i>Logout</a>
        </li>
      </ul>
    </div>
  </nav>
  
  
  
  
  
  
  
  
  
  
  
  
  	<!-- $('#exampleModalCenter').modal('show') -->
	<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			...
		  </div>
		  <div class="modal-footer">
		  </div>
		</div>
	  </div>
	</div>