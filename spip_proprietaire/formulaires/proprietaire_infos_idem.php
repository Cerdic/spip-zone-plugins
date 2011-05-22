<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_proprietaire_infos_idem_charger_dist($who){
	$conf = spip_proprio_recuperer_config();
	$valeurs = array(
		'who' => $who,
		'idem' => isset($conf[$who.'_idem']) && strlen($conf[$who.'_idem']) ? $conf[$who.'_idem'] : 'non',
	);
	return $valeurs;
}

function formulaires_proprietaire_infos_idem_verifier_dist($who){
	$erreurs = array();
	return $erreurs;
}

function formulaires_proprietaire_infos_idem_traiter_dist($who){
	$datas = array(
		$who.'_idem' => ($oui = _request('idem') AND $oui!='non') ? $oui : '',
	);
	if( $ok = spip_proprio_enregistrer_config($datas) )
		return array('message_ok' => _T('spip_proprio:ok_config'));
	return array('message_erreur' => _T('spip_proprio:erreur_config'));
}
?>