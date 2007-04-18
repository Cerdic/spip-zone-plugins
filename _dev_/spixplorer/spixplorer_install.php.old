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
	$cible = dirname(__FILE__);
//	spip_log('spixplorer: ' . $action . ' (' . $cible . ')');

	if ($action == 'test') {
		return is_readable($cible . '/quixplorer_index.php');
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
			'include' => 'inc',
			'_lib/lib_zip.php' => 'inc/spx_lib_zip.php',
			'inc/fun_extra.php' => 'inc/spx_extra.php',
			'inc/error.php' => 'inc/spx_error.php',
			'inc/footer.php' => 'inc/spx_footer.php',
			'inc/header.php' => 'inc/spx_header.php',
			'inc/init.php' => 'inc/spx_init.php',
			'inc/javascript.php' => 'inc/spx_javascript.php',
			'inc/js_admin2.php' => 'inc/spx_js_admin2.php',
			'inc/js_admin3.php' => 'inc/spx_js_admin3.php',
			'inc/js_admin.php' => 'inc/spx_js_admin.php',
			'inc/login.php' => 'inc/spx_login.php',

			'_lib' => 'action',
			'inc/fun_admin.php' => 'action/spx_admin.php',
			'inc/fun_archive.php' => 'action/spx_archive.php',
			'inc/fun_chmod.php' => 'action/spx_chmod.php',
			'inc/fun_copy_move.php' => 'action/spx_copy_move.php',
			'inc/fun_del.php' => 'action/spx_del.php',
			'inc/fun_down.php' => 'action/spx_down.php',
			'inc/fun_edit.php' => 'action/spx_edit.php',
			'inc/fun_list.php' => 'action/spx_list.php',
			'inc/fun_mkitem.php' => 'action/spx_mkitem.php',
			'inc/fun_search.php' => 'action/spx_search.php',
			'inc/fun_up.php' => 'action/spx_up.php',
			'inc/fun_users.php' => 'action/spx_users.php',
			'_lang' => 'spx_lang',

			'config/conf.php' => 'config/spx_conf.php',
//			'config/.htaccess' => 'config/spx_htaccess',
			'config/.htusers.php' => 'config/spx_htusers.php',
			'config/mimes.php' => 'config/spx_mimes.php'
		),
		'edit' => array(
			'#(?:require|include)[ (]+"\./\.include/fun_extra\.php"\)?#' => 'include_spip("inc/spx_extra")',
			'#(?:require|include)[ (]+"\./\.include/fun_(.*)\.php"\)?#' => 'include_spip("action/spx_$1")',
			'#(?:require|include)[ (]+"\./\.include/(.*)\.php"\)?#' => 'include_spip("inc/spx_$1")',
			'#(?:require|include)[ (]+"\./_lib/(.*)\.php"\)?#' => 'include_spip("inc/spx_$1")',
			'#(?:require|include)[ (]+"\./_lang/(.*)\.php"\)?#' => 'include_spip("spx_lang/$1")',
			'#(?:require|include)[ (]+"\./\.config/(.*)\.php"\)?#' => 'include_spip("config/spx_$1")',
			"#isset\(\\\$GLOBALS\['__POST'\]\[([^\]]+)\]\)((.+?=\s*)|[^\$]+?)\\\$GLOBALS\['__POST'\]\[\\1\]#"
				=> '$3_request($1)',
			'#\$GLOBALS\["(messages|error_msg)"\]\["([^"]+)"\]#' => '_T(\'spixplorer:$2\')',
			'#\$GLOBALS\["date_fmt"\]#' =>  '_T(\'spixplorer:date_fmt\')',
			'#\$GLOBALS\["text_dir"\]#' => '$GLOBALS[\'spip_lang_dir\']',

			'#\$GLOBALS#' => "\$GLOBALS['spx']",
			'#_img/#' => "plugins/spixplorer/_img/",
			'#fopen\("\./\.config/\.htusers\.php"#'
				=> 'fopen("plugins/spixplorer/config/spx_htusers.php"',
			'#_style/#' => 'plugins/spixplorer/_style/',
			'#.*have fun.*#i' => '$0

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007
',
//			'#\./\.#' => $cible . '/',
//			'#\./#' => $cible . '/'
		)
	));
	echo $statut . '<br />';

	if (!is_readable($cible . '/quixplorer_index.php')) {
		spip_log('spixplorer install: impossible de charger quixplorer');
		return 0;
	}

	return true;
}
