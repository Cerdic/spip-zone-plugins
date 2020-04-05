<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2018                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

// https://code.spip.net/@action_purger_dist
function action_purger_dist($arg=null)
{
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
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
			// suppression du cache si la methode de memoization selectionnée implemente la fonction purge
			$m = &$GLOBALS['Memoization'];
			$m->purge();

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

}

