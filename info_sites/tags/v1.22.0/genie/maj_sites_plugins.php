<?php
/**
 * Rechercher par les webservices les plugins à mettre à jour sur les sites
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Genie
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_maj_sites_plugins_dist($t) {
	include_spip('inc/flock');
	include_spip('info_sites_fonctions');
	$sites_projets_maj_plugins = sites_projets_maj_plugins();
	if (is_array($sites_projets_maj_plugins) and count($sites_projets_maj_plugins) > 0) {
		$serialize = serialize($sites_projets_maj_plugins);
		ecrire_fichier(_FICHIER_MAJ_PLUGINS, $serialize);
	}

	return $t;
}

?>
