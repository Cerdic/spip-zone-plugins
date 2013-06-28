<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function mutualiser_upgradeplugins() {
	define('_DOCTYPE_ECRIRE', ''); # on n'a pas lance spip_initialisation_suite() donc cette constante n'est pas definie
	include_spip('inc/minipres');

	// verif securite
	if (_request('secret')
	!= md5(
	$GLOBALS['meta']['version_installee'].'-'.$GLOBALS['meta']['popularite_total']
	)) {
		include_spip('inc/headers');
		redirige_par_entete($GLOBALS['meta']['adresse_site'].'/'._DIR_RESTREINT_ABS);
		exit;
	}

	// faire l'upgrade
	lire_metas();
	echo minipres(_T('titre_page_upgrade'),
		_L('Mise &agrave; jour des plugins')
	);
	// Installer les plugins
	include_spip('inc/plugin');
	actualise_plugins_actifs();
	effacer_meta('plugin_erreur_activation');
	ecrire_metas();
	installe_plugins();
	// vider tmp et recreer tmp/meta_cache.php
	include_spip('inc/invalideur');
	purger_repertoire(_DIR_TMP);
	ecrire_metas();
	echo minipres(_T('titre_page_upgrade'),
		_L('Aller dans <a href="@ecrire@">ecrire/</a>',
			array('ecrire' => $GLOBALS['meta']['adresse_site'].'/'._DIR_RESTREINT_ABS))
	);
	exit;
}

?>
