<?php

// ouvrir un odf et le decompacter dans tmp/
function spipodf_unzip($fichier){
	include_spip('inc/pclzip');
	$repodf = sous_repertoire(_DIR_TMP,'odf');
	$sous_rep = basename($fichier)."/";

	$odt = new PclZip($fichier);
	$ok = $odt->extract(
		PCLZIP_OPT_PATH, $repodf,
		PCLZIP_OPT_ADD_PATH, $sous_rep,
		PCLZIP_OPT_REPLACE_NEWER
	);

	if ($odt->error_code<0)
		return $odt->error_code;
		
	return $repodf.$sous_rep;
}

// recompacter un dossier pour en faire un odf
function spipodf_zip($dossier,$dest){
	include_spip('inc/pclzip');
	$files = preg_files($dossier,".*");
	$newodt = new PclZip($dest);
	$newodt->create($files,PCLZIP_OPT_REMOVE_PATH,$dossier);
	if ($newodt->error_code<0)
		return $newodt->error_code;

	return $dest;
}

function spipodf_envoyer($fichier){
	if (!preg_match(",[.](od[tpgbf])$,i",$fichier,$regs))
		return false;
	$extension = strtolower($regs[1]);
	$res = spip_query("SELECT mime_type FROM spip_types_documents WHERE extension="._q($extension));
	if (!$row = spip_fetch_array($res))
		return false;
	$mime = $row['mime_type'];
	header('Content-Type: '.$mime);
	header('Content-Length: '.filesize($fichier));
	header('Cache-Control: max-age=36000');
	header('Pragma: public');
	header('Content-Disposition: attachment; filename=' . basename($fichier));
	readfile($fichier);
	exit;
}

?>