<?php
/*
 * Plugin charge : inc/chargeur.php: charger comme spip_loader en interne
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 * Charger un zip et le decompresser
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function charge_charger_zip($depot, $nom, $remove = 'spip', $dest = _DIR_RACINE, $plugin = null)
{
   	include_spip('inc/distant');
	$contenu = recuperer_page($paquet = $depot . $nom . '.zip');

	if (!$contenu || !($fichier = charge_ecrire_fichier_zip($nom, $contenu))) {
		spip_log('charger_decompresser impossible de charger ' . $paquet);
		return 0;
	}

   	include_spip('inc/pclzip');
	$zip = new PclZip($fichier);
	$list = $zip->listContent();
	$i = count($list) - 1;
	$path = explode('/', $list[$i]['filename']);
	while ($i--) {
		$act = explode('/', $list[$i]['filename']);
		for ($j = 0; $j < count($path); ++$j) {
			if ($j >= count($path) || $act[$j] != $path[$j]) {
				break;
			}
		}
		for ( ; $j < count($path); ++$j) {
			unset($path[$j]);
		}
	}
	$aremove = explode('/', $remove);
	$jmax = count($path);
	for ($j = 0; $j < $jmax; ++$j) {
		if ($j >= count($aremove) || $path[$j] != $aremove[$j]) {
			break;
		}
		unset($path[$j]);
	}
	$adest = explode('/', $dest);
	$kmax = count($adest);
	for ($k = 0 ; $k < $kmax && $j < $jmax; ++$j, ++$k) {
		if ($path[$j] != $adest[$k]) {
			break;
		}
		unset($adest[$k]);
	}
	$dest = implode('/', $adest);

	$ok = $zip->extract(
		PCLZIP_OPT_PATH, $dest,
		PCLZIP_OPT_SET_CHMOD, _SPIP_CHMOD,
		PCLZIP_OPT_REPLACE_NEWER,
		PCLZIP_OPT_REMOVE_PATH, $remove . "/");
	if ($zip->error_code < 0) {
		spip_log('charger_decompresser erreur zip ' . $zip->error_code .
					' pour paquet: ' . $paquet);
		return $zip->error_code;
	}

	@unlink($fichier);

	if ($plugin) {
		charge_activer_plugin($plugin);
	}

	spip_log('charger_decompresser OK pour paquet: ' . $paquet);

	return 1;
}

// juste activer le plugin du repertoire $plugin
function charge_activer_plugin($plugin)
{
	spip_log('charger_decompresser activer plugin: ' . $plugin);
	include_spip('inc/plugin');
	ecrire_plugin_actifs(array($plugin), false, 'ajoute');
	ecrire_metas();
}

//
// Ecrire un fichier de maniere un peu sure
//
function charge_ecrire_fichier_zip ($nom, $contenu) {

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
