<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Tache periodique d'envoi
 *
 * @param array $taches_generales
 * @return array
 */
function mailshot_taches_generales_cron($taches_generales){

	$taches_generales['mailshot_bulksend'] = 60;

	return $taches_generales;
}


?>