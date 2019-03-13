<?php
/**
 * Plugin Rôles
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajoute Bootstrap dropdown aux plugins chargés
 *
 * @param array $flux
 *     Liste des js chargés
 * @return array
 *     Liste complétée des js chargés
**/
function roles_jquery_plugins($flux) {

	$config = lire_config('chosen/active', false);
	if (test_espace_prive() || $config =='oui') {
		$flux[] = 'javascript/bootstrap-dropdown.js';
	}
	return $flux;
}

/**
 * Ajoute Bootstrap dropdown aux css chargées dans le privé
 *
 * @param string $flux Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function roles_header_prive_css($flux) {

	$css = find_in_path('css/roles-dropdown.css');
	$flux .= '<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />' . "\n";

	return $flux;
}


/**
 * Ajoute Bootstrap dropdown aux css chargées dans le public
 *
 * @param string $flux  Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function roles_insert_head_css($flux) {

	$config = lire_config('chosen', array());
	if (isset($config['active']) and $config['active']=='oui') {
		$css = sinon(find_in_path('css/roles-dropdown_public.css'), find_in_path('css/roles-dropdown.css'));
		$flux .= '<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />' . "\n";
	}
	return $flux;
}
