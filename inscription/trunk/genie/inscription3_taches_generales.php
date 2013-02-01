<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * Par défaut tous les jours
 *
 * Réalise plusieurs actions :
 * -* vérifie s'il y a des comptes à valider / invalider et notifier les admins
 *
 * @return
 * @param object $time
 */
function genie_inscription3_taches_generales($time){
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('i3_inscriptionauteur', 0,
			array('verifier_confirmer'=>'oui')
		);
	}
	return 1;
}
?>