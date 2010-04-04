<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function inc_afficher_abonnements($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 12), array('arial2'), array('arial1', 120), array('arial1', 40));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_abonnement_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_LETTRES.'prive/images/rubrique-24.png');
	}


	function afficher_abonnement_boucle($row, $own) {
		$vals = '';

		$id_rubrique = $row['id_rubrique'];
		$total = $row['total'];

		$vals[] = http_img_pack(_DIR_PLUGIN_LETTRES.'prive/images/rubrique-12.png', "rub", '');

		$s = "<a href='".generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique)."'$dir_lang style=\"display:block;\">";
		if ($id_rubrique == 0) {
			$s.= _T('lettresprive:racine_du_site');
		} else {
			$s.= typo(sql_getfetsel('titre', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique)));
		}
		$s.= "</a>";
		$vals[] = $s;

		if ($total) {
			$s = $total.' '._T('lettresprive:abonnes');
		} else {
			$s = '&nbsp;';
		}
		$vals[] = $s;

		if ($options == "avancees") {
			$vals[] = "<b>"._T('info_numero_abbreviation').$id_rubrique."</b>";
		}

		return $vals;
	}
	
	
?>