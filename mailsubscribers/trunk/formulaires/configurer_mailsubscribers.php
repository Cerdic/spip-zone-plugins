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
 *   en base dans spip_mailsubscribers.
 * 
 *   Changer l'identifiant d'une liste nécessite aussi de le
 *   modifier dans spip_mailsubscribers.
 *
 * @return array
**/
function formulaires_configurer_mailsubscribers_traiter_dist(){

	if ($lists = _request('lists')) {
		$renommages = array(); # un renommage a t'il eu lieu ?
		foreach (_request('lists') as $k => $v) {

			// l'ancien nom d'identifiant. Ne pas normaliser s'il est vide !
			// sinon cela lui met 'newsletter::newsletter' d'office.
			if ($v['id_bak']) {
				$id_bak = mailsubscribers_normaliser_nom_liste($v['id_bak']);
			} else {
				$id_bak = '';
			}
			unset($v['id_bak']);

			// cas d'une suppression de liste d'information
			if ($v['status'] == 'delete') {
				mailsubscribers_supprimer_identifiant_liste($id_bak);
				unset($lists[$k]);
				continue;
			}

			// autres cas (nouvelle ou modification)
			if (strlen(trim($v['id']))) {
				$lists[$k]['id'] = mailsubscribers_normaliser_nom_liste($v['id']);
				if (!in_array($v['status'], array('open', 'close'))) {
					$lists[$k]['status'] = 'open';
				}

				// renommage d'une liste (il existe un ancien identifiant non vide)
				if ($id_bak AND ($lists[$k]['id'] != $id_bak)) {
					mailsubscribers_renommer_identifiant_liste($id_bak, $lists[$k]['id']);
					$renommages[$k] = $lists[$k]['id'];
				}
			}
			else {
				unset($lists[$k]);
			}
		}

		// si une liste est renommée (identifiant) du nom d'une autre existante,
		// on supprime la liste renommée.
		if ($renommages) {
			foreach ($renommages as $c => $id) {
				foreach ($lists as $k => $v) {
					if (($c != $k) AND ($v['id'] == $id)) {
						unset($lists[$c]);
						break;
					}
				}
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
