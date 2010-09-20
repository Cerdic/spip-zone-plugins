<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

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
		'capital_social' => $conf[$who.'_capital_social'],
	);
	return $valeurs;
}

function formulaires_proprietaire_infos_legales_verifier_dist($who='proprietaire'){
	$erreurs = array();

	return $erreurs;
}

function formulaires_proprietaire_infos_legales_traiter_dist($who='proprietaire'){
	$datas = array(
		$who.'_legal_forme' => _request('legal_forme'),
		$who.'_legal_abbrev' => _request('legal_abbrev'),
		$who.'_legal_genre' => _request('legal_genre'),
		$who.'_enregistrement_organisme' => _request('enregistrement_organisme'),
		$who.'_enregistrement_abbrev' => _request('enregistrement_abbrev'),
		$who.'_enregistrement_genre' => _request('enregistrement_genre'),
		$who.'_enregistrement_numero' => _request('enregistrement_numero'),
		$who.'_capital_social' => _request('capital_social'),
	);
	if( $ok = spip_proprio_enregistrer_config($datas) )
		return array('message_ok' => _T('spip_proprio:ok_config'));
	return array('message_erreur' => _T('spip_proprio:erreur_config'));
}
?>