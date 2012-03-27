<?php

function ziptex_polyglossia($lang){
	// function permettant de convertir une #LANG en nom du package polyglossia
	$tableau = array(
		'en' => 'english',
		'es' => 'spanish',
		'fr' => 'french');
	return $tableau[$lang];		
}
function ziptex_dir($array){
	sous_repertoire(_DIR_RACINE_DIR_ZIPTEX);
	foreach ($array as $dir){
		sous_repertoire(_DIR_ZIPTEX.$dir);	
	}
}
function ziptex_zipper($array){
	// $array
		// 0. Un tableau contenant les .tex direct, avec à chaque fois :
		// 		0. Chemin du fichier .tex dans l'arborescence SPIP
		//		1. Chemin du fichier .tex dans le future ZIP
		// 1. Un tableau contenant les squelette avec à chaque fois :
		// 		0. Nom du squelette
		//		1. Nom du .tex correspondant, avec le .tex
		// 		2. Option du squelette
	# Création des repertoires ad hoc
	sous_repertoire(_DIR_ZIPTEX);
	foreach ($array[0] as $direct){
		ziptex_copier_tex(find_in_path($direct[0]),$direct[1]);
	}
	foreach ($array[1] as $spip){
		ziptex_creer_tex($spip[0],$spip[1],$spip[2]);	
	}
	defined('_DIR_SITE')  ? $zip = zippeur(array(_DIR_ZIPTEX),date("Y-m-d H:i:s",time()),'','ziptex',_DIR_SITE.'local') : $zip = zippeur(array(_DIR_ZIPTEX),date("Y-m-d H:i:s",time()),'','ziptex','local') ;
	return $zip;

}

function ziptex_creer_tex($squel,$nom,$options=array()){
	
	$contenu = recuperer_fond($squel,$options);
	ecrire_fichier(_DIR_ZIPTEX.$nom,$contenu);
	
}

function ziptex_copier_tex($orig,$dest){
	copy(find_in_path($orig),_DIR_ZIPTEX.$dest);
}
function ziptex_copier_img($orig){
	/* Récuperation de l'extension */
	$match = array();
	if (preg_match(",\.([^.]+)$,", $orig, $match)){
		$ext = $match[1];
	}
	
	/* Cas particulier des .gif qui seront converti en .png*/
	if ($ext == 'gif'){
		include_spip('filtres/images_transforme');
		$orig = image_format($orig);
		$ext  = 'png';
	}
	
	/* Copie */
	
	$destination = _DIR_ZIPTEX._DIR_ZIPTEX_IMG.md5($orig).'.'.$ext;
	sous_repertoire(_DIR_ZIPTEX._DIR_ZIPTEX_IMG);
	copy($orig,$destination);
	return _DIR_ZIPTEX_IMG.basename($destination);
}
?>
