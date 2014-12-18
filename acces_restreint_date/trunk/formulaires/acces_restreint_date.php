<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_acces_restreint_date_charger_dist($objet, $id_objet, $retour=''){
	if (!$objet or !$id_objet or !autoriser('administrer', 'zone', 0)){
		return false;
	}
	
	$contexte = array(
		'quand' => '',
		'duree' => '',
		'periode' => '',
		'id_zone' => '',
		'supprimer' => array(),
		'_objet' => $objet,
		'_id_objet' => $id_objet,
	);
	
	return $contexte;
}

function formulaires_acces_restreint_date_verifier_dist($objet, $id_objet, $retour=''){
	$erreurs = array();
	
	if (!$supprimer = _request('supprimer')){
		if (!in_array(_request('quand'), array('avant', 'apres'))){
			$erreurs['quand'] = _T('info_obligatoire');
		}
		if (intval(_request('duree')) <= 0) {
			$erreurs['duree'] = _T('info_obligatoire');
		}
		if (!in_array(_request('periode'), array('jours', 'mois'))){
			$erreurs['periode'] = _T('info_obligatoire');
		}
		if (intval(_request('id_zone')) <= 0) {
			$erreurs['id_zone'] = _T('info_obligatoire');
		}
	}
	
	if (!$erreurs) {
		set_request('objet', $objet);
		set_request('id_objet', $id_objet);
	}
	
	return $erreurs;
}

function formulaires_acces_restreint_date_traiter_dist($objet, $id_objet, $retour=''){
	// Si on demande à enregistrer une nouvelle config
	if (!$supprimer = _request('supprimer')){
		include_spip('inc/editer');
		$retours = formulaires_editer_objet_traiter('zones_dates','new','','',$retour,'','','');
	}
	// Sinon c'est pour en supprimer
	elseif (is_array($supprimer)){
		foreach ($supprimer as $id_zones_date=>$valeur){
			if ($id_zones_date = intval($id_zones_date)){
				sql_delete('spip_zones_dates', 'id_zones_date = '.$id_zones_date);
				$retours = array('redirect' => $retour);
			}
		}
	}
	
	return $retours;
}
