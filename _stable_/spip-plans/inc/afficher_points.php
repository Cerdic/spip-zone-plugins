<?php


	/**
	 * SPIP-Plans
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('inc/plans_classes');


	function inc_afficher_points($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 16), array('arial2', 150), array('arial1', 16), array('arial1', 100), array('arial1', 50), array('arial1', 50), array('arial1', 16), array('arial1', 16), array('arial1', 16));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_point_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_PLAN.'/prive/images/point-24.png');
	}


	function afficher_point_boucle($row, $own) {
		global $spip_lang_right;
		
		$vals = '';

		$plan = new plan($row['id_plan']);
		$point = new point($row['id_plan'], $row['id_point']);

		$vals[] = http_img_pack(_DIR_PLUGIN_PLAN.'/prive/images/point-16.png', 'puce-verte', ' border="0" style="margin: 1px;"');

		$s = "<div>";
		$s.= "<a href='" . generer_url_ecrire('points_edit', 'id_plan='.$point->id_plan.'&id_point='.$point->id_point)."'$dir_lang style=\"display:block;\">";
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if ($logo = $chercher_logo($point->id_point, 'id_point', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
			if ($logo)
				$s.= "\n<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
		}
		$s.= typo($point->titre);
		$s.= "</a>";
		$s.= "</div>";
		$vals[] = $s;

	 	$vals[] = '&nbsp;';
	
		$vals[] = couper($point->lien, 10, '...');

		$vals[] = $point->abscisse;

		$vals[] = $point->ordonnee;

		if ($point->z_index == 0) {
			$s = '&nbsp;';
		} else {
			$s = '<a href="'.generer_url_action('editer_position_point', 'id_plan='.intval($point->id_plan).'&id_point='.intval($point->id_point).'&z_index='.intval($point->z_index-1), true, true).'">';
			$s.= http_img_pack(_DIR_PLUGIN_PLAN.'/prive/images/monter-16.png', 'monter', '');
			$s.= '</a>';
		}
		$vals[] = $s;

		if ($point->z_index == $plan->calculer_nb_points()-1) {
			$s = '&nbsp;';
		} else {
			$s = '<a href="'.generer_url_action('editer_position_point', 'id_plan='.intval($point->id_plan).'&id_point='.intval($point->id_point).'&z_index='.intval($point->z_index+1), true, true).'">';
			$s.= http_img_pack(_DIR_PLUGIN_PLAN.'/prive/images/descendre-16.png', 'descendre', '');
			$s.= '</a>';
		}
		$vals[] = $s;

		$s = '<a href="'.generer_url_action('supprimer_point', 'id_plan='.intval($point->id_plan).'&id_point='.intval($point->id_point), true, true).'">';
		$s.= http_img_pack(_DIR_PLUGIN_PLAN.'/prive/images/poubelle.png', 'poubelle', '');
		$s.= '</a>';
		$vals[] = $s;

		return $vals;
	}


?>