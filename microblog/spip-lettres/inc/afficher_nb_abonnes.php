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


	function inc_afficher_nb_abonnes($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 12), array('arial2'), array('arial2', 30));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_nb_abonne_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_LETTRES.'prive/images/statistiques.png');
	}


	function afficher_nb_abonne_boucle($row, $own) {
		$vals = '';

		$periode = $row['periode'];
		$total = $row['nb_inscriptions'] - $row['nb_desinscriptions'];

		if ($total == 0) {
			$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
		} else if ($total > 0) {
			$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
		} else {
			$vals[] = http_img_pack('puce-rouge.gif', 'puce-rouge', ' border="0" style="margin: 1px;"');
		}

		list($annee, $mois) = explode('-', $periode);
		$vals[] = ucwords(_T('date_mois_'.intval($mois))).' '.$annee;

		$vals[] = $total;

		return $vals;
	}
	
	
?>