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
		include_spip('inc/mailshot');
		list($periode,$nb) = mailshot_cadence();
		$taches_generales['mailshot_bulksend'] = $periode;
	}

	return $taches_generales;
}

function mailshot_afficher_fiche_objet($flux){
	if ($flux['args']['type']=='mailshot'
	  AND $id_mailshot = intval($flux['args']['id'])){

		$flux['data'] = preg_replace(",(<h1[^>]*>).*(</h1>),Uims","\\1"._T("mailshot:info_mailshot_no",array('id'=>$id_mailshot))."\\2",$flux['data'],1);
	}
	return $flux;
}

function mailshot_afficher_complement_objet($flux){
	if ($flux['args']['type']=='mailshot'
	  AND $id_mailshot=intval($flux['args']['id'])){
		#ajouter la liste des envois
		$contexte = array('id_mailshot'=>$id_mailshot);
		if (_request('recherche'))
			$contexte['recherche'] = _request('recherche');
		$flux['data'] .= recuperer_fond("prive/squelettes/contenu/inc-mailshot-destinataires",$contexte,array('ajax'=>true));
	}
	return $flux;
}

/**
 * Quand le statut d'un mailshot change, mettre a jour la date aussi
 *
 * @param $flux
 * @return mixed
 */
function mailshot_pre_edition($flux){
	if ($flux['args']['table']=='spip_mailshots'
	  AND $flux['args']['action']=='instituer'
	  AND $id_mailshot = $flux['args']['id_objet']
	  AND $statut_ancien = $flux['args']['statut_ancien']
	  AND isset($flux['data']['statut'])
		AND !isset($flux['data']['date'])
	  AND $statut = $flux['data']['statut']
	  AND $statut != $statut_ancien){

		$flux['data']['date'] = date('Y-m-d H:i:s');
	}
	return $flux;
}

/**
 * Quand le statut d'un mailshot change, mettre a jour la meta processing
 *
 * @param $flux
 * @return mixed
 */
function mailshot_post_edition($flux){
	if ($flux['args']['table']=='spip_mailshots'
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