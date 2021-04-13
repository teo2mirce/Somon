<?php
include('loginDB.php'); // Includes Login Script

if(isset($_SESSION['login_user'])){
header("location: index.php");
}
?>


<!DOCTYPE html>
<html lang="en">


<?php include('PhpInclude/head.php');  ?>


<body class="bg-dark">
  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Somon login</div>
      <div class="card-body">
        <form action="" method="post">
          <div class="form-group">
            <label for="Email">Account</label>
            <input class="form-control" name="Email" id="Email" placeholder="Email">
          </div>
          <div class="form-group">
            <label for="Password">Password</label>
            <input class="form-control" name="Password" id="Password" type="password" placeholder="Password">
          </div>
		  <input class="btn btn-primary btn-block" name="submit" type="submit" value=" Login ">
		  <span><?php echo $error; ?></span>
        </form>
        <div class="text-center">
          <a class="d-block small mt-3" href="Register.php">Register an Account</a>
          <!--  <a class="d-block small" href="forgot-password.php">Forgot Password?</a> -->
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>

</html>
