<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_SWFUPLOAD ($p) {
	$p = calculer_balise_dynamique($p,'SWFUPLOAD', array());
	return $p;
}

// stat
function balise_SWFUPLOAD_stat($args, $filtres) {
		return $args;
}

// dyn
function balise_SWFUPLOAD_dyn($str="")
{
  
  if (isset($GLOBALS['auteur_session']['statut']) &&
		 ($GLOBALS['auteur_session']['statut']!="0minirezo")) {
      spip_log("swfupload: user inconnu"); 
  } else {

	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	/*
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}
	session_start();
*/
    
  // Check the upload
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		
		exit(0);
	}

	$upload_dir = _DIR_TRANSFERT;
	$nom=$upload_dir.$_FILES['Filedata']['name'];
	@move_uploaded_file($_FILES['Filedata']['tmp_name'],$nom);	
	spip_log("swfupload: $nom");  

	$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
	return $file_id;

  }
}

?>
