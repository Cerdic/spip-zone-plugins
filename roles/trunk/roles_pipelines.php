<?php
/**
 * Plugin Rôles
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Ajoute Chosen & bootstrap (minimal) aux plugins chargés
 * 
 * @param array $flux
 *     Liste des js chargés
 * @return array
 *     Liste complétée des js chargés
**/
function roles_jquery_plugins($flux) {
	$flux[] = 'lib/chosen/chosen.jquery.js';
	$flux[] = 'javascript/bootstrap-dropdown.js';
	$flux[] = 'javascript/roles.js';
	return $flux;
}


/**
 * Ajoute Chosen & bootstrap (minimal) aux css chargées
 * 
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function roles_header_prive_css($texte) {

	$css = find_in_path('lib/chosen/chosen.css');
	$texte .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	$css = find_in_path('css/bootstrap-button-dropdown.css');
	$texte .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	return $texte;
}

?>
