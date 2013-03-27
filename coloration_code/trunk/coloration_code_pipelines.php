<?php
/**
 * Plugin coloration code
 * Fonctions spécifiques au plugin
 * 
 * @package SPIP\Coloration_code\Pipelines
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * Ajout d'une feuille de style CSS dans l'espace privé pour l'affichage des codes et cadres
 * 
 * @param string $flux
 * 		Le contenu de la partie css du head
 * @return string $flux
 * 		Le contenu de la partie css du head modifiée
 */
function coloration_code_header_prive_css($flux){
	$css2=find_in_path('prive/themes/spip/coloration_code.css');
	$flux .= "\n<link rel='stylesheet' type='text/css' href='$css2' id='csscoloration_code'> \n";
	return $flux;
}
?>