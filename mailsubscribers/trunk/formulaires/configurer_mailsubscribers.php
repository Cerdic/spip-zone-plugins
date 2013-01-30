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

/**
 * Enregistrement de la configuration de mailsubscribers
 *
 * @note
 *   La liste des listes de diffusion déclarées en configuration
 *   est systématiquement complétée par celles présentes réellement
 *   en base dans spip_mailsubscribers. Dès lors, modifier l'identifiant
 *   d'une liste induit aussi de modifier spip_mailsubscribers.
 *
 * @return array
**/
function formulaires_configurer_mailsubscribers_traiter_dist(){

	if ($lists = _request('lists')) {
		foreach (_request('lists') as $k => $v) {

			$id_bak = mailsubscribers_normaliser_nom_liste($v['id_bak']); # ancien nom d'identifiant.
			unset($v['id_bak']);

			if (strlen(trim($v['id']))) {
				$lists[$k]['id'] = mailsubscribers_normaliser_nom_liste($v['id']);
				if (!in_array($v['status'],array('open','close'))) {
					$lists[$k]['status'] = 'open';
				}

				if ($lists[$k]['id'] != $id_bak) {
					mailsubscribers_renommer_identifiant_liste($id_bak, $lists[$k]['id']);
				}
			}
			else {
				unset($lists[$k]);
			}
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
