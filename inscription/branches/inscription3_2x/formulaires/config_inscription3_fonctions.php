<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Fonctions de traitement du formulaire de configuration
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de pré-traitement du formulaire de configuration CFG
 * On supprime l'ancienne configuration pour avoir la nouvelle dans l'ordre
 * 
 * @param unknown_type $cfg
 */
function cfg_config_inscription3_pre_traiter(&$cfg){
	include_spip('inc/meta');
	effacer_meta('inscription3');
}

/**
 * Fonction de post-traitement du formulaire de configuration CFG
 * Crée les champs dans la table spip_auteurs_elargis dès la validation du CFG
 * 
 * @param unknown_type $cfg
 */
function cfg_config_inscription3_post_traiter(&$cfg){
	$verifier_tables = charger_fonction('inscription3_verifier_tables','inc');
	$verifier_tables();
}
?>
