<?php

function ziptex_polyglossia($lang){
	// function permettant de convertir une #LANG en nom du package polyglossia
	$tableau = array(
		'fr' => 'french',
		'en' => 'english',
		'es' => 'spanish');
	return $tableau[$lang];		
}
function ziptex_dir($array){
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
		ziptex_creer_tex($spip[0],$spip[1],$spip[3]);	
	}
	$zip = zippeur(array(_DIR_ZIPTEX),date("Y-m-d H:i:s",time()),'','ziptex','local');
	return $zip;

}

function ziptex_creer_tex($squel,$nom,$options=array()){

	$contenu = recuperer_fond($squel,$options);
	ecrire_fichier(_DIR_ZIPTEX.$nom,$contenu);
	
}

function ziptex_copier_tex($orig,$dest){
	copy(find_in_path($orig),_DIR_ZIPTEX.$dest);
}
?>
