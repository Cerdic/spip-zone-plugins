<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head_prive (SPIP)
 * Ajoute des barres de porte-plume sur les champs configurés
 * 
 * @param string $flux : le contexte du pipeline
 * @return string $flux : le contexte modifié
 */
function ppp_insert_head_prive($flux){
	$js = generer_url_public('barre_generalisee.js');
	if (defined('_VAR_MODE') && _VAR_MODE=='recalcul')
		$js = parametre_url($js, 'var_mode', 'recalcul');
	$flux .= "<script type='text/javascript' src='$js'></script>\n";
	return $flux;
}

?>