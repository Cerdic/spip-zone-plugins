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


	function inc_afficher_plans($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('lettresprive:plans');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 11), array('arial2'), array('arial1', 50));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_plan_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_PLAN.'/prive/images/plan-24.png');
	}


	function afficher_plan_boucle($row, $own) {
		global $spip_lang_right;
		
		$vals = '';

		$plan = new plan($row['id_plan']);

		switch ($plan->statut) {
			case 'hors_ligne':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
			case 'en_ligne':
				$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
				break;
		}

		$s = "<div>";
		$s.= "<a href='" . generer_url_ecrire("plans","id_plan=".$plan->id_plan) .
			"'$dir_lang style=\"display:block;\">";
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if ($logo = $chercher_logo($plan->id_plan, 'id_plan', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
			if ($logo)
				$s.= "\n<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
		}
		$s.= typo($plan->titre);
		$s.= "</a>";
		$s.= "</div>";
	
		$vals[] = $s;

		$vals[] = "<b>N°".$plan->id_plan."</b>";

		return $vals;
	}


?>