<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction retournant l'article du manuel enregistre dans la config
 * si on doit le cacher
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function manuelsite_article_si_cacher() {
	$conf_manuelsite = lire_config('manuelsite');
	if (!test_espace_prive() && $conf_manuelsite["cacher_public"] && $id=$conf_manuelsite["id_article"]) {
		return($id);
	}
	return 0;
}
?>