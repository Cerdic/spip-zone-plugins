<?php

// ouvrir un odf et le decompacter dans tmp/
function spipoasis_unzip($fichier){
	include_spip('inc/pclzip');
	$repodf = sous_repertoire(_DIR_TMP,'oasis');
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
function spipoasis_zip($dossier,$dest){
	include_spip('inc/pclzip');
	$files = preg_files($dossier,".*");
	$newodt = new PclZip($dest);
	$newodt->create($files,PCLZIP_OPT_REMOVE_PATH,$dossier);
	if ($newodt->error_code<0)
		return $newodt->error_code;

	return $dest;
}

// ecrire le meta.xml a partir du template meta.xml.html
function spipoasis_ecrire_meta($odf_dir,$contexte){
	// calculer le fond
	include_spip('inc/assembler');
	$texte = recuperer_fond("templates/meta.xml",$contexte);
	ecrire_fichier($odf_dir."meta.xml",$texte,true);
}

function spipoasis_recuperer_fond($template,$contexte,$nom_fichier){
	if (!preg_match(",[.](od[tpgbf])$,i",$template,$regs))
		return false;
	$extension = strtolower($regs[1]);
	include_spip("inc/charsets");
	$nom_fichier = preg_replace(",[^\w],","_",translitteration($nom_fichier));
	$nom_fichier = preg_replace(",_[_]+,","_",$nom_fichier);
	
	$contexte['fond']=$template;
	
	$nom_cache = $nom_fichier."-".substr(md5(serialize($contexte)),0,16).".$extension";
	$nom_fichier .= ".$extension";
	$repcache = sous_repertoire(_DIR_CACHE,'oasis');
	$cache = $repcache.$nom_cache;
	$template = find_in_path("templates/$template");
	if (!file_exists($cache)
		OR ($fmt=filemtime($cache))<time()-24*3600
		OR $fmt<filemtime($template)
		OR $GLOBALS['debug_oasis']
		){
		if (!strlen($template))
			return;
		$styliser = charger_fonction("spip2{$extension}_styliser",'inc');
		$unzip = spipoasis_unzip($template);
		// styliser un odf
		$styliser($unzip,$contexte);
		$cache = spipoasis_zip($unzip,$cache);
	}
	return array($cache,$nom_fichier);
}

function spipoasis_envoyer($fichier, $alias = NULL){
	if ($alias==NULL) $alias=basename($fichier);
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
	header('Content-Disposition: attachment; filename=' . $alias);
	readfile($fichier);
	exit;
}

?>