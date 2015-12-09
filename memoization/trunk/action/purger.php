<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

// http://doc.spip.org/@action_purger_dist
function action_purger_dist($arg=null)
{
	if (is_null($arg)) {
		if ($securiser_action = charger_fonction('securiser_action', 'inc', true))
			$arg = $securiser_action();
		else {
			/* compat SPIP 1.9 */
			$arg = _request('arg');
			$redirect = 'ecrire/'._request('redirect');
			include_spip('inc/meta');
			function spip_unlink($u) {
				return unlink($u);
			}
		}
	}

	include_spip('inc/invalideur');

	spip_log("purger $arg");

	switch ($arg) {
		case 'inhibe_cache':
			// inhiber le cache pendant 24h
			ecrire_meta('cache_inhib',$_SERVER['REQUEST_TIME']+24*3600);
			break;
		case 'reactive_cache':
			effacer_meta('cache_inhib');
			break;

		case 'cache':
			supprime_invalideurs();
			@spip_unlink(_CACHE_RUBRIQUES);
			@spip_unlink(_CACHE_PIPELINES);
			@spip_unlink(_CACHE_PLUGINS_PATH);
			@spip_unlink(_CACHE_PLUGINS_OPT);
			@spip_unlink(_CACHE_PLUGINS_FCT);
			@spip_unlink(_CACHE_PLUGINS_VERIF);
			@spip_unlink(_CACHE_CHEMIN);
			@spip_unlink(_DIR_TMP."plugin_xml_cache.gz");
			# purge a l'ancienne des fichiers de tmp/cache/
			purger_repertoire(_DIR_CACHE,array('subdir'=>true));
			purger_repertoire(_DIR_AIDE);
			purger_repertoire(_DIR_VAR.'cache-css');
			purger_repertoire(_DIR_VAR.'cache-js');

			# ajouter une mark pour les autres methodes de memoization
			ecrire_meta('cache_mark', time());
			/* compat SPIP 1.9 */
			if (function_exists('ecrire_metas')) ecrire_metas();

			break;

		case 'squelettes':
			purger_repertoire(_DIR_SKELS);
			break;

		case 'vignettes':
			purger_repertoire(_DIR_VAR,array('subdir'=>true));
			supprime_invalideurs();
			purger_repertoire(_DIR_CACHE);
			break;
	}

	// le faire savoir aux plugins
	pipeline('trig_purger',$arg);

	/* compat SPIP 1.9 */
	if (isset($redirect)) {
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}

?>
