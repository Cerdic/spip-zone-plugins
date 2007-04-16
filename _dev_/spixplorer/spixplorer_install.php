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
	spip_log('spixplorer: ' . $action);
	if ($action == 'test') {
		return is_dir($cible = dirname(__FILE__) . '/quixplorer');
	}

	if ($action != 'install') {
		return;
	}
echo 'install<br />';
return;
	spip_log(
	 'installer spixplorer depuis http://files.spip.org/externe/quixplorer.zip'
	);
   	include_spip('inc/chargeur');
	$statut = charge_charger_zip(
		'http://files.spip.org/externe/',
		'quixplorer_2_3_1',
		'quixplorer_2_3_1',
		$cible;
	echo $status . '<br />';

/* Une tentative pour récupérer en 2 temps de chez sf quixplorer_2_3_1

	// chargement decompression de http://prdownloads.sourceforge.net/quixplorer/quixplorer_2_3_1.zip?download
	require_once dirname(__FILE__) . '/inc/loader.php';
	$status = charge_charger_zip(
	 'http://prdownloads.sourceforge.net/quixplorer/quixplorer_2_3_1.zip?download',
	  'mes_fichiers');
	if ($status <= 0) {
		spip_log('spixplorer erreur ' . $status . ' pour paquet mes_fichiers.zip');
	}
	if (!preg_match('<a href="([^"]+\.zip)">', $contenu, $match) {
		spip_log('Pas de zip pour quixplorer');
		return 0;
	}
	$contenu = recuperer_page($match[1]);
*/

	if (!is_dir($cible) {
		spip_log('spixplorer install: impossible de charger quixplorer');
		return 0;
	}
//echo $contenu;

/*
   	include_spip('inc/pclzip');
	$zip = new PclZip(_DIR_TMP . 'quixplorer.zip');

	// restitution du dump si present
	if (!$dump = preg_files(_DIR_DUMP . '.*\.xml\.gz$')) {
		spip_log('Installation terminee, pas de dump de la BD disponible');
		return true;
	}

	// declenchement de import_all comme si on y etait
	ob_start();
	include_spip('exec/import_all');
	$dump = substr(strrchr($dump[0], '/'), 1);
	$GLOBALS['connect_toutes_rubriques'] = true;
	$GLOBALS['connect_id_auteur'] = 1;
	$_REQUEST['exec'] = 'import_all';
	$_REQUEST['archive'] = $dump;
	import_all_debut($_REQUEST);
	$import_all = charger_fonction('import_all');
	$import_all();
	ob_end_clean();

	// installation des plugins issus du dump trouves dans spip_meta
	lire_metas();
	include_spip('inc/plugin');
	$plugins = liste_plugin_actifs();
	unset($plugins['spixplorer']);
	foreach ($plugins as $key=>$plug){
		// ca ne marche que si le paquet s'appelle pareil que le repertoire du plugin
		$status = kit_charger_zip($url_serveur . 'DISTRIB/', $plug['dir'],
						'spip/plugins', dirname(dirname(__FILE__)));
		if ($status <= 0) {
			spip_log('spixplorer erreur ' . $status . ' pour plugin ' . $plug['dir']);
		} else {
			$_POST['s' . substr(md5('statusplug_' . $plug['dir']),0,16)] = 'O';
		}
	}

	$_POST['s' . substr(md5("statusplug_spixplorer"),0,16)] = 'O';
	include_spip('action/activer_plugins');
	enregistre_modif_plugin();
	verif_plugin();
	installe_plugins();
	
	spip_log('Installation terminee');
	redirige_par_entete("../");
*/	
	return true;
}

// Nettoyer tous les fichiers kit_loader... de la racine
// Mais auparavant, recuperer l'url du serveur originel de kit_loader
function spx_nettoyer_racine() {
	$url_serveur = false;
	($lines = @file(_DIR_RACINE . 'kit_loader.php')) || ($lines = array());
	foreach ($lines as $line ) {
		if (($pos = strpos($line, "define('_SERVEUR_URL', '")) !== false) {
			$url_serveur = substr($line, $pos + 24, -4);
			break;
		}
	}

	$d = opendir(_DIR_RACINE);
	while (false !== ($f = readdir($d))) {
		if (preg_match('/^kit_loader.*\.(php|js|css)$/', $f)) {
			unlink(_DIR_RACINE . $f);
		}
	}
	closedir($d);
	return $url_serveur;
}

//
// Ecrire un fichier de maniere un peu sure
//
function spx_ecrire_fichier_zip ($nom, $contenu) {

	$fp = @fopen($fichier = _DIR_TMP . $nom . '.zip', 'wb');
	$s = @fputs($fp, $contenu, $a = strlen($contenu));

	$ok = ($s == $a);

	@fclose($fp);

	if (!$ok) {
		@unlink($fichier);
		return false;
	}

	return $fichier;
}

?>
