<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de vérification des tables pour inscription3
 * Appelée à chaque validation du formulaire CFG
 */
function inc_inscription3_verifier_tables_dist(){

	include_spip('inc/cextras_gerer');
	include_spip('base/inscription3');
	$champs = inscription3_declarer_champs_extras();
	creer_champs_extras($champs);
	return;
}
?>
