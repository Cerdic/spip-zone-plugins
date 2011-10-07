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

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Utilisation du pipeline "affiche_gauche" :
 * on affiche un bloc identique a celui du logo de l'auteur avec son gravatar
 * n'a d'effet que dans SPIP < 3 car la page exec a ensuite ete renommee
 *
 * @param  Array $flux  Structure permettant de generer la page
 * @return Array        La structure retournee apres traitement
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


/**
 * Inserer le gravatar de l'auteur qui sera utilise comme #LOGO_AUTEUR par defaut
 * tant que l'auteur n'a pas upload son propre logo
 * Utilise dans SPIP >=3.0.0-dev
 * @param array $flux
 * @return array
 */
function gravatar_recuperer_fond($flux){
	if (test_espace_prive()
	  AND $flux['args']['fond'] == 'formulaires/editer_logo'
	  AND $flux['args']['contexte']['objet']=='auteur'
		AND $id_auteur = $flux['args']['contexte']['id_objet']
		AND strpos($flux['data']['texte'],'spip_logos')==false
	  AND $email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur))){

		include_spip('inc/gravatar');
		if ($gravatar = gravatar_img($email)) {
			$gravatar = extraire_attribut($gravatar,'src');
			$logo = recuperer_fond('formulaires/inc-apercu-logo',array('logo'=>$gravatar,'quoi'=>'logo_on','editable'=>'','titre'=>_T('gravatar:titre_gravatar_auteur')));
			$p = strpos($flux['data']['texte'],'<label');
			$flux['data']['texte'] = substr_replace($flux['data']['texte'],$logo,$p,0);
		}
	}
	return $flux;
}
?>