<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/mailsubscribers");
include_spip("inc/config");
include_spip("inc/cvt_configurer");

function formulaires_configurer_mailsubscribers_charger_dist(){
	$valeurs = array(
		'proposer_signup_optin' => lire_config('mailsubscribers/proposer_signup_optin',0),
		'double_optin' => lire_config('mailsubscribers/double_optin',1),
		'lists' => lire_config('mailsubscribers/lists',array()),
	);

	foreach ($valeurs['lists'] as $k => $v){
		$valeurs['lists'][$k]['id'] = mailsubscribers_filtre_liste($v['id']);
	}

	return $valeurs;
}

function formulaires_configurer_mailsubscribers_verifier_dist(){
	$erreurs = array();

	if ($lists = _request('lists')) {
		foreach ($lists as $k => $v){
			if (strlen($v['id'])  AND !strlen($v['titre'])){
				$erreurs['lists'][$k]['titre'] = _T('info_obligatoire');
			}
		}
	}

	return $erreurs;
}

function formulaires_configurer_mailsubscribers_traiter_dist(){

	if ($lists = _request('lists')) {
		foreach (_request('lists') as $k => $v){
			if (strlen(trim($v['id']))){
				$lists[$k]['id'] = mailsubscribers_normaliser_nom_liste($v['id']);
				if (!in_array($v['status'],array('open','close')))
					$lists[$k]['status'] = 'open';
			}
			else
				unset($lists[$k]);
		}
		set_request('lists',array_merge($lists)); // array_merge pour renumeroter les cles numeriques...
		ecrire_config('mailsubscribers/',array('lists'=>$lists));
	}


	$trace = cvtconf_formulaires_configurer_enregistre('configurer_mailsubscribers',array());
	$res = array('message_ok'=>_T('config_info_enregistree').$trace,'editable'=>true);

	// et on efface le request
	set_request('lists');

	return $res;
}