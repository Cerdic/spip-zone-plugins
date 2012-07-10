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


	function inc_afficher_sondages_minis($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 11), array('arial2'), array('arial1', 40));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_sondages_mini_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png');
	}


	function afficher_sondages_mini_boucle($row, $own) {
		$vals = '';

		$sondage = new sondage($row['id_sondage']);

		switch ($sondage->statut) {
			case 'prepa':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
			case 'publie':
				$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
				break;
		}

		$s = "<div>";
		$s.= "<a href='" . generer_url_ecrire("sondages","id_sondage=".$sondage->id_sondage)."'$dir_lang style=\"display:block;\">";
		$s.= typo($sondage->titre);
		$s.= "</a>";
		$s.= "</div>";
	
		$vals[] = $s;

		$vals[] = "<b>N°".$sondage->id_sondage."</b>";

		return $vals;
	}
	
	
?>