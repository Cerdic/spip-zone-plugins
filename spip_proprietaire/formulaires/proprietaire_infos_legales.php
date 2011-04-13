<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('spip_proprio_fonctions');

function formulaires_proprietaire_infos_legales_charger_dist($who='proprietaire'){
	$conf = spip_proprio_recuperer_config();
	$valeurs = array(
		'legal_forme' => $conf[$who.'_legal_forme'],
		'legal_abbrev' => $conf[$who.'_legal_abbrev'],
		'legal_genre' => $conf[$who.'_legal_genre'] ? $conf[$who.'_legal_genre'] : 'fem',
		'enregistrement_organisme' => $conf[$who.'_enregistrement_organisme'],
		'enregistrement_abbrev' => $conf[$who.'_enregistrement_abbrev'],
		'enregistrement_genre' => $conf[$who.'_enregistrement_genre'],
		'enregistrement_numero' => $conf[$who.'_enregistrement_numero'],
		'enregistrement_siren' => $conf[$who.'_enregistrement_siren'],
		'enregistrement_siret' => $conf[$who.'_enregistrement_siret'],
		'enregistrement_tvaintra' => $conf[$who.'_enregistrement_tvaintra'],
		'capital_social' => $conf[$who.'_capital_social'],
	);
	return $valeurs;
}

function formulaires_proprietaire_infos_legales_verifier_dist($who='proprietaire'){
	$erreurs = array();
	if($siren = _request('enregistrement_siren')) {
		if (!siren_valide($siren)) {
			$erreurs['enregistrement_siren'] = _T('spip_proprio:num_invalide');
		} elseif($siret = _request('enregistrement_siret')) {
			if (!siret_valide($siren, $siret)) {
				$erreurs['enregistrement_siret'] = _T('spip_proprio:num_invalide');
			} elseif($tva = _request('enregistrement_tvaintra')) {
				if ($tva!=tva_intracom_valide($tva.$siren)) {
					$erreurs['enregistrement_tvaintra'] = _T('spip_proprio:num_invalide');
				}
			}
		}
	}
	return $erreurs;
}

function formulaires_proprietaire_infos_legales_traiter_dist($who='proprietaire'){
	$messages = array();
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
		$who.'_capital_social' => _request('capital_social'),
	);
	if (strlen($datas[$who.'_enregistrement_siren'])) {
		list(
			$datas[$who.'_enregistrement_siren'],
			$datas[$who.'_enregistrement_siret'],
			$datas[$who.'_enregistrement_tvaintra']
		) = completer_insee(
			$datas[$who.'_enregistrement_siren'], 
			$datas[$who.'_enregistrement_siret']
		);
		$datas[$who.'_enregistrement_numero'] = '';
	    $redirect = generer_url_ecrire('spip_proprio', 'page='.$who);
		$messages['redirect'] = $redirect;
	}
	if( $ok = spip_proprio_enregistrer_config($datas) ) {
		$messages['message_ok'] = _T('spip_proprio:ok_config');
	} else {
		$messages['message_erreur'] = _T('spip_proprio:erreur_config');
	}
	return $messages;
}
?>