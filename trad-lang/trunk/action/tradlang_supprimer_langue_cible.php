<?php
/**
 *
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 *
 * Action permettant de supprimer une langue cible si vide
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_tradlang_supprimer_langue_cible_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(\w+)\/(\w+)$,', $arg, $r)) {
		spip_log("action_tradlang_creer_langue_cible $arg pas compris", 'tradlang');
		return false;
	}
	$id_tradlang_module = intval($r[1]);
	$lang_cible = $r[2];

	include_spip('inc/autoriser');

	if ($lang_cible
		and intval($id_tradlang_module)
		and autoriser('modifier', 'tradlang')
		and !sql_countsel('spip_tradlangs', 'id_tradlang_module = ' . intval($id_tradlang_module) . ' AND lang = ' . sql_quote($lang_cible) . " AND statut='OK'")) {
		if (!_request('confirm')) {
			$row_module = sql_fetsel('*', 'spip_tradlang_modules', 'id_tradlang_module = ' . intval($id_tradlang_module));
			$titre = $row_module['nom_mod'] .': ' . _T('tradlang:confirm_suppression_langue_cible', array('lang' => $lang_cible));
			$btn_label = _T('tradlang:bouton_supprimer_langue_module');
			$redirect = _request('redirect');
			$url_action = generer_action_auteur('tradlang_supprimer_langue_cible', "$id_tradlang_module/$lang_cible", $redirect);
			$url_action = parametre_url($url_action, 'confirm', 1, '&');

			// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
			include_spip('inc/minipres');
			echo minipres($titre, "<style>h1{font-weight: normal}#minipres{text-align: center}</style>". bouton_action($btn_label,$url_action), '', true);
			exit;

		}
		else {
			/**
			 * Suppression des versions et urls
			 */
			$tradlangs = sql_allfetsel('id_tradlang', 'spip_tradlangs', 'id_tradlang_module = ' . intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible));
			$tradlangs_supprimer = array();
			foreach ($tradlangs as $tradlang) {
				$tradlangs_supprimer[] = $tradlang['id_tradlang'];
			}
			if (count($tradlangs_supprimer)) {
				sql_delete('spip_versions', 'objet="tradlang" AND ' . sql_in('id_objet', $tradlangs_supprimer));
				sql_delete('spip_versions_fragments', 'objet="tradlang" AND ' . sql_in('id_objet', $tradlangs_supprimer));
				sql_delete('spip_urls', 'type="tradlang" AND ' . sql_in('id_objet', $tradlangs_supprimer));
			}
			/**
			 * Suppression des chaînes de langue
			 */
			sql_delete('spip_tradlangs', 'id_tradlang_module = ' . intval($id_tradlang_module) . ' AND lang = ' . sql_quote($lang_cible));
			/**
			 * Suppression des bilans de cette langue
			 */
			sql_delete('spip_tradlangs_bilans', 'id_tradlang_module = ' . intval($id_tradlang_module) . ' AND lang = ' . sql_quote($lang_cible));
			include_spip('inc/invalideur');
			suivre_invalideur('1');
			if ($redirect = _request('redirect')){
				$redirect = parametre_url($redirect, 'lang_cible', '', '&');
				$redirect = parametre_url($redirect, 'var_lang_suppr', $lang_cible, '&');
				$GLOBALS['redirect'] = $redirect;
			}
		}
	} else {
		spip_log("action_tradlang_supprimer_langue_cible_dist : Module $id_tradlang_module est traduit en $lang_cible", 'tradlang');
	}

}
