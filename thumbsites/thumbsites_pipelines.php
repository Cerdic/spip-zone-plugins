<?php

function thumbsites_affiche_gauche($flux) {
	if ((_request('exec') == 'sites')) {
		if ($id_syndic = intval(_request('id_syndic'))
		AND $row = sql_fetsel('url_site', 'spip_syndic', 'id_syndic='.$id_syndic)
		AND $url = $row['url_site']) {
			include_spip('inc/thumbsites_filtres');
			if ($thumbshot_cache = thumbshot($url)) {
				// On affiche un bloc identique a celui du logo du site
				include_spip('inc/filtres_images_mini');
				$img = image_reduire('<img src="'.$thumbshot_cache.'" alt="" class="miniature_logo" />', 170, 170);
				if ($taille = @getimagesize($thumbshot_cache))
					$taille = _T('info_largeur_vignette', array('largeur_vignette' => $taille[0], 'hauteur_vignette' => $taille[1]));
				$bouton = bouton_block_depliable(_T('thumbsites:titre_thumbshot_site'), false, "thumbshot-$id_syndic");

				$cadre = '<div id="iconifier-thumbshot-' . $id_syndic . '" class="iconifier">';
				$cadre .= debut_cadre('r', find_in_path('prive/themes/spip/images/thumbsites-24.png'), '', $bouton, '', '', false);
				$cadre .= '<div><a href="' . $thumbshot_cache . '">'. $img . '</a></div>';
				$cadre .= debut_block_depliable(false,"thumbshot-$id_syndic") 
					. '<div class="cadre_padding">'
					. '<div class="spip_xx-small">' . $taille . '</div>' 
					. '</div>'
					. fin_block();
				$cadre .= fin_cadre_relief(true);
				$cadre .= '</div>';

				$flux['data'] .= $cadre;
			}
		}
	}

	return $flux;
}

?>