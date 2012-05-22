<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_aeres_charger_dist(){
	if (isset($GLOBALS['meta']['aeres']))
		$valeurs = unserialize($GLOBALS['meta']['aeres']);
	else
		$valeurs = array(
			'debut' => '',
			'fin' => '',
			'csl' => '',
			'contact' => '',
			'conference_actes' => '',
			'format_docs' => ''
		);
	
	// Liste des membres
	if (isset($valeurs['membres']))
		$membres = explode(';',$valeurs['membres']);
	else
		$membres = array();
	include_spip('base/abstract_sql');
	$zcreators = sql_allfetsel('auteur','spip_zcreators','','auteur','auteur');
	foreach ($zcreators as $cle => $zcreator) // remise a plat du tableau
		$zcreators[$cle] = $zcreator['auteur'];
	$non_membres = array_diff($zcreators,$membres);
	
	$valeurs['membres'] = $membres;
	$valeurs['non_membres'] = $non_membres;
	
	return $valeurs;
}

function formulaires_configurer_aeres_verifier_dist(){
	$erreurs = array();
	if (!_request('debut') || !intval(_request('debut'))) $erreurs['debut'] = 'Vous devez spécifier un nombre entier.';
	if (!_request('fin') || !intval(_request('fin'))) $erreurs['fin'] = 'Vous devez spécifier un nombre entier.';
	if (!autoriser('webmestre')) $erreurs['message_erreur'] = 'Vous n\'avez pas les droits suffisants pour modifier la configuration.';
	return $erreurs;
}



function formulaires_configurer_aeres_traiter_dist(){
	$membres = _request('membres');
	if (count($membres)) sort($membres);
	else $membres = array();
	set_request('membres',$membres); // On retransmet le tableau correctement trié
	$non_membres = _request('non_membres');
	if (count($non_membres)) sort($non_membres);
	else $non_membres = array();
	set_request('non_membres',$non_membres);
	$config = array(
		'debut' => _request('debut'),
		'fin' => _request('fin'),
		'csl' => _request('csl'),
		'contact' => _request('contact'),
		'conference_actes' => _request('conference_actes'),
		'format_docs' => _request('format_docs'),
		'membres' => implode(";", $membres)
	);
	include_spip('inc/meta');
	ecrire_meta('aeres',serialize($config));
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>