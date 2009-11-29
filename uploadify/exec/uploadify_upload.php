<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_uploadify_upload_dist()
{
//if (!empty($_FILES)) {
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "invalid upload";
		
		exit(0);
	}
	
	$tempFile = $_FILES['Filedata']['tmp_name'];
	//$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . _DIR_TMP . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	
	// Uncomment the following line if you want to make the directory if it doesn't exist
	// mkdir(str_replace('//','/',$targetPath), 0755, true);
	
	@move_uploaded_file($tempFile,$targetFile);
	
	/*
	$upload_dir = _DIR_TMP;
	$nom=$upload_dir.$_FILES['Filedata']['name'];
	@move_uploaded_file($_FILES['Filedata']['tmp_name'],$nom);	
	*/
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
unlink($nom);
}
//}
echo "1"; // Important so upload will work on OSX
}
?>