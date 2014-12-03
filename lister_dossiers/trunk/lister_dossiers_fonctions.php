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
		if (!preg_match("/^\./", $value)) {
			if (is_dir($racine . DIRECTORY_SEPARATOR . $value)) {
				$repertoires[$value] = lister_dossiers($racine . DIRECTORY_SEPARATOR . $value);
			} else {
			}
		}
	}

	return $repertoires;
}
?>