<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_attribuer_daterubriques_charger_dist(){
	$valeurs['id_secteur'] = _request('id_secteur');
	return $valeurs;
}

function formulaires_attribuer_daterubriques_verifier_dist(){
	$erreurs = array();

	if (_request('id_secteur')) {
		if (!is_numeric(_request('id_secteur'))){
			$erreurs['id_secteur']=_T('daterubriques:valeur_incorrecte');
		}
	}
	return $erreurs;
}

function formulaires_attribuer_daterubriques_traiter_dist(){
	if (_request('attribuer')){
		$id_secteur=_request('id_secteur');

		include_spip('inc/attribuer_daterubriques');
		if (attribuer_date_rubriques($id_secteur)){
			return _T("daterubriques:mise_a_jour_rubriques");
		}
	}
}

?>
