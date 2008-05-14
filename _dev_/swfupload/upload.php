<?php
	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}

	session_start();

	// Check the upload
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		exit(0);
	}
	
	function getName($pre='',$post='')
{
	$name=microtime();
	$name=str_replace(array(' ','.'),'',$name);
	$cle=mt_rand(0,9);
	return $pre.$name.$cle.'.'.$post;
}

		$upload_dir=$_POST["UPLOAD_DIR"];
		//$extension_upload=substr(strrchr($_FILES['Filedata']['name'], '.')  ,1);
		$nom=$upload_dir.$_FILES['Filedata']['name'];
		//$nom=getName($upload_dir,$extension_upload);
		//$nom=getName('upload2/',$extension_upload);
		move_uploaded_file($_FILES['Filedata']['tmp_name'],$nom);	

	$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
	
	$_SESSION["file_info"][$file_id] = $imagevariable;
	
	echo $file_id;	// Return the file id to the script
		
	
?>