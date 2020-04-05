<?php
/**
 * Plugin Inscription3 pour SPIP
 * © cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de vérification des tables pour inscription3
 * Appelée à chaque validation du formulaire CFG
 */
function inc_inscription3_verifier_tables_dist(){
	include_spip('inc/cextras');
	include_spip('base/inscription3');
	$champs = inscription3_declarer_champs_extras();
	foreach($champs as $table=>$saisies){
		$ret = champs_extras_creer($table, $saisies);
	}
	return;
}
?>
