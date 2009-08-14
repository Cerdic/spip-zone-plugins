<?php

function omnipresence_affiche_gauche($flux) {
	include_spip('inc/omnipresence');

	if ((_request('exec') == 'auteur_infos')) {
		if ($id_auteur = intval(_request('id_auteur'))
		AND $jid = sql_fetsel(CHAMP_JID, 'spip_auteurs', 'id_auteur='.$id_auteur)
		AND $jid = $jid[CHAMP_JID]) {
			$host = sql_fetsel(CHAMP_SERVEUR_OMNIPRESENCE, 'spip_auteurs', 'id_auteur='.$id_auteur);
			$host = $host[CHAMP_SERVEUR_OMNIPRESENCE];
			if ($avatar_cache = demander_action('avatar', $jid, $host, $url=True)) {
				// On affiche un bloc identique a celui du logo de l'auteur avec son avatar Jabber
				include_spip('inc/filtres_images_mini');
				$img = image_reduire('<img src="'.$avatar_cache.'" alt="" class="miniature_logo" />', 170, 170);
				if ($taille = @getimagesize($avatar_cache))
					$taille = _T('info_largeur_vignette', array('largeur_vignette' => $taille[0], 'hauteur_vignette' => $taille[1]));
				$bouton = bouton_block_depliable(_T('omnipresence:titre_avatar_auteur'), false, "jabber-avatar-$id_auteur");

				$cadre_avatar = '<div id="iconifier-jabber-avatar-'.$id_auteur.'" class="iconifier">';
				$cadre_avatar .= debut_cadre('r', find_in_path('images/bulb-24.png'), '', $bouton, '', '', false);
				$cadre_avatar .= '<div><a href="' . $avatar_cache . '">'. $img . '</a></div>';
				$cadre_avatar .= debut_block_depliable(false,"jabber-avatar-$id_auteur") 
					. '<div class="cadre_padding">'
					. '<div class="spip_xx-small">' . $taille . '</div>' ;
				$message = demander_action('message', $jid, $host);
				if ('' != $message) {
					$cadre_avatar .= '<div class="spip_xx-small"><em>' . demander_action('message', $jid, $host) . '</em></div>';
				}
				$cadre_avatar .= '</div>'
					. fin_block()
					. fin_cadre_relief(true)
					. '</div>';

				$flux['data'] .= $cadre_avatar;
			}
		}
	}

	return $flux;
}
?>
