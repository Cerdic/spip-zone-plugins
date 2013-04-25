<?php
/**
 * Smush
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 
 * @package SPIP\Smush\Cron
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * Par défaut tous les jours
 *
 * Vérifie la présence des binaires nécessaires
 *
 * @return
 * @param object $time
 */
function genie_smush_taches_generales($time){

	include_spip('inc/smush_verifier_binaires');
	
	tester_pngnq();
	tester_optipng();
	tester_jpegtran();
	tester_convert();
	tester_global();
	
	return 1;
}
?>