<?php

function havatar_affiche_gauche($flux) {
	if ((_request('exec') == 'auteur_infos')) {
		if ($id_auteur = intval(_request('id_auteur'))
		AND $url = sql_fetsel('url_site', 'spip_auteurs', 'id_auteur='.$id_auteur)
		AND $url = $url['url_site']) {
			include_spip('inc/havatar');
			if ($havatar_cache = havatar($url)) {
				// On affiche un bloc identique a celui du logo de l'auteur avec son havatar
				include_spip('inc/filtres_images_mini');
				$img = image_reduire('<img src="'.$havatar_cache.'" alt="" class="miniature_logo" />', 170, 170);
				if ($taille = @getimagesize($havatar_cache))
					$taille = _T('info_largeur_vignette', array('largeur_vignette' => $taille[0], 'hauteur_vignette' => $taille[1]));
				$bouton = bouton_block_depliable(_T('havatar:titre_havatar_auteur'), false, "havatar-$id_auteur");

				$cadre_havatar = '<div id="iconifier-havatar-' . $id_auteur . '" class="iconifier">';
				$cadre_havatar .= debut_cadre('r', find_in_path('images/havatar-24.gif'), '', $bouton, '', '', false);
				$cadre_havatar .= '<div><a href="' . $havatar_cache . '">'. $img . '</a></div>';
				$cadre_havatar .= debut_block_depliable(false,"havatar-$id_auteur") 
					. '<div class="cadre_padding">'
					. '<div class="spip_xx-small">' . $taille . '</div>' 
					. '</div>'
					. fin_block();
				$cadre_havatar .= fin_cadre_relief(true);
				$cadre_havatar .= '</div>';

				$flux['data'] .= $cadre_havatar;
			}
		}
	}

	return $flux;
}

?>
