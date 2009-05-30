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


	function inc_afficher_sondages($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('sondagesprive:sondages');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 11), array('arial2'), array('arial1', 100), array('arial1', 40));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_sondage_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png');
	}


	function afficher_sondage_boucle($row, $own) {
		global $spip_lang_right;
		
		$vals = '';

		$sondage = new sondage($row['id_sondage']);

		switch ($sondage->statut) {
			case 'prepa':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
			case 'publie':
				$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
				break;
			case 'termine':
				$vals[] = http_img_pack('puce-rouge.gif', 'puce-rouge', ' border="0" style="margin: 1px;"');
				break;
		}

		$s = "<div>";
		$s.= "<a href='" . generer_url_ecrire("sondages","id_sondage=".$sondage->id_sondage)."'$dir_lang style=\"display:block;\">";
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if ($logo = $chercher_logo($sondage->id_sondage, 'id_sondage', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
			if ($logo)
				$s.= "\n<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
		}
		$s.= typo($sondage->titre);
		if (($GLOBALS['meta']['multi_rubriques'] == 'oui') OR ($GLOBALS['meta']['multi_articles'] == 'oui'))
			if ($GLOBALS['auteur_session']['lang'] != $lang)
				$s.= " <font size='1' color='#666666'>(".traduire_nom_langue($sondage->lang).")</font>";
		$s.= "</a>";
		$s.= "</div>";
	
		$vals[] = $s;

		$vals[] = affdate_jourcourt($sondage->date);;

		$vals[] = "<b>N°".$sondage->id_sondage."</b>";

		return $vals;
	}
	
	
?>