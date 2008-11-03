<?php
/*	*********************************************************************
	*
	* Copyright (c) 2007
	* Xavier Burot
	*
	* SPIP-ALBUM : Programme d'affichage de photos
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_album_download() {
	verifier_visiteur(); // securite...
	$file_download = find_in_path(_request('file'));
	if (file_exists($file_download)) {
		header("Content-Type: application/force-download"); 
		header("Content-Transfer-Encoding: application/octet-stream"); // Surtout ne pas enlever le \n
		header("Content-disposition: attachment; filename=".basename($file_download)); 
		header("Accept-Ranges : bytes");
		header("Content-Length: ".filesize($file_download)); 
		header("Pragma: no-cache"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public"); 
		header("Expires: 0"); 
		readfile($file_download);
	} else {
		$redirect = generer_url_public('404').'&erreur="' . basename(_request('file')) . '" ' . _T('album:err_download');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}

?>