<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline noizetier_lister_pages pour modifier les blocs des pages page-login et page-spip_pass
 *
 * @param array $pages
 * @return array
 */
function aveline_noizetier_lister_pages($pages){
	$blocs = array_merge(
		array('avantcontenu' => array(
			'nom' => _T('aveline:nom_bloc_avantcontenu'),
			'description' => _T('aveline:description_bloc_avantcontenu'),
			'icon' => find_in_path('img/ic_bloc_avantcontenu.png')
			)
		),
		noizetier_blocs_defaut()
	);
	$pages['page-login']['blocs'] = $blocs;
	$pages['page-spip_pass']['blocs'] = $blocs;
	
	return $pages;
}




?>
