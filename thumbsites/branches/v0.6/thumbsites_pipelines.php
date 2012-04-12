<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function thumbsites_affiche_gauche($flux) {
	if ((_request('exec') == 'sites')) {
		if ($id_syndic = intval(_request('id_syndic'))
		AND $row = sql_fetsel('url_site', 'spip_syndic', 'id_syndic='.$id_syndic)
		AND $url = $row['url_site']) {
			include_spip('inc/thumbsites_filtres');
			if ($thumbshot_cache = thumbshot($url)) {
				// On affiche un bloc identique a celui du logo du site
				if ($taille = @getimagesize($thumbshot_cache))
					$taille = _T('info_largeur_vignette', array('largeur_vignette' => $taille[0], 'hauteur_vignette' => $taille[1]));

				$flux["data"] .= recuperer_fond('prive/squelettes/contenu/thumbsites_affiche_boite',array(
					'thumbshot_cache' => $thumbshot_cache,
					'taille' => $taille,
					'id_syndic' => $id_syndic,
					'url' => $url
					));
			}
		}
	}

	return $flux;
}

?>