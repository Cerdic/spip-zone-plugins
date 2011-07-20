<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_attribuer_date_rubriques_charger_dist(){
	$valeurs['id_secteur'] = _request('id_secteur');
	return $valeurs;
}

function formulaires_attribuer_date_rubriques_verifier_dist(){
	// tester l'url si ajout
	$erreurs = array();
	
	if (_request('id_secteur')) {
		if (!is_numeric(_request('id_secteur'))){
			$erreurs['id_secteur']=_T('daterubriques:valeur_incorrecte');
		}
	}
	return $erreurs;
}

function formulaires_attribuer_date_rubriques_traiter_dist(){
	if (_request('attribuer')){
		$id_secteur=_request('id_secteur');

		include_spip('inc/attribuer_date_rubriques');
		if (attribuer_date_rubriques($id_secteur)){
			return _T("daterubriques:mise_a_jour_rubriques");
		}
	}
}

?>
