<?php
/**
 * Plugin Rôles
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Ajoute Bootstrap (minimal) aux plugins chargés
 * 
 * @param array $flux
 *     Liste des js chargés
 * @return array
 *     Liste complétée des js chargés
**/
function roles_jquery_plugins($flux) {
	$flux[] = 'javascript/bootstrap-dropdown.js';
	return $flux;
}


/**
 * Ajoute Bootstrap (minimal) aux css chargées
 * 
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function roles_header_prive_css($texte) {

	$css = find_in_path('css/bootstrap-button-dropdown.css');
	$texte .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	return $texte;
}

?>
