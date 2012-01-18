<?php

function docx_to_html($filepath) {
	include_once dirname(__FILE__).'/postoffice/class.PostOffice.php';
	include_once dirname(__FILE__).'/postoffice/class.DOCX-HTML.php';

	$_debug = false;

	global $PostOffice;
	//Initiate The PostOffice Class for Extraction
	$PostOffice = new PostOffice($filepath,$_debug);
	if(!$PostOffice){
		$result = "11. The files contents could not be extracted.";
		update_option('postoffice_last_result', $result);
		write_log($result, $file_name, $file_size);
		if ($_debug == "true") {
			echo $result . "<br />" . $returnlink;
			error_reporting(E_ALL ^ E_NOTICE);
		} else {
			header($return, true, 302);
			echo $result . "<br />" . $returnlink;
		}
	    exit(0);
	}

	$extract = new DOCXtoHTML();

	$extract->docxPath = $filepath;
	$extract->tempDir = $PostOffice->tempDir; # URK c'est dans le rep du plugin !
	$path_info = pathinfo($filepath);
	$extract->content_folder = strtolower(str_replace("." . $path_info['extension'], "", str_replace(" ", "-", $path_info['basename'])));
	$extract->image_max_width = $postoffice_max_image;
	$extract->imagePathPrefix = _DIR_TMP;#plugins_url();
	if (isset($_POST['postoffice_original_images'])) {
		$extract->keepOriginalImage = ($_POST['postoffice_original_images'] == "true") ? true : false;
	} else {
		$extract->keepOriginalImage = false;
	}
	if (isset($_POST['postoffice_split'])) {
		$extract->split = ($_POST['postoffice_split'] == "true") ? true : false;
	} else {
		$extract->split = false;
	}
	if (isset($_POST['postoffice_colors'])) {
		$extract->allowColor = ($_POST['postoffice_colors'] == "true") ? true : false;
	} else {
		$extract->allowColor = false;
	}	
	$extract->Init();

	//handle the output of the class and define variables needed for the WP post
	$d = $extract->output;

	return $d[0];
}

function extracteur_docx($filepath, &$charset) {
	$charset = 'utf-8';

	$html = docx_to_html($filepath);

	include_spip('inc/sale');
	return sale($html);

}

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['docx'] = 'extracteur_docx';


?>
