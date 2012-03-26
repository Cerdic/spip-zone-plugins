<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function zippeur_creer_arbo($chemin,$fichier='oui'){
	$arbo = explode('/',$chemin);
	
	defined('_DIR_SITE') ? $chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES : $chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES;
	if ($fichier == 'oui'){
		array_pop($arbo);
	}
	foreach ($arbo as $rep){
		$chemin = $chemin.'/'.$rep;
		sous_repertoire($chemin);		
	}
}
function zippeur_creer_fichier($squel,$chemin,$options=array()){
	zippeur_creer_arbo($chemin);
	defined('_DIR_SITE') ? $chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.$chemin : $chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES.$chemin;
	$contenu = recuperer_fond($squel,$options);
	ecrire_fichier($chemin,$contenu);
}

function zippeur_copier_fichier($orig,$dest){
	zippeur_creer_arbo($chemin);
	defined('_DIR_SITE') ? $chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.$chemin : $chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES.$chemin;
	copy(find_in_path($orig),$chemin.$dest);
}
?>