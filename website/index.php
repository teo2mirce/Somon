<?php
include('loginDB.php'); // Includes Login Script

if(!isset($_SESSION['login_user']))header("location: login.php");

?>

<!DOCTYPE html>
<html lang="en">

<?php include('PhpInclude/head.php');  ?>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">

	<?php include('PhpInclude/NavbarInclude.php'); ?>
	
	
  <div class="content-wrapper">
  
	<?php include('PhpInclude/ScrollToTopAndLogoutModal.php');  ?>
	
  </div>
  
  <?php include('PhpInclude/tail.php');  ?>
  
</body>

</html>
