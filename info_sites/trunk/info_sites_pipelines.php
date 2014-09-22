<?php
/**
 * Définit les pipelines du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function info_sites_affiche_milieu($flux)
{
	$liste_objets = array('organisations','contacts','projets','projets_sites');

	$liste_plugins = isset($GLOBALS['meta']['plugin'])? unserialize($GLOBALS['meta']['plugin']) : array();

	// rss_commits étant facultatif, on regarde s'il est actif.
	if (in_array('rss_commits', $liste_plugins)) {
		$liste_objets[] = 'commits';
	}
	if ($flux["args"]["exec"] == "accueil") {
		foreach ($liste_objets as $objet) {
		$flux["data"] .=  recuperer_fond('prive/objets/liste/' . $objet);
		}
	}
    return $flux;
}

?>