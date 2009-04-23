<?php


	function inc_afficher_formulaires($titre, $requete, $formater) {
		if ($titre == _T("autres"))
			$titre = _T('formulairesprive:formulaires');
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 12), array('arial2'), array('arial1', 100), array('arial1', 50));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_formulaire_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png');
	}


	function afficher_formulaire_boucle($row, $own) {
	
		global $dir_lang, $spip_lang_right;
		
		$vals = '';

		$id_formulaire	= $row['id_formulaire'];
		$titre			= $row['titre'];
		$date			= $row['date'];
		$statut			= $row['statut'];
		$lang			= $row['lang'];

		switch ($statut) {
			case 'hors_ligne':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
			case 'en_ligne':
				$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
				break;
		}

		$s = "<div>";
		$s .= "<a href='" . generer_url_ecrire("formulaires","id_formulaire=$id_formulaire") .
			"'$dir_lang style=\"display:block;\">";
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if ($logo = $chercher_logo($id_formulaire, 'id_formulaire', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
			if ($logo)
				$s.= "\n<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
		}
		$s .= typo($titre);
		if (($GLOBALS['meta']['multi_rubriques'] == 'oui') OR ($GLOBALS['meta']['multi_articles'] == 'oui'))
			if ($GLOBALS['auteur_session']['lang'] != $lang)
				$s.= " <font size='1' color='#666666'$dir_lang>(".traduire_nom_langue($lang).")</font>";
		$s .= "</a>";
		$s .= "</div>";
	
		$vals[] = $s;

		$vals[] = affdate_jourcourt($date);

		$vals[] = "<b>N°".$id_formulaire."</b>";
	
		return $vals;
	}


?>