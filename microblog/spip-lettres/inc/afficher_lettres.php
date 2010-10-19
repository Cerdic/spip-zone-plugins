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


	include_spip('classes/lettre');


	function inc_afficher_lettres($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('lettresprive:lettres_information');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 11), array('arial2'), array('arial1', 35), array('arial1', 100), array('arial1', 40));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_lettre_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_LETTRES.'prive/images/lettre-24.png');
	}


	function afficher_lettre_boucle($row, $own) {
		global $spip_lang_right;
		
		$vals = '';

		$lettre = new lettre($row['id_lettre']);

		switch ($lettre->statut) {
			case 'brouillon':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
			case 'envoi_en_cours':
				$vals[] = http_img_pack('puce-orange.gif', 'puce-orange', ' border="0" style="margin: 1px;"');
				break;
			case 'envoyee':
				$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
				break;
		}

		// Le titre (et la langue)
		$s = "<div>";
		$s.= "<a href='" . generer_url_ecrire("lettres","id_lettre=".$lettre->id_lettre) .
			"'$dir_lang style=\"display:block;\">";
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if ($logo = $chercher_logo($lettre->id_lettre, 'id_lettre', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
			if ($logo)
				$s.= "\n<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
		}
		$s.= typo($lettre->titre);
		if (($GLOBALS['meta']['multi_rubriques'] == 'oui') OR ($GLOBALS['meta']['multi_articles'] == 'oui'))
			if ($GLOBALS['visiteur_session']['lang'] != $lettre->lang)
				$s.= " <font size='1' color='#666666'$dir_lang>(".traduire_nom_langue($lettre->lang).")</font>";
		$s.= "</a>";
		$s.= "</div>";
	
		$vals[] = $s;

		if ($lettre->statut == 'envoyee' and $lettre->calculer_taux_ouverture())
			$vals[] = $lettre->calculer_taux_ouverture().'%';
		else
			$vals[] = '&nbsp;';

		// La date
		switch ($lettre->statut) {
			case 'brouillon':
				$d = affdate_jourcourt($lettre->date);
				break;
			case 'envoi_en_cours':
				$d = affdate_jourcourt($lettre->date_debut_envoi);
				break;
			case 'envoyee':
				$d = affdate_jourcourt($lettre->date_fin_envoi);
				break;
		}
		$vals[] = $d;
		$vals[] = "<b>N°".$lettre->id_lettre."</b>";

		return $vals;
	}
	
	
?>