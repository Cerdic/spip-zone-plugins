<?php

/**
 *
 * Gravatar : Globally Recognized AVATAR
 *
 * @package     plugins
 * @subpackage  gravatar
 *
 * @author      Fil, Cedric, Thomas Beaumanoir
 * @license     GNU/GPL
 *
 * @version     $Id$
 **/

/**
 * Utilisation du pipeline "affiche_gauche" :
 * on affiche un bloc identique a celui du logo de l'auteur avec son gravatar
 *
 * @param  Array $flux # structure permettant de generer la page
 * @return Array       # retournee apres traitement
 */
function gravatar_affiche_gauche($flux) {
	if ((_request('exec') == 'auteur_infos')) {
		if ($id_auteur = intval(_request('id_auteur'))
		AND $email = sql_fetsel('email', 'spip_auteurs', 'id_auteur='.$id_auteur)
		AND $email = $email['email']) {
			include_spip('inc/gravatar');
			if ($gravatar_cache = gravatar($email)) {
				// On affiche un bloc identique a celui du logo de l'auteur avec son gravatar
				include_spip('inc/filtres_images_mini');
				$img = image_reduire('<img src="'.$gravatar_cache.'" alt="" class="miniature_logo" />', 170, 170);
				if ($taille = @getimagesize($gravatar_cache))
					$taille = _T('info_largeur_vignette', array('largeur_vignette' => $taille[0], 'hauteur_vignette' => $taille[1]));
				$bouton = bouton_block_depliable(_T('gravatar:titre_gravatar_auteur'), false, "gravatar-$id_auteur");

				$cadre_gravatar = '<div id="iconifier-gravatar-' . $id_auteur . '" class="iconifier">';
				$cadre_gravatar .= debut_cadre('r', find_in_path('images/gravatar-24.gif'), '', $bouton, '', '', false);
				$cadre_gravatar .= '<div><a href="' . $gravatar_cache . '">'. $img . '</a></div>';
				$cadre_gravatar .= debut_block_depliable(false,"gravatar-$id_auteur") 
					. '<div class="cadre_padding">'
					. '<div class="spip_xx-small">' . $taille . '</div>' 
					. '</div>'
					. fin_block();
				$cadre_gravatar .= fin_cadre_relief(true);
				$cadre_gravatar .= '</div>';

				$flux['data'] .= $cadre_gravatar;
			}
		}
	}

	return $flux;
}

?>