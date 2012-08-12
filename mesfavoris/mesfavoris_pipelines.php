<?php
/**
 * Plugin mesfavoris
 * (c) 2009-2012 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */
 
// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head_css
 * 
 * @param string $flux Le contenu CSS du head
 * @param string $flux Le contenu CSS du head modifié
 */
function mesfavoris_insert_head_css($flux){
	$config = "";
	if (isset($GLOBALS['meta']['mesfavoris']))
		$config = unserialize($GLOBALS['meta']['mesfavoris']);
	if ($config AND isset($config['style_formulaire']))
		$config = $config['style_formulaire'];

	if (!$config OR !$css=find_in_path("mesfavoris-$config.css"))
		$css = find_in_path("mesfavoris-32.css");
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".direction_css($css)."' />\n";
	return $flux;
}

?>