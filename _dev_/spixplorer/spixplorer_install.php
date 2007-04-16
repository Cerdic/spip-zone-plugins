<?php
/*
 * Plugin spixplorer : ecrire/?exec=spixplorer installation d'un spip pre-configure
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */
// la fonction appelee par le core, une simple "factory" de la classe spixplorer
// Pour l'instant, juste un lancement de action charger decompresser
// Ca va finir par une serie et notamment le mes_fichiers.zip du plugin mes_fichiers
function spixplorer_install($action)
{
	$cible = dirname(__FILE__) . '/quixplorer';
//	spip_log('spixplorer: ' . $action . ' (' . $cible . ')');

	if ($action == 'test') {
		return is_dir($cible);
	}

	if ($action != 'install') {
		return;
	}

	echo 'install spixplorer<br />';
	spip_log(
	 'installer spixplorer depuis http://files.spip.org/externe/quixplorer.zip'
	);

   	include_spip('inc/chargeur');
	$statut = charge_charger_zip(
		'http://files.spip.org/externe/quixplorer_2_3_1',
		'',
		'quixplorer_2_3_1',
		$cible
	);
	echo $status . '<br />';

	if (!is_dir($cible)) {
		spip_log('spixplorer install: impossible de charger quixplorer');
		return 0;
	}

	return true;
}
