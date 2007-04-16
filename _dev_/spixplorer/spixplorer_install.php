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

/* manque un chouille
   	include_spip('inc/distant');
	$contenu = recuperer_page(
	 'http://prdownloads.sourceforge.net/quixplorer/quixplorer_2_3_1.zip?download=1');

	if (!preg_match('#<a href="([^"]+)\.zip">#', $contenu, $match)) {
		spip_log('Pas de zip pour quixplorer');
		return 0;
	}
	spip_log(
	 'installer spixplorer depuis ' . $match[1]
	);
*/

   	include_spip('inc/chargeur');
	$statut = chargeur_charger_zip(array(
// si ca marchait ce serait $match[1], ...
		'zip' => 'http://files.spip.org/externe/quixplorer_2_3_1.zip',
		'remove' => 'quixplorer_2_3_1',
		'dest' => $cible,
		'rename' => array(
			'index.php' => 'quixplorer_index.php'
		),
		'edit' => array(
			'#\./\.#' => $cible . '/',
			'#\./#' => $cible . '/'
		)
	));
	echo $statut . '<br />';

	if (!is_dir($cible)) {
		spip_log('spixplorer install: impossible de charger quixplorer');
		return 0;
	}

	return true;
}
