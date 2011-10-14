<?php
/**
 * saveauto : plugin de sauvegarde automatique de la base de donnees de SPIP
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

//function cfg_config_saveauto_post_traiter(&$cfg){}

/*
 * recuperer les erreurs pour affichage
 *
 */
function cfg_config_saveauto_verifier(&$cfg){
   $modifs = $cfg->log_modif;
   $valeurs = $cfg->val;

   // vérif des valeurs 
	$erreurs = array();

	// tester l'existence du repertoire de stockage
	if(defined('_DIR_SITE')) 
		$racine = _DIR_SITE;
	else 
		$racine = _DIR_RACINE;
	if (!file_exists($racine.$valeurs["rep_bases"])) {
		$erreurs["rep_bases"] = _T('saveauto:erreur_repertoire_inexistant',array('rep'=>$racine.$valeurs["rep_bases"]));
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('saveauto:erreurs_config');
	}
	return $erreurs;
}
