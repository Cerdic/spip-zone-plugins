<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function zippeur_creer_fichier($squel,$chemin,$options=array()){
	defined('_DIR_SITE') ? $chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.$chemin : $chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES.$chemin;
	$contenu = recuperer_fond($squel,$options);
	ecrire_fichier($chemin,$contenu);
}
	
?>