<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifier le type mime d'un fichier envoyé par swfupload
 */
function swfupload_verifier_mime($files){
	if (function_exists('finfo_open')) {
		$f = finfo_open(FILEINFO_MIME);
		$mime = finfo_file($f, $files['Filedata']['tmp_name']);
		$mime = explode(';',$mime);
		$mime = $mime[0];
		finfo_close($f);
		$files['Filedata']['type'] = $mime;
		spip_log("1. on change le mime-type => $mime","swfupload");
	}
	elseif (class_exists('finfo')) {
		$f = new finfo(FILEINFO_MIME);
		$files['Filedata']['type'] = $f->file($files['Filedata']['tmp_name']);
		spip_log("2. on change le mime-type => $mime","swfupload");
	}
	elseif (strlen($mime=@shell_exec("file -bi ".escapeshellarg($files['Filedata']['tmp_name'])))!=0) {
		//Using shell if unix an authorized
		$files['Filedata']['type'] = trim($mime);
		$mime = $files['Filedata']['type'];
		spip_log("3. on change le mime-type => $mime","swfupload");
	}
	elseif (function_exists('mime_content_type')) {
		//Double check the mime-type with magic-mime if avaible
		$files['Filedata']['type'] = mime_content_type($files['Filedata']['tmp_name']);
		$mime = $files['Filedata']['type'];
		spip_log("4. on change le mime-type => $mime","swfupload");
	}
	if ($files['Filedata']['type'] && preg_match(',odt,',$files['Filedata']['name']) && ($files['Filedata']['type'] != 'application/vnd.oasis.opendocument.text')){
		$files['Filedata']['type'] = 'application/vnd.oasis.opendocument.text';
		spip_log("5. on change le mime-type => $mime","swfupload");
	}
	return $files;
}
?>
