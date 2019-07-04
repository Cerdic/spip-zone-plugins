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
        $ret .= recuperer_fond("prive/squelettes/inclure/blocs_exclus");
		$flux["data"] .= $ret;
	}
	if ($exec == "configurer_identite") {
		$page = $flux["args"]["page"];
        $ret .= recuperer_fond("prive/squelettes/inclure/contact_site");
		$flux["data"] .= $ret;
	}
 
	return $flux;
}
