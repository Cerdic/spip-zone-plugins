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


	include_spip('lettres_fonctions');


	function inc_afficher_clics($titre, $requete, $formater) {
		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
		$styles = array(array('', 12), array('arial1'), array('arial1', 30));
		$tableau = array();
		$args = array();
		$presenter_liste = charger_fonction('presenter_liste', 'inc');
		return $presenter_liste($requete, 'afficher_clic_boucle', $tableau, $args, $force, $styles, $tmp_var, $titre, _DIR_PLUGIN_LETTRES.'prive/images/clic.png');
	}


	function afficher_clic_boucle($row, $own) {
		$vals = '';

		$url = $row['url'];
		if (preg_match(',^spip\.php,',$url)) {
			$url = $GLOBALS['meta']['adresse_site'] . '/' . $url;
		}

		$total = $row['total'];
		if ($total == 0) {
			$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
		} else {
			$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
		}

		$url_coupee = str_split($url, 25);

		$vals[] = '<a href="'.$url.'" target="_blank">'.implode(' ', $url_coupee).'</a>';

		$vals[] = '<b>'.$total.'x</b>';

		return $vals;
	}
	
	
?>