<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/step');
include_spip('inc/step_presentation');

function formulaires_gerer_zones_plugins_charger_dist(){
	return array();
}

function formulaires_gerer_zones_plugins_verifier_dist(){
	// tester l'url si ajout
	$erreurs = array();
	
	if (_request('ajouter_zone')) {
		if (!step_verifier_adresse_zone($url = _request('adresse_zone'))){
			$erreurs['adresse_zone']=_T('step_zone_adresse_incorrecte');
		}
		elseif (sql_countsel('spip_zones_plugins','adresse='.sql_quote(trim($url)))){
			$erreurs['adresse_zone']=_T('step_zone_adresse_presente');
		}
	}
	return $erreurs;
}

function formulaires_gerer_zones_plugins_traiter_dist(){
	if (_request('ajouter_zone')) {
		$zone = _request('adresse_zone');
		if ($nb = step_ajouter_zone($zone)) {
			return _L("La zone $zone a ete ajoutee");
		}
	} 
	
	elseif (_request('actualiser')){
		if (step_update()){
			return _L("Les zones ont ete mises a jour");
		}
	}
}

?>
