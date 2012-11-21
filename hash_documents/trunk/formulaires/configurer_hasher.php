<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement des valeurs
 * @return array
 */
function formulaires_configurer_hasher_charger_dist(){

	list($oui, $non) = hasher_compter_documents();

	if (intval($non) > 0) {
		$nb_oui = min(intval($non), 100);
	}

	if (intval($oui) > 0) {
		$nb_non = min(intval($oui), 100);
	}

	$valeurs = array(
		'oui'=>$oui,
		'non'=>$non,
		'nb_oui'=>$nb_oui,
		'nb_non'=>$nb_non		
	);

	return $valeurs;
}


function formulaires_configurer_hasher_traiter_dist(){
   $message = array();
	switch(_request('choix_action')) {
		case 'hasher' :
			$docs = hasher_deplacer_n_documents(_request('nb_a_hasher'), false);
		break;
		
		case 'corriger' :
			$docs = hasher_deplacer_n_documents(_request('nb_a_hasher'), true);
		break;
		
		case 'dehasher' :
			$docs = hasher_deplacer_n_documents(-_request('nb_a_dehasher'), false, true);
		break;
	}

	if(isset($docs))
		if(is_array($docs))
			return array('message_ok'=>(_T('hasher:documents_modifies').join(', ', $docs)));
		else
			return array('message_erreur'=>(_T('hasher:erreur_traitement')));
	else
		return array('message_erreur'=>(_T('hasher:erreur_action')));
}

?>
