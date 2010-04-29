<?php

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
	
  $message = array('editable'=>true, 'message_ok'=>'');
	
	
	sql_updateq('spip_syndic',array(
      'rssarticle'=>_request('rssarticle'),
      'resume'=>'non',
      'oubli'=>'oui'  
  ),'id_syndic='.intval($id_syndic));
  
  include_spip('inc/headers');
  $message .= redirige_par_entete("./?exec=sites&id_syndic=$id_syndic");

	return $message;
	
}

?>
