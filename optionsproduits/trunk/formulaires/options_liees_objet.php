<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('action/editer_liens');

function formulaires_options_liees_objet_charger_dist($objet, $id_objet) {
	$valeurs = array(
		'objet'           => $objet,
		'id_objet'        => $id_objet,
		'id_option'       => _request('id_option'),
		'modifier_option' => _request('modifier_option'),
		'prix'            => _request('prix'),
	);

	return $valeurs;
}

function formulaires_options_liees_objet_verifier_dist($objet, $id_objet) {
	$erreurs = array();

	if (_request('prix') && !is_numeric(_request('prix'))) {
		$erreurs['prix'] = 'Saisissez un nombre décimal avec un point';
	}

	return $erreurs;
}

function formulaires_options_liees_objet_traiter_dist($objet, $id_objet) {
	$retours = array();
	
	// modification du prix
	if(_request('modifier_option')) {
		if ($id_option = _request('id_option')) {
			$prix = _request('prix');
			if ($prix == '') {
				$prix = sql_getfetsel('prix_defaut', 'spip_options', 'id_option = ' . $id_option);
			}
			if (objet_associer(
				array('option' => $id_option),
				array($objet => $id_objet),
				array('prix_option_objet' => $prix)
			)) {
				$retours['message_ok'] = _T('option:option_ajoutee');
			}
		}
		set_request('modifier_option', '');
		
	} else {

		// association d'options
		$groupes = sql_allfetsel('id_optionsgroupe', 'spip_optionsgroupes');
		foreach ($groupes as $groupe) {
			if ($id_option = _request('id_option_groupe_' . $groupe['id_optionsgroupe'])) {
				$prix = sql_getfetsel('prix_defaut', 'spip_options', 'id_option = ' . $id_option);
				if (objet_associer(
					array('option' => $id_option),
					array($objet => $id_objet),
					array('prix_option_objet' => $prix)
				)) {
					$retours['message_ok'] = _T('option:option_ajoutee');
				}
			}
		}
		
	}
	
	return $retours;
}
