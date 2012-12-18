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

	// on active la tache cron uniquement si necessaire (un envoi en cours)
	if (isset($GLOBALS['meta']['mailshot_processing'])){
		$taches_generales['mailshot_bulksend'] = 60;
	}

	return $taches_generales;
}


/**
 * Quand le statut d'un mailshot change, mettre a jour la meta processing
 *
 * @param $flux
 * @return mixed
 */
function mailshot_post_edition($flux){
	if ($flux['args']['table']=='spip_mailshot'
	  AND $flux['args']['action']=='instituer'
	  AND $id_mailshot = $flux['args']['id_objet']
	  AND $statut_ancien = $flux['args']['statut_ancien']
	  AND isset($flux['data']['statut'])
	  AND $statut = $flux['data']['statut']
	  AND $statut != $statut_ancien
	  AND ($statut=='processing' OR $statut_ancien=='processing')){

		include_spip("inc/mailshot");
		mailshot_update_meta_processing();
	}
	return $flux;
}
?>