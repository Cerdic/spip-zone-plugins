<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function mutualiser_upgrade() {
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

	if ($GLOBALS['spip_version_base']
	== str_replace(',','.',$GLOBALS['meta']['version_installee'])) {
		include_spip('inc/headers');
		redirige_par_entete($GLOBALS['meta']['adresse_site'].'/'._DIR_RESTREINT_ABS);
		exit;
	}

	// faire l'upgrade
	$old = $GLOBALS['meta']['version_installee'];
	$base = charger_fonction('upgrade', 'base');
	$base('upgrade',false);
	lire_metas();
	$new = $GLOBALS['meta']['version_installee'];
	if ($old == $new
	OR $new != $GLOBALS['spip_version_base']) {
		echo minipres(_T('titre_page_upgrade'),
			_L('Erreur de mise &#224; jour de @old@ vers @new@',
				array('old' => $old, 'new' => $new))
		);
	} else {
		echo minipres(_T('titre_page_upgrade'),
			_L('La base de donn&#233;es a &#233;t&#233; mise &#224; jour de @old@ vers @new@',
				array('old' => $old, 'new' => $new))
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
	}
	exit;
}

?>
