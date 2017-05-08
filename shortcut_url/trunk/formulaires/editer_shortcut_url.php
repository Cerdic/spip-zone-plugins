<?php
/**
 * shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @license    GNU/GPL
 * @package    SPIP\formulaires\shortcut_url
 */

/**
 * Gestion du formulaire de shortcut_url des sites
 *
 * @package SPIP\Formulaires
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('action/editer_objet');

/**
 * Chargement du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_editer_shortcut_url_charger_dist($id_shortcut_url = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {
	$valeurs = formulaires_editer_objet_charger('shortcut_url', $id_shortcut_url, '', '', $retour, '');

	if (defined('_TAILLE_RACCOURCI')) {
		if (_TAILLE_RACCOURCI >= 5) {
			$valeurs['taille_raccourci'] = _TAILLE_RACCOURCI;
		} else {
			$valeurs['taille_raccourci'] = 8;
		}
	} else {
		$valeurs['taille_raccourci'] = 8;
	}
	return $valeurs;
}

function formulaires_editer_shortcut_url_verifier_dist($id_shortcut_url = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {
	$erreurs = formulaires_editer_objet_verifier('shortcut_url', $id_shortcut_url, array('url'));

	if (!$url = _request('url')) {
		$erreurs['url'] = _T('info_obligatoire');
	} else {
		// Check si l'url est valide
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
			$erreurs['url'] = _T('shortcut_url:erreur_url_invalide');
		} else {
			// On supprime ?var_mode=recalcul et autres var_mode (cf traiter aussi)
			$url = parametre_url($url, 'var_mode', '');
			// Check si l'URL existe deja
			if ($id_shortcut_url_existe = sql_getfetsel('id_shortcut_url', 'spip_shortcut_urls', 'url=' . sql_quote($url) . ' AND id_shortcut_url != '.intval($id_shortcut_url))) {
				set_request('id_shortcut_url_existe', $id_shortcut_url_existe);
				$erreurs['url'] = _T('shortcut_url:erreur_url_exist');
			}
		}
	}
	// On vérifie que l'URL raccourcie n'existe pas
	if (_request('titre')) {
		$id_shortcut_url_existe = sql_getfetsel('id_shortcut_url', 'spip_shortcut_urls', 'titre=' . sql_quote(_request('titre').' AND id_shortcut_url != '.intval($id_shortcut_url)));
		if ($id_shortcut_url_existe) {
			set_request('id_shortcut_url_existe', $id_shortcut_url_existe);
			$erreurs['titre'] = _T('shortcut_url:erreur_url_raccourcis_exist');
		}
	}

	return $erreurs;
}

// https://code.spip.net/@inc_editer_shortcut_url_dist
function formulaires_editer_shortcut_url_traiter_dist($id_shortcut_url = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {
	include_spip('inc/distant');
	$result = $set = array();

	if (intval($id_shortcut_url) > 0) {
		sql_delete('spip_urls', 'type=' . sql_quote('shortcut_url') . ' AND id_objet=' . intval($id_shortcut_url));
	}

	$editer_objet = charger_fonction('editer_objet', 'action');
	$editer_objet($id_shortcut_url, 'shortcut_url', $set);

	$res = array(
			'editable' => true,
			'message_ok' => _T('shortcut_url:message_confirmation_shortcut_url')
	);
	if (_request('exec') == 'accueil') {
		set_request('id_shortcut_url', false);
		set_request('url', false);
		set_request('titre', false);
	}
	$res['message_ok'] .= "<script type='text/javascript'>if (window.jQuery) $('.liste-objets.shortcut_url, #navigation .box, #shortcut_supplement').ajaxReload();</script>";
	return $res;
}
