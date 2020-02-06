<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/spip_proprio_insee');

function formulaires_proprietaire_infos_legales_charger_dist($who = 'proprietaire', $mode = false) {
	$conf = spip_proprio_recuperer_config();

	$valeurs = array(
		'who' => $who,
		'legal_forme' => isset($conf[$who.'_legal_forme']) ? $conf[$who.'_legal_forme'] : '',
		'legal_abbrev' => isset($conf[$who.'_legal_abbrev']) ? $conf[$who.'_legal_abbrev'] : '',
		'legal_genre' => isset($conf[$who.'_legal_genre']) ? $conf[$who.'_legal_genre'] : 'fem',
		'enregistrement_organisme' => isset($conf[$who.'_enregistrement_organisme']) ? $conf[$who.'_enregistrement_organisme'] : '',
		'enregistrement_abbrev' => isset($conf[$who.'_enregistrement_abbrev']) ? $conf[$who.'_enregistrement_abbrev'] : '',
		'enregistrement_genre' => isset($conf[$who.'_enregistrement_genre']) ? $conf[$who.'_enregistrement_genre'] : '',
		'enregistrement_numero' => isset($conf[$who.'_enregistrement_numero']) ? $conf[$who.'_enregistrement_numero'] : '',
		'enregistrement_siren' => isset($conf[$who.'_enregistrement_siren']) ? $conf[$who.'_enregistrement_siren'] : '',
		'enregistrement_siret' => isset($conf[$who.'_enregistrement_siret']) ? $conf[$who.'_enregistrement_siret'] : '',
		'enregistrement_tvaintra' => isset($conf[$who.'_enregistrement_tvaintra']) ? $conf[$who.'_enregistrement_tvaintra'] : '',
		'enregistrement_tva_nonapplicable' => (isset($conf[$who.'_enregistrement_tva_nonapplicable']) and $conf[$who.'_enregistrement_tva_nonapplicable'] == true) ? 'oui' : 'non',
		'capital_social' => isset($conf[$who.'_capital_social']) ? $conf[$who.'_capital_social'] : '',
		'proposer_enregistrement' => 'libre',
	);

	// Cas particulier si numero FR
	if (($mode && $mode == 'auto_fr') || (isset($conf[$who.'_enregistrement_siren']) and $conf[$who.'_enregistrement_siren'] != '')) {
		$valeurs['proposer_enregistrement'] = 'auto_fr';
	}

	return $valeurs;
}

function formulaires_proprietaire_infos_legales_verifier_dist($who = 'proprietaire') {
	$erreurs = array();

	if ($siren = _request('enregistrement_siren')) {
		if (!siren_valide($siren)) {
			$erreurs['enregistrement_siren'] = _T('spipproprio:num_invalide');
		} elseif ($siret = _request('enregistrement_siret')) {
			if (!siret_valide($siren, $siret)) {
				$erreurs['enregistrement_siret'] = _T('spipproprio:num_invalide');
			} elseif ($tva = _request('enregistrement_tvaintra')) {
				if ($tva != tva_intracom_valide($tva.$siren)) {
					$erreurs['enregistrement_tvaintra'] = _T('spipproprio:num_invalide');
				}
			}
		}
	}

	return $erreurs;
}

function formulaires_proprietaire_infos_legales_traiter_dist($who = 'proprietaire') {
	$messages = array();

	// Recuperation des valeurs
	$datas = array(
		$who.'_legal_forme' => _request('legal_forme'),
		$who.'_legal_abbrev' => _request('legal_abbrev'),
		$who.'_legal_genre' => _request('legal_genre'),
		$who.'_enregistrement_organisme' => _request('enregistrement_organisme'),
		$who.'_enregistrement_abbrev' => _request('enregistrement_abbrev'),
		$who.'_enregistrement_genre' => _request('enregistrement_genre'),
		$who.'_enregistrement_numero' => _request('enregistrement_numero'),
		$who.'_enregistrement_siren' => _request('enregistrement_siren'),
		$who.'_enregistrement_siret' => _request('enregistrement_siret'),
		$who.'_enregistrement_tvaintra' => _request('enregistrement_tvaintra'),
		$who.'_enregistrement_tva_nonapplicable' => (_request('enregistrement_tva_nonapplicable') && _request('enregistrement_tva_nonapplicable') == 'oui') ? true : false,
		$who.'_capital_social' => _request('capital_social'),
	);

	// Traitements
	$num_mode = _request('num_mode_hidden');
	if (strlen($datas[$who.'_enregistrement_siren']) > 0 && $num_mode == 'auto_fr') {
		list(
			$datas[$who.'_enregistrement_siren'],
			$datas[$who.'_enregistrement_siret'],
			$datas[$who.'_enregistrement_tvaintra']
		) = completer_insee(
						$datas[$who.'_enregistrement_siren'],
						$datas[$who.'_enregistrement_siret'],
						$datas[$who.'_enregistrement_tva_nonapplicable']
		);
		$datas[$who.'_enregistrement_numero'] = '';
		$redirect = generer_url_ecrire('spip_proprio', 'page='.$who);
//		$messages['redirect'] = $redirect;
	} else {
		$datas[$who.'_enregistrement_siren'] = '';
		$datas[$who.'_enregistrement_siret'] = '';
		$datas[$who.'_enregistrement_tvaintra'] = '';
	}

	// Enregistrement et retour
	if ($ok = spip_proprio_enregistrer_config($datas)) {
		$messages['message_ok'] = _T('spipproprio:ok_config');
	} else {
		$messages['message_erreur'] = _T('spipproprio:erreur_config');
	}

	return $messages;
}
