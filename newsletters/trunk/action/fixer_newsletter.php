<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/newsletters");
include_spip("newsletters_fonctions");

/**
 * fixer les images dans IMG pour ne pas les perdre meme si on vide les cache des images
 * ou si on supprime un doc joint, un logo etc...
 *
 *
 * @param int|null $id_newsletter
 * @return mixed
 */
function action_fixer_newsletter_dist($id_newsletter = null){
	if (is_null($id_newsletter)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_newsletter = $securiser_action();
	}

	include_spip('inc/autoriser');
	if (autoriser('modifier', 'newsletter', $id_newsletter)){
		$row = sql_fetsel('html_email,html_page,texte_email', 'spip_newsletters', 'id_newsletter=' . intval($id_newsletter));

		// trouver toutes les images dans les 2 versions html
		$images = array();
		foreach (array('html_email','html_page') as $champ){
			preg_match_all('/<img\s[^>]*(src=["\'])([^\'"]*)(["\'])[^>]*>/Uims', $row[$champ], $matches, PREG_SET_ORDER);
			if ($matches AND count($matches)){
				foreach ($matches as $matche){
					$src = $matche[2];
					if (!isset($images[$src])){
						if ($url = newsletter_fixer_image($src,$id_newsletter))
							$images[$src] = url_absolue($url);
					}
					// et remplacer les simples quotes des balises img par doubles quotes au passage
					// car certains outils d'envoi en ligne signalent les simples quote comme une erreur dans leur editeur
					if (strpos($matche[0],"'")!==false){
						$img = preg_replace(',=\'([^\'"]*)\',Uims','="\\1"',$matche[0]);
						if ($img!==$matche[0]){
							$row[$champ] = str_replace($matche[0],$img,$row[$champ]);
						}
					}
				}
			}
		}

		foreach(array_keys($row) as $k){
			// remplacer les urls dans les differentes versions
			$row[$k] = str_replace(array_keys($images),array_values($images),$row[$k]);
		}

		include_spip("action/editer_objet");
		objet_modifier("newsletter",$id_newsletter,$row);
	}
}
