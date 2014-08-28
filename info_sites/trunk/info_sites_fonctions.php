<?php
/**
 * Définit les fonctions du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}


include_spip('inc/filtres_ecrire');

function version2branche($version)
{
	if (preg_match("/\./", $version)) {
		$numeros = explode(".", $version);
		if (count($numeros) >= 3) {
			$version = $numeros[0] . "." . $numeros[1];
		} elseif (count($numeros) <= 2) {
			$version = $numeros[0];
		}
	}

	return $version;
}
?>