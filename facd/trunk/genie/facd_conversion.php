<?php
/**
 * Fichier des fonctions utilisées en CRON
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * On lance une conversion
 * Si la conversion n'est pas ok ...
 * On donne un statut "erreur" dans la file afin de ne pas la bloquer
 *
 * @param object $time
 * @return int
 */
function genie_facd_conversion($time) {
	spip_log('début de tache cron', 'facd');
	$traiter = charger_fonction('facd_traiter_conversion', 'action');
	$traiter();
	spip_log('fin de tache cron', 'facd');
	return 1;
}
