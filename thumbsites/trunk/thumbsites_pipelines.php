<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Boite de configuration des objets.
 *
 * @param array $flux
 *
 * @return array
 */
function thumbsites_afficher_config_objet($flux)
{
	$type = $flux['args']['type'];
	if (($type == 'site')
	and ($id = intval($flux['args']['id']))
	and ($url = sql_getfetsel('url_site', 'spip_syndic', 'id_syndic='.sql_quote($id)))) {
		include_spip('inc/thumbsites_filtres');
		if ($thumbshot_cache = thumbshot($url)) {
			if ($taille = @getimagesize($thumbshot_cache)) {
				$flux['data'] .= recuperer_fond('prive/squelettes/navigation/thumbshot',
				array(
					'id_objet' => $id,
					'objet' => objet_type(table_objet($type)),
					'thumbshot_cache' => $thumbshot_cache,
					'largeur' => $taille[0],
					'hauteur' => $taille[1],
					'url' => $url,
				));
			}
		}
	}

	return $flux;
}
