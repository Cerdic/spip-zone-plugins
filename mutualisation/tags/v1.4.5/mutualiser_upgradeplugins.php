<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function mutualiser_upgradeplugins() {
	define('_DOCTYPE_ECRIRE', ''); # on n'a pas lance spip_initialisation_suite() donc cette constante n'est pas definie
	include_spip('inc/minipres');

	// verif securite
	if (_request('secret') != md5($GLOBALS['meta']['version_installee'] . '-' . $GLOBALS['meta']['secret_du_site'])) {
		include_spip('inc/headers');
		redirige_par_entete($GLOBALS['meta']['adresse_site'] . '/' . _DIR_RESTREINT_ABS . '?exec=admin_plugin');
		exit;
	}

	// faire l'upgrade
	lire_metas();
	// Installer les plugins
	include_spip('inc/plugin');

	// si jamais la liste des plugins actifs change, il faut faire un refresh du hit
	// pour etre sur que les bons fichiers seront charges lors de l'install
	$new = actualise_plugins_actifs();
	if ($new and _request('actualise') < 2) {
		include_spip('inc/headers');
		redirige_par_entete(parametre_url(self(), 'actualise', _request('actualise')+1, '&'));
	} else {
		//effacer_meta('plugin_erreur_activation');
		//ecrire_metas();
		plugin_installes_meta();

		// vider tmp et recreer tmp/meta_cache.php
		include_spip('inc/invalideur');
		supprime_invalideurs();
		@spip_unlink(_CACHE_RUBRIQUES);
		@spip_unlink(_CACHE_CHEMIN);
		@spip_unlink(_DIR_TMP . "plugin_xml_cache.gz");
		@spip_unlink(_CACHE_PLUGINS_OPT);
		purger_repertoire(_DIR_CACHE, array('subdir' => true));
		purger_repertoire(_DIR_AIDE);
		purger_repertoire(_DIR_VAR . 'cache-css');
		purger_repertoire(_DIR_VAR . 'cache-js');
		purger_repertoire(_DIR_SKELS);

		if (_request('ajax') != 'oui') {
			echo minipres(_T('titre_page_upgrade'),
				_L('Mise &agrave; jour des plugins') . '<br/>' . _L('Aller dans <a href="@ecrire@">ecrire/</a>',
					array('ecrire' => $GLOBALS['meta']['adresse_site'] . '/' . _DIR_RESTREINT_ABS . '?exec=admin_plugin')));
		} else {
			header('Access-Control-Allow-Origin: *');
			echo _request('up');
		}
		exit;
	}
}
