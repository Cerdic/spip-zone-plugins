<?php
/**
 * Utilisations de pipelines par SPIPr-Dane-Noisettes
 *
 * @plugin     SPIPr-Dane-Noisettes
 * @copyright  2019
 * @author     Dominique Lepaisant
 * @licence    GNU/GPL
 * @package    SPIP\Sdn\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Affichage du formulaire de selection du layer dans les pages du noizetier
 *
 * @param array $flux
 * @return array
 */
function sdn_affiche_milieu($flux) {
	$exec = $flux["args"]["exec"];
 
	if ($exec == "noizetier_page" || $exec == "noizetier_pages") {
		$page = $flux["args"]["page"];
		$contexte = array('page'=>$page?$page:"defaut");
		$ret .= recuperer_fond("prive/squelettes/inclure/selection_layer_page_interface", $contexte);
		$flux["data"] .= $ret;
	}
	return $flux;
}

/**
 * Insertion dans le head du prive
 *
 * @param array $flux
 * @return array
 */
function sdn_header_prive($flux) { 
 		$flux .= '<script src="' . find_in_path('js/sdn_prive.js') . '" type="text/javascript"></script>' . "\n";	

	return $flux;
}
