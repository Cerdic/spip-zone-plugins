<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_proprietaire_copyright_charger_dist(){
	$valeurs = array(
		'copyright_annee' => '',
		'copyright_complement' => '',
		'copyright_comment' => _T('spip_proprio:copyright_default_comment'),
	);
	$datas = spip_proprio_recuperer_config();
	if($datas AND count($datas)) $valeurs = array_merge($valeurs, $datas);
	return $valeurs;
}

function formulaires_proprietaire_copyright_verifier_dist(){
	$erreurs = array();

	return $erreurs;
}

function formulaires_proprietaire_copyright_traiter_dist(){
	$datas = array(
		'copyright_annee' => _request('copyright_annee'),
		'copyright_complement' => _request('copyright_complement'),
		'copyright_comment' => _request('copyright_comment'),
	);
	if( $ok = spip_proprio_enregistrer_config($datas) )
		return array('message_ok' => _T('spip_proprio:ok_config'));
	return array('message_erreur' => _T('spip_proprio:erreur_config'));
}
?>