<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');

/**
 * CVT: charger
 *
 */
function formulaires_editer_rssarticle_charger_dist($id_syndic='new', $retour=''){

	$rssarticle = sql_getfetsel('rssarticle','spip_syndic','id_syndic='.intval($id_syndic));
	$valeurs['rssarticle'] = $rssarticle;
	$valeurs['id_syndic'] = $id_syndic;
	$valeurs['editable'] = true;
	
	if (!autoriser('modifier', 'syndic', $id_syndic))
		$valeurs['editable'] = false;

	return $valeurs;
}

/**
 * CVT: verifer
 *
 */
function formulaires_editer_rssarticle_verifier_dist($id_syndic='new', $retour=''){
	$erreurs = array();
	return $erreurs;
}

/**
 * CVT: traiter
 *
 */
function formulaires_editer_rssarticle_traiter_dist($id_syndic='new', $retour=''){
	if (_request('rssarticle')=='oui') {
		sql_updateq('spip_syndic',array(
			'rssarticle'=> 'oui',
			'resume'=>'non',
			'oubli'=>'oui'
		),'id_syndic='.intval($id_syndic));
		
		// on force le site en mode oubli et pas resume 
		// on rensynchronise la syndic pour passer les anciens articles (qui etaient ss doute en mode resume) en mode complet HTML
		// sql_delete("spip_syndic_articles", "id_syndic=".sql_quote($id_syndic)); // alternative ;)
		include_spip('genie/syndic');
		$t = syndic_a_jour($id_syndic);
	} else {
		sql_updateq('spip_syndic',array('rssarticle'=> 'non'),'id_syndic='.intval($id_syndic)); 
	}
	$message = array('editable'=>true, 'message_ok'=>_T("rssarticle:site_maj"));

	return $message;
}

?>