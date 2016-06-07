<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement des valeurs par defaut des champs du formulaire
 */
function formulaires_inscription3_recherche_charger_dist() {
	$datas = array();
	$datas['ordre'] = _request('ordre');
	$datas['desc'] = _request('desc');
	$datas['case'] = _request('case');
	$datas['valeur'] = _request('valeur');

	$datas['exceptions'] = pipeline('i3_exceptions_des_champs_auteurs_elargis', array());

	if (_request('afficher_tous')) {
		set_request('valeur', '');
		set_request('case', '');
	}
	return $datas;
}

/**
 * Vérification du formulaire
 * @return
 */
function formulaires_inscription3_recherche_verifier_dist() {
	$erreurs = array();
	if (_request('supprimer_auteurs')) {
		$auteurs_checked = _request('check_aut');
		if (is_array($auteurs_checked)) {
			include_spip('inc/autoriser');
			foreach ($auteurs_checked as $val) {
				$statut = sql_fetsel('nom,statut', 'spip_auteurs', 'id_auteur='.intval($val));
				if (!autoriser('modifier', 'auteur', $val) or ($statut['statut'] == '0minirezo')) {
					$erreurs['check_aut'.$val] = array('nom' => $statut['nom'], 'statut' => $statut['statut']);
				}
			}
			if (count($erreurs)>0) {
				foreach ($erreurs as $infos) {
					$infos_erreurs = '<p>'._T('inscription3:erreur_info_statut', $infos).'</p>';
				}
				$erreurs['message_erreur'] = '<p>'._T('inscription3:erreur_suppression_comptes_impossible').'</p>';
				$erreurs['message_erreur'] .= $infos_erreurs;
			}
		} else {
			$erreurs['message_erreur'] = _T('inscription3:no_user_selected');
		}
	}

	return $erreurs;
}

/**
 * Traitement du formulaire
 * @return
 */
function formulaires_inscription3_recherche_traiter_dist() {

	$retour = array();
	if (_request('supprimer_auteurs')) {
		$auteurs_checked = _request('check_aut');
		$nb_auteurs = 0;
		if (is_array($auteurs_checked)) {
			foreach ($auteurs_checked as $val) {
				$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.intval($val));
				if ($statut != '0minirezo') {
					sql_updateq('spip_auteurs', array('statut' => '5poubelle'), 'id_auteur='.intval($val));
					$nb_auteurs++;
				}
			}
		} else {
			// Rien à faire
		}
		if ($nb_auteurs > 1) {
			$retour['message_ok'] = _T('inscription3:message_users_supprimes_nb', array('nb' => $nb_auteurs));
		} else {
			$retour['message_ok'] = _T('inscription3:message_users_supprimes_un');
		}
	}
	return $retour;
}
