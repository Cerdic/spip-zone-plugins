<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 * 
 */

 /**
 * une fonction qui regarde si $texte est une chaine de langue
 * de la forme <:qqch:>
 * si oui applique _T()
 * si non applique typo()
 */
function _T_ou_typo($texte, $args=array()) {
	
	if (preg_match('/^\<:(.*?):\>$/',$texte,$match)) 
		$text = _T($match[1],$args);
	else {
		include_spip('inc/texte');
		$text = typo($texte);
	}

	return $text;

}
if (defined('_BONUX_STYLE'))
	_chemin(_DIR_PLUGIN_SPIP_BONUX."spip21/");

?>
