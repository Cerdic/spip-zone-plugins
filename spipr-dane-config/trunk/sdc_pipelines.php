<?php
/**
 * Utilisations de pipelines par Spipr-Dane Config
 *
 * @plugin     Spipr-Dane Config
 * @copyright  2019
 * @author     Webmestre DANE
 * @licence    GNU/GPL
 * @package    SPIP\Sdc\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline header_prive
 * 
 * Insertion de la feuille de style de l'espace privé
 * 
 * @param string $flux
 * 		Le code html du head de l'espace privé
 * @return string $flux
 * 		Le code html du head complété
 */
function sdc_header_prive($flux) {
	$css = find_in_path('prive/themes/spip/css/prive_sdc.css');
    $flux .= "\n<link rel='stylesheet' name='$f' href='$css' type='text/css' />\n";
	return $flux;
}
