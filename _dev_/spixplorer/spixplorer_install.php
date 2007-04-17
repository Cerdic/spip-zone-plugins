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
			'index.php' => 'quixplorer_index.php',
			'_lib/lib_zip.php' => 'include/lib_zip.php',
			'_lib' => 'action',
//			'action/init.php' => 'inc/init.php'
			'include/fun_admin.php' => 'action/spx_admin.php',
			'include/fun_archive.php' => 'action/spx_archive.php',
			'include/fun_chmod.php' => 'action/spx_chmod.php',
			'include/fun_copy_move.php' => 'action/spx_copy_move.php',
			'include/fun_del.php' => 'action/spx_del.php',
			'include/fun_down.php' => 'action/spx_down.php',
			'include/fun_edit.php' => 'action/spx_edit.php',
			'include/fun_extra.php' => 'include/extra.php',
			'include/fun_list.php' => 'action/spx_list.php',
			'include/fun_mkitem.php' => 'action/spx_mkitem.php',
			'include/fun_search.php' => 'action/spx_search.php',
			'include/fun_up.php' => 'action/spx_up.php',
			'include/fun_users.php' => 'action/spx_users.php',
			'include' => 'inc',
			'_lang' => 'lang'
		),
		'edit' => array(
			'#(?:require|include)[ (]+"\./\.include/fun_extra\.php"\)?#' => 'include_spip("quixplorer/inc/extra")',
			'#(?:require|include)[ (]+"\./\.include/fun_(.*)\.php"\)?#' => 'include_spip("action/spx_$1")',
			'#(?:require|include)[ (]+"\./\.include/(.*)\.php"\)?#' => 'include_spip("quixplorer/inc/$1")',
			'#(?:require|include)[ (]+"\./_lib/(.*)\.php"\)?#' => 'include_spip("quixplorer/inc/$1")',
			'#(?:require|include)[ (]+"\./_lang/(.*)\.php"\)?#' => 'include_spip("quixplorer/lang/$1")',
			'#(?:require|include)[ (]+"\./\.config/(.*)\.php"\)?#' => 'include_spip("quixplorer/config/$1")',
			"#isset\(\\\$GLOBALS\['__POST'\]\[([^\]]+)\]\)(.+?)\\\$GLOBALS\['__POST'\]\[\\1\]#"
				=> '_request($1)',
			'#\$GLOBALS#' => "\$GLOBALS['spx']",
			'#_img/#' => "plugins/spixplorer/quixplorer/_img/",
			'#.*have fun.*#i' => '$0

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007
',
//			'#\./\.#' => $cible . '/',
//			'#\./#' => $cible . '/'
		)
	));
	echo $statut . '<br />';

	if (!is_dir($cible)) {
		spip_log('spixplorer install: impossible de charger quixplorer');
		return 0;
	}

	return true;
}
