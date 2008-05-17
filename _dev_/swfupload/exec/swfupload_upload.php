<?php
include_spip('inc/actions'); // *action_auteur et determine_upload

function exec_swfupload_upload()
{

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

		//$upload_dir=$_POST["UPLOAD_DIR"];
		$upload_dir = determine_upload();
		$nom=$upload_dir.$_FILES['Filedata']['name'];
		@move_uploaded_file($_FILES['Filedata']['tmp_name'],$nom);	


	$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
	
	//$_SESSION["file_info"][$file_id] = $imagevariable;
	
	echo $file_id;	// Return the file id to the script
}
	
?>