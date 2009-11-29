<?php
// Uploadify v1.6.2
// Copyright (C) 2009 by Ronnie Garcia
// Co-developed by Travis Nickels
if (!empty($_FILES)) {
/**/
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	
	// Uncomment the following line if you want to make the directory if it doesn't exist
	// mkdir(str_replace('//','/',$targetPath), 0755, true);
	
	move_uploaded_file($tempFile,$targetFile);
	/*$upload_dir = _DIR_TRANSFERT;
	$nom=$upload_dir.$_FILES['Filedata']['name'];
	@move_uploaded_file($_FILES['Filedata']['tmp_name'],$nom);	
	
	$upload_dir = _DIR_TRANSFERT;
	$nom=$upload_dir.$_FILES['Filedata']['name'];
	//if ($id_article!=0)	{
	$files = array(array (
			'name' => $_FILES['Filedata']['name'],
			'tmp_name' => $nom)
			);
	$type = "article";
	$mode = 'document';
	include_spip('action/joindre');
	joindre_documents($files, $mode, $type, 4, 0, $hash, $redirect, $actifs, $iframe_redirect);
//}*/
}
echo "1";
?>