<?php
/**
 * Plugin coloration code
 * Fonctions spécifiques au plugin
 *
 * @package SPIP\Coloration_code\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * Ajout d'une feuille de style CSS dans l'espace privé pour l'affichage des codes et cadres
 *
 * @param string $flux
 * 		Le contenu de la partie css du head
 * @return string $flux
 * 		Le contenu de la partie css du head modifiée
 */
function coloration_code_header_prive_css($flux) {
	$css2 = find_in_path('prive/themes/spip/coloration_code.css');
	$flux .= "\n<link rel='stylesheet' type='text/css' href='$css2' id='csscoloration_code'>\n";
	return $flux;
}

/**
 * Inserer des styles
 *
 * @param string $flux
 * @return string
 */
function coloration_code_insert_head_css($flux) {
	if ($f = find_in_path('css/coloration_code.css') and !PLUGIN_COLORATION_CODE_SANS_STYLES) {
		$flux .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="all" />';
	}
	return $flux;
}
