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
	$listing_objets = array('organisations','contacts','projets','projets_sites','commits');

	if ($flux["args"]["exec"] == "accueil") {
		foreach ($listing_objets as $objet) {
		$flux["data"] .=  recuperer_fond('prive/objets/liste/' . $objet);
		}
	}
    return $flux;
}

?>