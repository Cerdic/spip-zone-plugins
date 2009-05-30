<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function inc_afficher_choix_sondages($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 20), array('arial2', 135), array('arial1', 50), array('arial1', 140), array('arial2', 24), array('arial2', 24), array('arial2', 24));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_choix_sondage_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png');
	}


	function afficher_choix_sondage_boucle($row, $own) {
		global $spip_lang_right;
		
		$vals = '';

		$choix = new choix($row['id_choix']);
		$sondage = new sondage($choix->id_sondage);

		$vals[] = http_img_pack(_DIR_PLUGIN_SONDAGES.'/prive/images/radio.png', 'radio', '');

		$s = '<a href="'.generer_url_ecrire('choix_edit', 'id_sondage='.$sondage->id_sondage.'&id_choix='.$choix->id_choix).'">';
		$s.= typo($choix->titre);
		$s.= '</a>';
		$vals[] = $s;

		$vals[] = $choix->calculer_nb_votes().'&nbsp;'._T('sondages:votes');

		$pourcentage = sondages_calculer_pourcentage($choix->id_sondage, $choix->id_choix);
		$vals[] = '<img src="'._DIR_PLUGIN_SONDAGES.'/prive/images/jauge-vert.gif" alt="pourcentage" title="'.$pourcentage.'%" width="'.$pourcentage.'" height="8" border="0" />&nbsp;'.$pourcentage.'%';

		if ($sondage->statut == 'termine') {
			$vals[] = '&nbsp;';
			$vals[] = '&nbsp;';
			$vals[] = '&nbsp;';
		} else {
			if ($choix->ordre == 0) {
				$s = '&nbsp;';
			} else {
				$s = '<a class="editer_position_choix" href="'.generer_url_action('editer_position_choix', 'id_sondage='.intval($choix->id_sondage).'&id_choix='.intval($choix->id_choix).'&position='.intval($choix->ordre-1), true, true).'">';
				$s.= http_img_pack(_DIR_PLUGIN_SONDAGES.'/prive/images/monter-16.png', 'monter', '');
				$s.= '</a>';
			}
			$vals[] = $s;

			if ($choix->ordre == $sondage->calculer_nb_choix()-1) {
				$s = '&nbsp;';
			} else {
				$s = '<a class="editer_position_choix" href="'.generer_url_action('editer_position_choix', 'id_sondage='.intval($choix->id_sondage).'&id_choix='.intval($choix->id_choix).'&position='.intval($choix->ordre+1), true, true).'">';
				$s.= http_img_pack(_DIR_PLUGIN_SONDAGES.'/prive/images/descendre-16.png', 'descendre', '');
				$s.= '</a>';
			}
			$vals[] = $s;

			$s = '<a class="supprimer_choix" href="'.generer_url_action('supprimer_choix', 'id_sondage='.intval($choix->id_sondage).'&id_choix='.intval($choix->id_choix), true, true).'">';
			$s.= http_img_pack(_DIR_PLUGIN_SONDAGES.'/prive/images/poubelle.png', 'poubelle', '');
			$s.= '</a>';
			$vals[] = $s;
		}

		return $vals;
	}
	
	
?>