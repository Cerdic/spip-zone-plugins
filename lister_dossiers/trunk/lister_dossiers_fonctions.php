<?php
/**
 * Fonctions utiles au plugin Lister les dossiers
 *
 * @plugin     Lister les dossiers
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Lister_dossiers\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function lister_dossiers($racine = _DIR_RACINE)
{
	$repertoires = array();
	$repertoires_scannes = scandir($racine, 0);
	foreach ($repertoires_scannes as $key => $value) {
		if (is_dir($racine . DIRECTORY_SEPARATOR . $value) and !preg_match("/^\./", $value)) {
			$repertoires[$value] = lister_dossiers($racine . DIRECTORY_SEPARATOR . $value);
		}
	}
	return $repertoires;
}

function dossiers_taille ($racine = _DIR_RACINE)
{
	include_spip('inc/filtres');
	$spip_version = floatval(spip_version());
	if ($spip_version == 2.1) {
		include_spip('exec/admin_vider');
	} elseif ($spip_version == 3.0) {
		include_spip('action/calculer_taille_cache');
	}
	return calculer_taille_dossier($racine);
}
?>