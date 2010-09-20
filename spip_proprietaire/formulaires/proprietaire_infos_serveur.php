<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_proprietaire_infos_serveur_charger_dist(){
	$valeurs = array(
		'type_serveur' => '',
		'os_serveur' => '',
		'os_serveur_web' => 'http://',
	);
	$datas = spip_proprio_recuperer_config();
	if($datas AND count($datas)) $valeurs = array_merge($valeurs, $datas);
	return $valeurs;
}

function formulaires_proprietaire_infos_serveur_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_proprietaire_infos_serveur_traiter_dist(){
	$datas = array(
		'type_serveur' => _request('type_serveur'),
		'os_serveur' => _request('os_serveur'),
		'os_serveur_web' => _request('os_serveur_web'),
	);
	if( $ok = spip_proprio_enregistrer_config($datas) )
		return array('message_ok' => _T('spip_proprio:ok_config'));
	return array('message_erreur' => _T('spip_proprio:erreur_config'));
}
?>