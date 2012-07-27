<?php
/**
 * Plugin Chosen
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajoute Chosen aux plugins JS chargés
 * 
 * @param array $flux
 *     Liste des js chargés
 * @return array
 *     Liste complétée des js chargés
**/
function chosen_jquery_plugins($flux) {
	$flux[] = 'lib/chosen/chosen.jquery.js'; # lib originale
	$flux[] = 'javascript/spip_chosen.js';    # chargements SPIP automatiques
	return $flux;
}


/**
 * Ajoute Chosen aux css chargées
 * 
 * @param string $texte Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function chosen_header_prive_css($texte) {

	$css = find_in_path('lib/chosen/chosen.css');
	$texte .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	return $texte;
}

?>
