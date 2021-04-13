<!DOCTYPE html>
<html lang="en">

<?php include('PhpInclude/head.php');  ?>

<body class="bg-dark">
  <div class="container">
    <div class="card card-register mx-auto mt-5">
      <div class="card-header">Register an Account</div>
      <div class="card-body">
        <div>
          <div class="form-group">
            <div class="form-row">
                <label for="exampleInputName">Username</label>
                <input class="form-control" id="exampleInputName" type="text" aria-describedby="nameHelp" placeholder="Username">
            </div>
          </div>
          <div class="form-group">
            <div class="form-row">
				<label for="exampleInputEmail1">Email address</label>
				<input class="form-control" id="exampleInputEmail1" type="email" aria-describedby="emailHelp" placeholder="Enter email">
            </div>
          </div>
          <button id="Register" class="btn btn-primary btn-block">Register</button>
        </div>
        <div class="text-center">
          <a class="d-block small mt-3" href="login.php">Login Page</a>
          <!-- <a class="d-block small" href="forgot-password.html">Forgot Password?</a> -->
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  
  <script>
  	$('#Register').click(function()
	{
		$.post( "phps/RegisterUser.php",{User: $("#exampleInputName").val(),Email: $("#exampleInputEmail1").val()}, function( data )
		{
			alert(data);
		});
	});
  </script>
  
</body>

</html>
