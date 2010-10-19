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


	function inc_afficher_lettres_minis($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 11), array('arial2'), array('arial1', 40));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_lettre_mini_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_LETTRES.'prive/images/lettre-24.png');
	}


	function afficher_lettre_mini_boucle($row, $own) {
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

		$s = "<a href='" . generer_url_ecrire("lettres","id_lettre=".$lettre->id_lettre) .
			"'$dir_lang style=\"display:block;\">";
		$s.= typo($lettre->titre);
		$s.= "</a>";
		$vals[] = $s;

		$vals[] = "<b>N°".$lettre->id_lettre."</b>";

		return $vals;
	}
	
	
?>