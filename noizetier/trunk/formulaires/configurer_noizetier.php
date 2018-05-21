<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_configurer_noizetier_charger_dist() {

	// On récupère les valeurs configurées
	include_spip('inc/cvt_configurer');
	$valeurs = cvtconf_formulaires_configurer_recense('configurer_noizetier');

	// Injecter les objets exclus
	include_spip('inc/noizetier_objet');
	$valeurs['_objets_exclus'] = lister_objets_exclus();

	$valeurs['editable'] = true;

	return $valeurs;
}


function formulaires_configurer_noizetier_traiter_dist() {
	$retour = array();

	// Si on a changé la configuration de l'ajax par défaut, on supprime le cache ajax des
	// noisettes pour forcer son recalcul à la prochaine utilisation.
	include_spip('inc/config');
	$defaut_ajax = lire_config('noizetier/ajax_noisette');

	if ($defaut_ajax != _request('ajax_noisette')) {
		include_spip('inc/ncore_cache');
		cache_supprimer('noizetier', _NCORE_NOMCACHE_TYPE_NOISETTE_AJAX);
	}

	// On filtre le tableau des objets configurables pour éviter l'index vide fourni systématiquement par la saisie.
	$objets_configurables = _request('objets_noisettes');
	$objets_configurables = is_array($objets_configurables) ? array_filter($objets_configurables) : array();
	set_request('objets_noisettes', $objets_configurables);

	// On enregistre les nouvelles valeurs saisies
	include_spip('inc/cvt_configurer');
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_noizetier', array());
	$retour['message_ok'] = _T('config_info_enregistree') . $trace;
	$retour['editable'] = true;

	return $retour;
}


/**
 * Renvoie la liste des types d'objet ne pouvant pas être personnalisés car ne possédant pas
 * de page détectable par le noiZetier.
 *
 * @api
 *
 * @return array|null
 */
function lister_objets_exclus() {

	static $exclusions = null;

	if (is_null($exclusions)) {
		$exclusions = array();
		include_spip('base/objets');

		// On récupère les tables d'objets sous la forme spip_xxxx.
		$tables = lister_tables_objets_sql();
		$tables = array_keys($tables);

		// On récupère la liste des pages disponibles et on transforme le type d'objet en table SQL.
		$where = array('composition=' . sql_quote(''), 'est_page_objet=' . sql_quote('oui'));
		$pages = sql_allfetsel('type', 'spip_noizetier_pages', $where);
		$pages = array_map('reset', $pages);
		$pages = array_map('table_objet_sql', $pages);

		// On exclut donc les tables qui ne sont pas dans la liste issues des pages.
		$exclusions = array_diff($tables, $pages);
	}

	return $exclusions;
}
