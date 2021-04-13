<? //Generate text file on the fly

	session_start(); // Starting Session
   header("Content-type: text/plain");
   header("Content-Disposition: attachment; filename=config.uap");

   // do your Db stuff here to get the content into $content
   print $_SESSION['Email']."\n";
   print $_SESSION['Password'];
   //print "masa";
 ?>