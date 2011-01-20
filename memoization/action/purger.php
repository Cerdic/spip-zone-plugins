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
function action_purger_dist()
{
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

	include_spip('inc/invalideur');

	spip_log("purger $arg");

	switch ($arg) {

	case 'cache': 
		supprime_invalideurs();
		@spip_unlink(_CACHE_RUBRIQUES);
		@spip_unlink(_CACHE_PIPELINES);
		@spip_unlink(_CACHE_PLUGINS_PATH);
		@spip_unlink(_CACHE_PLUGINS_OPT);
		@spip_unlink(_CACHE_PLUGINS_FCT);
		@spip_unlink(_CACHE_PLUGINS_VERIF);
		@spip_unlink(_CACHE_CHEMIN);
		purger_repertoire(_DIR_AIDE);
		purger_repertoire(_DIR_VAR.'cache-css');
		purger_repertoire(_DIR_VAR.'cache-js');

		# purge a l'ancienne des fichiers de tmp/cache/
		purger_repertoire(_DIR_CACHE,array('subdir'=>true));

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


	/* compat SPIP 1.9 */
	if (isset($redirect)) {
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}

?>
