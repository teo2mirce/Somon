<? //Generate text file on the fly

require '../loginDB.php'; // Includes Login Script
 
$UserID=$_SESSION["UID"];
$ID=$_GET['ID'];
   $Function=GetSingleValue($con,"Select F from Instances where ID=$ID");
   $OutValue=GetSingleValue($con,"Select OutValue from Instances where ID=$ID");
   
   $OutType=GetSingleValue($con,"Select OutType from PublicFunctions where ID=$Function");
   if(in_array($OutType,array("Image","Gif","Wav","Mp3","Mid","Video","CSV","Txt","SVM")))
   {
	   if(GetSingleValue($con,"Select UserID from BlobFiles where ID=$OutValue")!=$UserID)die("Not for you");
	   $FileName=GetSingleValue($con,"Select FileName from BlobFiles where ID=$OutValue");
	   header("Content-type: image/gif");
	   header("Content-Disposition: attachment; filename=$FileName");
	   
	   print GetSingleValue($con,"Select BlobFile from BlobFiles where ID=$OutValue");

   }
   else
   {
	   if(in_array($OutType,array("[Image]","[Gif]","[Wav]","[Mp3]","[Mid]","[Video]","[CSV]","[Txt]","[SVM]")))
	   {
		   //https://stackoverflow.com/questions/1061710/php-zip-files-on-the-fly
		   
		   
		   // Prepare File
			$file = tempnam("tmp", "zip");
			$zip = new ZipArchive();
			$zip->open($file, ZipArchive::OVERWRITE);
			
			$OutValue = substr($OutValue, 1, -1);
			foreach(explode(",",$OutValue) as $BlobID)
		   	{
				$FileName=GetSingleValue($con,"Select FileName from BlobFiles where ID=$BlobID and UserID=$UserID");
				$FileData=GetSingleValue($con,"Select BlobFile from BlobFiles where ID=$BlobID and UserID=$UserID");
				
					
				// Stuff with content
				$zip->addFromString($FileName, $FileData);
			}

			// Close and send to users
			$zip->close();
			header('Content-Type: application/zip');
			header('Content-Length: ' . filesize($file));
			header('Content-Disposition: attachment; filename="file.zip"');
			readfile($file);
			unlink($file); 
	   }
	   else
	   {
		    echo "<script>window.close();</script>";
			die("Not a file");
	   }
   }
   
   
 ?>