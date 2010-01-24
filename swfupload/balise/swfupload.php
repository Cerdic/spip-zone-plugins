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
  @chmod($nom, 0777);
	spip_log("swfupload: $nom");  

	$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);	
  
    $id_article=intval(_request('id_article'));
	
	if ($id_article!=0)	{
	$files = array(array (
			'name' => $_FILES['Filedata']['name'],
			'tmp_name' => $nom)
			);
	$type = "article";
	$mode = 'document';
	include_spip('action/joindre');
	joindre_documents($files, $mode, $type, $id_article, 0, $hash, $redirect, $actifs, $iframe_redirect);
					}
	return $file_id;			
	}
}

?>
